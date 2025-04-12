/**
 * Modern Medical Management System
 * Enhanced JavaScript functionality
 * Created by Claude AI Assistant
 * Version 1.0
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
  // Initialize all components
  initializeToasts();
  initializeAnimations();
  initializeDropdowns();
  initializeModals();
  initializeFormValidation();
  initializeDataTables();
  initializeCharts();
  initializeDarkMode();
});

/**
 * Toast Notification System
 */
const toastSystem = {
  container: null,
  
  // Initialize the toast container
  init: function() {
    // Create toast container if it doesn't exist
    if (!document.querySelector('.toast-container')) {
      this.container = document.createElement('div');
      this.container.className = 'toast-container';
      document.body.appendChild(this.container);
    } else {
      this.container = document.querySelector('.toast-container');
    }
  },
  
  // Show a toast notification
  show: function(message, type = 'info', duration = 3000) {
    if (!this.container) this.init();
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Create toast content
    toast.innerHTML = `
      <div class="toast-header">
        <strong class="mr-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
        <button type="button" class="btn-close" aria-label="Close"></button>
      </div>
      <div class="toast-body">${message}</div>
    `;
    
    // Add to container
    this.container.appendChild(toast);
    
    // Add close button functionality
    const closeBtn = toast.querySelector('.btn-close');
    closeBtn.addEventListener('click', () => {
      this.hide(toast);
    });
    
    // Auto-hide after duration
    setTimeout(() => {
      this.hide(toast);
    }, duration);
    
    return toast;
  },
  
  // Hide a toast notification
  hide: function(toast) {
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-20px)';
    
    setTimeout(() => {
      if (toast.parentNode) {
        toast.parentNode.removeChild(toast);
      }
    }, 300);
  },
  
  // Shorthand methods for different toast types
  success: function(message, duration) {
    return this.show(message, 'success', duration);
  },
  
  error: function(message, duration) {
    return this.show(message, 'danger', duration);
  },
  
  warning: function(message, duration) {
    return this.show(message, 'warning', duration);
  },
  
  info: function(message, duration) {
    return this.show(message, 'info', duration);
  }
};

/**
 * Initialize toast notifications
 */
function initializeToasts() {
  toastSystem.init();
  
  // Add global toast function
  window.showToast = function(message, type, duration) {
    return toastSystem.show(message, type, duration);
  };
  
  // Add shorthand methods to global scope
  window.showSuccessToast = function(message, duration) {
    return toastSystem.success(message, duration);
  };
  
  window.showErrorToast = function(message, duration) {
    return toastSystem.error(message, duration);
  };
  
  window.showWarningToast = function(message, duration) {
    return toastSystem.warning(message, duration);
  };
  
  window.showInfoToast = function(message, duration) {
    return toastSystem.info(message, duration);
  };
}

/**
 * Initialize animations for page elements
 */
function initializeAnimations() {
  // Add entrance animations to elements with data-animate attribute
  const animatedElements = document.querySelectorAll('[data-animate]');
  
  animatedElements.forEach((element, index) => {
    const animationType = element.getAttribute('data-animate');
    const delay = element.getAttribute('data-delay') || index * 100;
    
    // Add animation class after a delay
    setTimeout(() => {
      element.classList.add(`animate-${animationType}`);
      element.style.opacity = '1';
    }, delay);
  });
  
  // Initialize intersection observer for scroll animations
  if ('IntersectionObserver' in window) {
    const scrollAnimElements = document.querySelectorAll('[data-scroll-animate]');
    
    const scrollObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const element = entry.target;
          const animationType = element.getAttribute('data-scroll-animate');
          
          element.classList.add(`animate-${animationType}`);
          element.style.opacity = '1';
          
          // Stop observing after animation is triggered
          scrollObserver.unobserve(element);
        }
      });
    }, { threshold: 0.1 });
    
    scrollAnimElements.forEach(element => {
      element.style.opacity = '0';
      scrollObserver.observe(element);
    });
  }
}

/**
 * Initialize dropdown functionality
 */
function initializeDropdowns() {
  const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
  
  dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      
      const dropdown = this.nextElementSibling;
      
      // Close all other dropdowns
      document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
        if (menu !== dropdown) {
          menu.classList.remove('show');
        }
      });
      
      // Toggle current dropdown
      dropdown.classList.toggle('show');
    });
  });
  
  // Close dropdowns when clicking outside
  document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
      document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
        menu.classList.remove('show');
      });
    }
  });
}

/**
 * Initialize modal functionality
 */
