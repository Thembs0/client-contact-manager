// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing...');
    
    // Initialize tabs
    initTabs();
    
    // Initialize AJAX forms
    initAjaxForms();
    
    // Initialize unlink buttons
    initUnlinkButtons();
    
    // Initialize real-time validation
    addRealTimeValidation();
});

function initTabs() {
    console.log('Initializing tabs...');
    const tabLinks = document.querySelectorAll('.tab-link');
    
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get tab id
            const tabId = this.getAttribute('data-tab');
            
            // Remove active class from all tabs and contents
            document.querySelectorAll('.tab-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));
            
            // Add active class to current tab and content
            this.classList.add('active');
            document.getElementById(tabId).classList.add('active');
        });
    });
}

function initAjaxForms() {
    console.log('Initializing AJAX forms...');
    
    // Link contact form
    const linkContactForm = document.getElementById('link-contact-form');
    if (linkContactForm) {
        linkContactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Linking...';
            submitBtn.disabled = true;
            
            const isClientPage = window.location.href.includes('action=clients');
            const url = isClientPage ? 'index.php?action=clients&page=linkContact' : 'index.php?action=contacts&page=linkClient';
            
            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Linked successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Error linking', 'error');
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
    
    // Link client form
    const linkClientForm = document.getElementById('link-client-form');
    if (linkClientForm) {
        linkClientForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Linking...';
            submitBtn.disabled = true;
            
            fetch('index.php?action=contacts&page=linkClient', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification(data.message || 'Linked successfully!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showNotification(data.message || 'Error linking', 'error');
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('An error occurred', 'error');
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
}

// Define initUnlinkButtons function
function initUnlinkButtons() {
    console.log('Initializing unlink buttons...');
    const unlinkButtons = document.querySelectorAll('.unlink-btn');
    console.log('Found', unlinkButtons.length, 'unlink buttons');
    
    unlinkButtons.forEach(btn => {
        // Remove any existing event listeners
        btn.removeEventListener('click', handleUnlink);
        // Add new event listener
        btn.addEventListener('click', handleUnlink);
    });
}

// Handle unlink clicks
function handleUnlink(e) {
    e.preventDefault();
    
    const btn = e.currentTarget;
    const clientId = btn.getAttribute('data-client-id');
    const contactId = btn.getAttribute('data-contact-id');
    const originalText = btn.textContent;
    
    console.log('Unlink clicked - clientId:', clientId, 'contactId:', contactId);
    
    if (!clientId || !contactId) {
        showNotification('Missing data attributes', 'error');
        return;
    }
    
    if (!confirm('Are you sure you want to unlink?')) {
        return;
    }
    
    const formData = new FormData();
    formData.append('client_id', clientId);
    formData.append('contact_id', contactId);
    
    // Show loading state
    btn.textContent = 'Unlinking...';
    btn.style.opacity = '0.5';
    btn.style.pointerEvents = 'none';
    
    // Determine which controller to use based on the current page
    const isClientPage = window.location.href.includes('action=clients');
    const url = isClientPage ? 'index.php?action=clients&page=unlinkContact' : 'index.php?action=contacts&page=unlinkClient';
    
    console.log('Sending unlink request to:', url);
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        console.log('Response status:', response.status);
        console.log('Response headers:', response.headers);
        
        // Get the response text first
        return response.text().then(text => {
            console.log('RAW RESPONSE:', text);
            console.log('Response length:', text.length);
            console.log('First 100 chars:', text.substring(0, 100));
            
            // Try to parse it as JSON
            try {
                const data = JSON.parse(text);
                console.log('Parsed JSON:', data);
                return data;
            } catch (e) {
                console.error('JSON Parse Error:', e);
                console.error('The response is not valid JSON');
                throw new Error('Invalid JSON response. Check console for raw response.');
            }
        });
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message || 'Unlinked successfully!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification(data.message || 'Error unlinking', 'error');
            // Reset button state
            btn.textContent = originalText;
            btn.style.opacity = '1';
            btn.style.pointerEvents = 'auto';
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        showNotification('Error: ' + error.message, 'error');
        // Reset button state
        btn.textContent = originalText;
        btn.style.opacity = '1';
        btn.style.pointerEvents = 'auto';
    });
}

// Notification function
function showNotification(message, type) {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.notification');
    existingNotifications.forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        border-radius: 5px;
        z-index: 9999;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        animation: slideIn 0.3s ease;
        font-weight: 500;
    `;
    document.body.appendChild(notification);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
            }, 300);
        }
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Client-side validation
function validateClientForm() {
    const name = document.getElementById('name').value;
    
    if (!name.trim()) {
        showNotification('Client name is required', 'error');
        return false;
    }
    
    return true;
}

function validateContactForm() {
    const name = document.getElementById('name').value;
    const surname = document.getElementById('surname').value;
    const email = document.getElementById('email').value;
    
    if (!name.trim()) {
        showNotification('Name is required', 'error');
        return false;
    }
    
    if (!surname.trim()) {
        showNotification('Surname is required', 'error');
        return false;
    }
    
    if (!email.trim()) {
        showNotification('Email is required', 'error');
        return false;
    }
    
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        showNotification('Please enter a valid email address', 'error');
        return false;
    }
    
    return true;
}

// Real-time validation
function addRealTimeValidation() {
    const emailInput = document.getElementById('email');
    if (emailInput) {
        emailInput.addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                showFieldError(this, 'Please enter a valid email address');
            } else {
                clearFieldError(this);
            }
        });
    }
    
    const nameInput = document.getElementById('name');
    if (nameInput) {
        nameInput.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                showFieldError(this, 'This field is required');
            } else {
                clearFieldError(this);
            }
        });
    }
}

function showFieldError(field, message) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (!existingError) {
        const error = document.createElement('small');
        error.className = 'field-error';
        error.style.color = '#dc3545';
        error.style.display = 'block';
        error.style.marginTop = '5px';
        error.textContent = message;
        field.parentNode.appendChild(error);
    }
    field.style.borderColor = '#dc3545';
}

function clearFieldError(field) {
    const error = field.parentNode.querySelector('.field-error');
    if (error) {
        error.remove();
    }
    field.style.borderColor = '#ddd';
}