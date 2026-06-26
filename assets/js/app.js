/**
 * Movie Night QR Ticket System - Main JavaScript
 */

// Configuration
const CONFIG = {
  apiBase: '/api/',
  pollInterval: 2000,
  maxFileSize: 5 * 1024 * 1024 // 5MB
};

// Utility Functions
const Utils = {
  // Format currency
  formatCurrency: (amount) => {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD'
    }).format(amount);
  },

  // Format date
  formatDate: (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
  },

  // Format time
  formatTime: (timeString) => {
    const [hours, minutes] = timeString.split(':');
    const date = new Date(2000, 0, 1, hours, minutes);
    return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
  },

  // Validate email
  validateEmail: (email) => {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  },

  // Validate phone
  validatePhone: (phone) => {
    const digits = phone.replace(/\D/g, '');
    return digits.length >= 10 && digits.length <= 15;
  },

  // Show notification
  notify: (message, type = 'info', duration = 5000) => {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
      <span>${message}</span>
      <button onclick="this.parentElement.remove()" style="background: none; border: none; color: inherit; cursor: pointer;">&times;</button>
    `;
    
    const container = document.querySelector('.container') || document.body;
    container.insertBefore(alertDiv, container.firstChild);
    
    if (duration > 0) {
      setTimeout(() => alertDiv.remove(), duration);
    }
  },

  // Play sound
  playSound: (type = 'success') => {
    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
    const oscillator = audioContext.createOscillator();
    const gainNode = audioContext.createGain();
    
    oscillator.connect(gainNode);
    gainNode.connect(audioContext.destination);
    
    if (type === 'success') {
      oscillator.frequency.value = 800;
      gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.2);
      oscillator.start(audioContext.currentTime);
      oscillator.stop(audioContext.currentTime + 0.2);
    } else if (type === 'error') {
      oscillator.frequency.value = 300;
      gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
      gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3);
      oscillator.start(audioContext.currentTime);
      oscillator.stop(audioContext.currentTime + 0.3);
    }
  },

  // API request
  apiRequest: async (endpoint, method = 'GET', data = null) => {
    const options = {
      method,
      headers: {
        'Content-Type': 'application/json'
      }
    };
    
    if (data) {
      options.body = JSON.stringify(data);
    }
    
    try {
      const response = await fetch(CONFIG.apiBase + endpoint, options);
      return await response.json();
    } catch (error) {
      console.error('API Error:', error);
      return { success: false, message: 'API request failed' };
    }
  },

  // Show loading state
  setLoading: (element, isLoading = true) => {
    if (isLoading) {
      element.disabled = true;
      element.innerHTML = '<span class="spinner"></span> Loading...';
    } else {
      element.disabled = false;
      element.innerHTML = element.getAttribute('data-original-text') || 'Submit';
    }
  }
};

// Form Handling
const Forms = {
  // Handle ticket purchase
  handlePurchase: async (event) => {
    event.preventDefault();
    
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.setAttribute('data-original-text', submitBtn.innerHTML);
    
    // Validate form
    const name = form.querySelector('#name').value.trim();
    const phone = form.querySelector('#phone').value.trim();
    const email = form.querySelector('#email')?.value.trim() || '';
    
    if (!name || name.length < 2) {
      Utils.notify('Please enter a valid name', 'error');
      return;
    }
    
    if (!Utils.validatePhone(phone)) {
      Utils.notify('Please enter a valid phone number', 'error');
      return;
    }
    
    if (email && !Utils.validateEmail(email)) {
      Utils.notify('Please enter a valid email address', 'error');
      return;
    }
    
    // Submit
    Utils.setLoading(submitBtn, true);
    
    const result = await Utils.apiRequest('purchase.php', 'POST', {
      name,
      phone,
      email
    });
    
    Utils.setLoading(submitBtn, false);
    
    if (result.success) {
      Utils.notify('Ticket purchased successfully!', 'success');
      // Store ticket data in sessionStorage for the next page
      sessionStorage.setItem('ticketData', JSON.stringify(result));
      // Redirect to ticket page
      setTimeout(() => {
        window.location.href = 'ticket.php';
      }, 1500);
    } else {
      Utils.notify(result.message || 'Failed to purchase ticket', 'error');
    }
  },

  // Handle admin login
  handleAdminLogin: async (event) => {
    event.preventDefault();
    
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    submitBtn.setAttribute('data-original-text', submitBtn.innerHTML);
    
    const username = form.querySelector('#username').value.trim();
    const password = form.querySelector('#password').value;
    
    if (!username || !password) {
      Utils.notify('Please enter username and password', 'error');
      return;
    }
    
    Utils.setLoading(submitBtn, true);
    
    const result = await Utils.apiRequest('admin/login.php', 'POST', {
      username,
      password
    });
    
    Utils.setLoading(submitBtn, false);
    
    if (result.success) {
      Utils.notify('Login successful!', 'success');
      setTimeout(() => {
        window.location.href = 'dashboard.php';
      }, 1500);
    } else {
      Utils.notify(result.message || 'Invalid credentials', 'error');
    }
  }
};

// Scanner Functionality
const Scanner = {
  isScanning: false,
  lastScannedValue: '',

  // Initialize QR scanner
  init: async () => {
    const qrReaderElement = document.getElementById('qr-reader');
    if (!qrReaderElement) return;
    
    // Check if html5-qrcode library is loaded
    if (typeof Html5Qrcode === 'undefined') {
      console.error('html5-qrcode library not loaded');
      Utils.notify('QR Code library not loaded', 'error');
      return;
    }
    
    try {
      const html5QrCode = new Html5Qrcode('qr-reader');
      
      const config = {
        fps: 10,
        qrbox: { width: 250, height: 250 },
        aspectRatio: 1.77778
      };
      
      await html5QrCode.start(
        { facingMode: 'environment' },
        config,
        (decodedText) => Scanner.handleScan(decodedText),
        (errorMessage) => {
          // Silently ignore errors
        }
      );
      
      Scanner.isScanning = true;
      Utils.notify('Scanner ready - point camera at QR code', 'info', 3000);
    } catch (error) {
      console.error('Scanner error:', error);
      Utils.notify('Failed to start camera. Please check permissions.', 'error');
    }
  },

  // Handle QR code scan
  handleScan: async (qrValue) => {
    // Prevent duplicate scans
    if (qrValue === Scanner.lastScannedValue) return;
    Scanner.lastScannedValue = qrValue;
    
    // Verify ticket
    const result = await Utils.apiRequest('scan.php', 'POST', { qr_value: qrValue });
    
    const resultDiv = document.getElementById('scan-result');
    
    if (result.success) {
      Utils.playSound('success');
      resultDiv.className = 'scan-result success';
      resultDiv.innerHTML = `
        <div class="result-icon">✅</div>
        <div class="result-title">VALID TICKET</div>
        <div class="result-details">
          <div class="result-row">
            <span>Name:</span>
            <strong>${result.ticket.name}</strong>
          </div>
          <div class="result-row">
            <span>Ticket ID:</span>
            <strong>${result.ticket.ticket_id}</strong>
          </div>
          <div class="result-row">
            <span>Entry Time:</span>
            <strong>${new Date().toLocaleTimeString()}</strong>
          </div>
        </div>
      `;
    } else if (result.used) {
      Utils.playSound('error');
      resultDiv.className = 'scan-result error';
      resultDiv.innerHTML = `
        <div class="result-icon">❌</div>
        <div class="result-title">TICKET ALREADY USED</div>
        <div class="result-details">
          <div class="result-row">
            <span>Name:</span>
            <strong>${result.ticket.name}</strong>
          </div>
          <div class="result-row">
            <span>Ticket ID:</span>
            <strong>${result.ticket.ticket_id}</strong>
          </div>
          <div class="result-row">
            <span>First Scan Time:</span>
            <strong>${Utils.formatDate(result.ticket.used_at)}</strong>
          </div>
        </div>
      `;
    } else {
      Utils.playSound('error');
      resultDiv.className = 'scan-result error';
      resultDiv.innerHTML = `
        <div class="result-icon">❌</div>
        <div class="result-title">INVALID TICKET</div>
        <p>No valid ticket found. Please try again.</p>
      `;
    }
    
    resultDiv.style.display = 'block';
    
    // Reset scan state after 2 seconds
    setTimeout(() => {
      Scanner.lastScannedValue = '';
    }, 2000);
  },

  // Stop scanner
  stop: async () => {
    if (Scanner.isScanning) {
      try {
        await Html5Qrcode.getCameras();
        Scanner.isScanning = false;
      } catch (error) {
        console.error('Error stopping scanner:', error);
      }
    }
  }
};

// Ticket Display
const Ticket = {
  // Display ticket
  display: () => {
    const ticketData = sessionStorage.getItem('ticketData');
    if (!ticketData) {
      Utils.notify('No ticket data found', 'error');
      return;
    }
    
    const data = JSON.parse(ticketData);
    const ticketContainer = document.getElementById('ticket-container');
    
    if (ticketContainer) {
      // Populate ticket data
      document.getElementById('ticket-id').textContent = data.ticket_id;
      document.getElementById('ticket-qr').src = 'data:image/png;base64,' + data.qr_code;
      document.getElementById('ticket-name').textContent = data.name;
      // ... populate other fields
    }
  },

  // Download ticket as PNG
  download: async () => {
    const ticketCard = document.querySelector('.ticket-card');
    if (!ticketCard) return;
    
    const btn = event.target;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner"></span> Generating...';
    
    try {
      // Use html2canvas to capture the ticket card
      if (typeof html2canvas === 'undefined') {
        Utils.notify('Canvas library not loaded', 'error');
        return;
      }
      
      const canvas = await html2canvas(ticketCard, {
        scale: 2,
        backgroundColor: '#1a1a2e'
      });
      
      const link = document.createElement('a');
      link.href = canvas.toDataURL('image/png');
      link.download = 'movie-night-ticket.png';
      link.click();
      
      Utils.notify('Ticket downloaded successfully!', 'success', 3000);
    } catch (error) {
      console.error('Download error:', error);
      Utils.notify('Failed to download ticket', 'error');
    } finally {
      btn.disabled = false;
      btn.innerHTML = 'Download as PNG';
    }
  }
};

// Initialize on page load
document.addEventListener('DOMContentLoaded', () => {
  // Attach form handlers
  const purchaseForm = document.getElementById('purchase-form');
  if (purchaseForm) {
    purchaseForm.addEventListener('submit', Forms.handlePurchase);
  }
  
  const loginForm = document.getElementById('admin-login-form');
  if (loginForm) {
    loginForm.addEventListener('submit', Forms.handleAdminLogin);
  }
  
  // Initialize scanner if needed
  const scannerPage = document.getElementById('scanner-page');
  if (scannerPage) {
    Scanner.init();
  }
  
  // Display ticket if on ticket page
  const ticketPage = document.getElementById('ticket-page');
  if (ticketPage) {
    Ticket.display();
  }
});

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
  Scanner.stop();
});