function initializeModals() {
  // Modal open buttons
  const modalTriggers = document.querySelectorAll('[data-toggle="modal"]');
  
  modalTriggers.forEach(trigger => {
    trigger.addEventListener('click', function(e) {
      e.preventDefault();
      
      const targetId = this.getAttribute('data-target');
      const modal = document.querySelector(targetId);
      
      if (modal) {
        openModal(modal);
      }
    });
  });
  
  // Modal close buttons
  const modalCloseButtons = document.querySelectorAll('[data-dismiss="modal"]');
  
  modalCloseButtons.forEach(button => {
    button.addEventListener('click', function() {
      const modal = this.closest('.modal');
      closeModal(modal);
    });
  });
  
  // Close modal when clicking on backdrop
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('modal')) {
      closeModal(e.target);
    }
  });
  
  // Close modal with Escape key
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      const openModal = document.querySelector('.modal.show');
      if (openModal) {
        closeModal(openModal);
      }
    }
  });
}

// Helper function to open a modal
function openModal(modal) {
  document.body.classList.add('modal-open');
  modal.style.display = 'block';
  
  setTimeout(() => {
    modal.classList.add('show');
  }, 10);
}

// Helper function to close a modal
function closeModal(modal) {
  modal.classList.remove('show');
  
  setTimeout(() => {
    modal.style.display = 'none';
    document.body.classList.remove('modal-open');
  }, 300);
}

/**
 * Initialize form validation
 */
function initializeFormValidation() {
  const forms = document.querySelectorAll('.needs-validation');
  
  forms.forEach(form => {
    form.addEventListener('submit', function(event) {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      
      form.classList.add('was-validated');
    }, false);
  });
}

/**
 * Initialize DataTables if the library is available
 */
function initializeDataTables() {
  if (typeof $.fn.DataTable !== 'undefined') {
    $('.data-table').DataTable({
      responsive: true,
      language: {
        search: "_INPUT_",
        searchPlaceholder: "Search...",
        lengthMenu: "Show _MENU_ entries",
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "Showing 0 to 0 of 0 entries",
        infoFiltered: "(filtered from _MAX_ total entries)"
      }
    });
  }
}

/**
 * Initialize Charts if Chart.js is available
 */
function initializeCharts() {
  if (typeof Chart !== 'undefined') {
    // Set default chart options
    Chart.defaults.color = '#6b7280';
    Chart.defaults.font.family = "'Inter', sans-serif";
    
    // Initialize charts with data-chart attribute
    const chartElements = document.querySelectorAll('[data-chart]');
    
    chartElements.forEach(element => {
      const chartType = element.getAttribute('data-chart');
      const chartData = JSON.parse(element.getAttribute('data-chart-data') || '{}');
      const chartOptions = JSON.parse(element.getAttribute('data-chart-options') || '{}');
      
      new Chart(element, {
        type: chartType,
        data: chartData,
        options: chartOptions
      });
    });
  }
}

/**
 * Initialize Dark Mode toggle
 */
function initializeDarkMode() {
  const darkModeToggle = document.getElementById('darkModeToggle');
  
  if (darkModeToggle) {
    // Check for saved user preference
    const savedDarkMode = localStorage.getItem('darkMode');
    
    // Apply dark mode if saved preference exists
    if (savedDarkMode === 'enabled') {
      document.body.classList.add('dark-mode');
      darkModeToggle.checked = true;
    }
    
    // Toggle dark mode on change
    darkModeToggle.addEventListener('change', function() {
      if (this.checked) {
        document.body.classList.add('dark-mode');
        localStorage.setItem('darkMode', 'enabled');
      } else {
        document.body.classList.remove('dark-mode');
        localStorage.setItem('darkMode', 'disabled');
      }
    });
  }
}

/**
 * Utility function to format dates
 * @param {Date|string} date - Date object or date string
 * @param {string} format - Format string (default: 'YYYY-MM-DD')
 * @returns {string} Formatted date string
 */
function formatDate(date, format = 'YYYY-MM-DD') {
  const d = new Date(date);
  
  if (isNaN(d.getTime())) {
    return 'Invalid date';
  }
  
  const year = d.getFullYear();
  const month = String(d.getMonth() + 1).padStart(2, '0');
  const day = String(d.getDate()).padStart(2, '0');
  const hours = String(d.getHours()).padStart(2, '0');
  const minutes = String(d.getMinutes()).padStart(2, '0');
  const seconds = String(d.getSeconds()).padStart(2, '0');
  
  return format
    .replace('YYYY', year)
    .replace('MM', month)
    .replace('DD', day)
    .replace('HH', hours)
    .replace('mm', minutes)
    .replace('ss', seconds);
}

/**
 * Utility function to format currency
 * @param {number} amount - Amount to format
 * @param {string} currency - Currency code (default: 'USD')
 * @returns {string} Formatted currency string
 */
function formatCurrency(amount, currency = 'USD') {
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: currency
  }).format(amount);
}

/**
 * Utility function for AJAX requests
 * @param {Object} options - Request options
 * @returns {Promise} Promise that resolves with the response
 */
