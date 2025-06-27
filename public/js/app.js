// Utility function for CSRF token
function getCsrfToken() {
    const tokenField = document.querySelector('input[name="csrf_token"]');
    return tokenField ? tokenField.value : null;
}

// Helper function for making API requests
async function apiRequest(url, options = {}) {
    const token = getCsrfToken();
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': token
        },
        credentials: 'same-origin'
    };

    try {
        const response = await fetch(url, { ...defaultOptions, ...options });
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return await response.json();
    } catch (error) {
        console.error('API request failed:', error);
        throw error;
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize all popovers
    const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });

    // Form validation
    const forms = document.querySelectorAll('.needs-validation');
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });

    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });
});

// Transaction form handling
if (document.getElementById('transactionForm')) {
    const form = document.getElementById('transactionForm');
    const modeToggle = document.getElementById('modeToggle');

    // Toggle between basic and full mode
    if (modeToggle) {
        modeToggle.addEventListener('change', function() {
            const fullModeFields = document.querySelectorAll('.full-mode-field');
            fullModeFields.forEach(field => {
                field.style.display = this.checked ? 'block' : 'none';
                const inputs = field.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.required = this.checked;
                });
            });
        });
    }

    // Handle entity change
    const startEntitySelect = document.getElementById('start_entity_id');
    if (startEntitySelect) {
        startEntitySelect.addEventListener('change', function() {
            const feeEntitySelect = document.getElementById('fee_entity_id');
            if (feeEntitySelect && !modeToggle.checked) {
                feeEntitySelect.value = this.value;
            }
        });
    }
}

// Date picker initialization
const datePickers = document.querySelectorAll('.datepicker');
datePickers.forEach(picker => {
    picker.addEventListener('focus', (e) => {
        e.target.type = 'date';
    });
    picker.addEventListener('blur', (e) => {
        if (!e.target.value) {
            e.target.type = 'text';
        }
    });
});