function ajax(options) {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    
    xhr.open(options.method || 'GET', options.url);
    
    if (options.headers) {
      Object.keys(options.headers).forEach(key => {
        xhr.setRequestHeader(key, options.headers[key]);
      });
    }
    
    xhr.onload = function() {
      if (xhr.status >= 200 && xhr.status < 300) {
        resolve(xhr.response);
      } else {
        reject({
          status: xhr.status,
          statusText: xhr.statusText,
          response: xhr.response
        });
      }
    };
    
    xhr.onerror = function() {
      reject({
        status: xhr.status,
        statusText: xhr.statusText,
        response: xhr.response
      });
    };
    
    if (options.json) {
      xhr.setRequestHeader('Content-Type', 'application/json');
      xhr.responseType = 'json';
      xhr.send(JSON.stringify(options.data));
    } else {
      xhr.send(options.data);
    }
  });
}

/**
 * Enhanced medicine search functionality
 */
function initializeMedicineSearch() {
  const searchInput = document.getElementById('medicine_search');
  const resultsContainer = document.getElementById('medicine_results');
  
  if (searchInput && resultsContainer) {
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
      const query = this.value.trim();
      
      // Clear previous timeout
      clearTimeout(searchTimeout);
      
      // Clear results if query is empty
      if (query === '') {
        resultsContainer.innerHTML = '';
        return;
      }
      
      // Set a timeout to prevent too many requests
      searchTimeout = setTimeout(() => {
        // Show loading indicator
        resultsContainer.innerHTML = '<div class="p-2 text-center"><div class="spinner"></div></div>';
        
        // Fetch results
        fetch('fetchMedicines.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: 'search=' + encodeURIComponent(query)
        })
        .then(response => response.text())
        .then(html => {
          resultsContainer.innerHTML = html;
          
          // Add animation to results
          const results = resultsContainer.querySelectorAll('.medicine-result');
          results.forEach((result, index) => {
            result.style.opacity = '0';
            result.style.transform = 'translateY(10px)';
            
            setTimeout(() => {
              result.style.transition = 'opacity 300ms, transform 300ms';
              result.style.opacity = '1';
              result.style.transform = 'translateY(0)';
            }, index * 50);
          });
        })
        .catch(error => {
          resultsContainer.innerHTML = '<div class="alert alert-danger">Error fetching medicines</div>';
          console.error('Error fetching medicines:', error);
        });
      }, 300);
    });
  }
}

/**
 * Initialize patient visit timeline
 */
function initializeVisitTimeline() {
  const timelineItems = document.querySelectorAll('.timeline-item');
  
  if (timelineItems.length > 0) {
    timelineItems.forEach((item, index) => {
      item.style.opacity = '0';
      item.style.transform = 'translateY(20px)';
      
      setTimeout(() => {
        item.style.transition = 'opacity 500ms ease, transform 500ms ease';
        item.style.opacity = '1';
        item.style.transform = 'translateY(0)';
      }, 100 + (index * 150));
    });
  }
}

/**
 * Print functionality
 */
function printElement(elementId) {
  const element = document.getElementById(elementId);
  
  if (!element) {
    console.error(`Element with ID "${elementId}" not found.`);
    return;
  }
  
  const originalContents = document.body.innerHTML;
  const printContents = element.innerHTML;
  
  document.body.innerHTML = `
    <div class="print-container">
      ${printContents}
    </div>
  `;
  
  window.print();
  document.body.innerHTML = originalContents;
  
  // Reinitialize scripts after restoring content
  document.addEventListener('DOMContentLoaded', function() {
    initializeToasts();
    initializeAnimations();
    initializeDropdowns();
    initializeModals();
    initializeFormValidation();
    initializeDataTables();
    initializeCharts();
    initializeDarkMode();
  });
}

/**
 * Export table to CSV
 */
function exportTableToCSV(tableId, filename = 'export.csv') {
  const table = document.getElementById(tableId);
  
  if (!table) {
    console.error(`Table with ID "${tableId}" not found.`);
    return;
  }
  
  // Get all rows
  const rows = table.querySelectorAll('tr');
  
  // Prepare CSV content
  let csv = [];
  
  rows.forEach(row => {
    const rowData = [];
    const cols = row.querySelectorAll('td, th');
    
    cols.forEach(col => {
      // Replace commas and quotes to avoid CSV issues
      let text = col.innerText.replace(/"/g, '""');
      rowData.push(`"${text}"`);
    });
    
    csv.push(rowData.join(','));
  });
  
  // Combine rows with newlines
  const csvContent = csv.join('\n');
  
  // Create download link
  const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
  const url = URL.createObjectURL(blob);
  const link = document.createElement('a');
  
  link.setAttribute('href', url);
  link.setAttribute('download', filename);
  link.style.display = 'none';
  
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
}

// Make utility functions globally available
window.formatDate = formatDate;
window.formatCurrency = formatCurrency;
window.ajax = ajax;
window.printElement = printElement;
window.exportTableToCSV = exportTableToCSV;
window.initializeMedicineSearch = initializeMedicineSearch;
window.initializeVisitTimeline = initializeVisitTimeline; 