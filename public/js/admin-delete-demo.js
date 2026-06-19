/**
 * Demo Script for Admin Delete System
 * Shows examples of how to use the new SweetAlert delete system
 */

// Example of how to manually trigger a delete with custom data
function demoDeleteUser() {
    const form = document.createElement('form');
    form.action = '/admin/users/1';
    form.method = 'POST';
    form.className = 'delete-form';
    form.dataset.deleteType = 'client';
    form.dataset.deleteName = 'John Doe';
    form.dataset.deleteWarning = 'This will delete all their 3 wedding cards.';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodInput);
    
    // Manually call the delete handler
    handleDelete(form);
}

// Example of how to manually trigger a template delete
function demoDeleteTemplate() {
    const form = document.createElement('form');
    form.action = '/admin/templates/1';
    form.method = 'POST';
    form.className = 'delete-form';
    form.dataset.deleteType = 'template';
    form.dataset.deleteName = 'Elegant Wedding Template';
    form.dataset.deleteWarning = 'This will affect 5 wedding cards.';
    
    const csrfToken = document.createElement('input');
    csrfToken.type = 'hidden';
    csrfToken.name = '_token';
    csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    
    const methodInput = document.createElement('input');
    methodInput.type = 'hidden';
    methodInput.name = '_method';
    methodInput.value = 'DELETE';
    
    form.appendChild(csrfToken);
    form.appendChild(methodInput);
    
    // Manually call the delete handler
    handleDelete(form);
}

// Test the SweetAlert styling
function testSweetAlertStyling() {
    Swal.fire({
        title: 'Test SweetAlert Styling',
        html: `<div class="test-content">
            <p>This is a test of our beautiful SweetAlert styling that matches the main.css design.</p>
            <div style="margin: 20px 0; padding: 15px; background: rgba(255, 107, 157, 0.1); border-radius: 10px;">
                ✨ Features glassmorphism effects<br>
                🎨 Gradient backgrounds<br>
                💫 Beautiful animations<br>
                🎯 Perfect color matching
            </div>
        </div>`,
        icon: false,
        confirmButtonText: '<i class="fas fa-heart"></i> Beautiful!',
        customClass: {
            popup: 'glassmorphism-popup',
            title: 'swal-title',
            htmlContainer: 'swal-content',
            confirmButton: 'swal-confirm-btn'
        },
        buttonsStyling: false,
        showClass: {
            popup: 'animate__animated animate__bounceIn'
        }
    });
}

// Console helpers for testing
if (typeof window !== 'undefined') {
    window.demoDeleteUser = demoDeleteUser;
    window.demoDeleteTemplate = demoDeleteTemplate;
    window.testSweetAlertStyling = testSweetAlertStyling;
    
    console.log(`
🎨 Admin Delete System Demo Loaded!

Try these commands in the console:
- demoDeleteUser()      → Test user deletion
- demoDeleteTemplate()  → Test template deletion  
- testSweetAlertStyling() → Test SweetAlert styling

The system automatically detects:
✅ Delete forms with class 'delete-form'
✅ Delete buttons with class 'delete-btn'
✅ Custom data attributes for messaging
✅ AJAX requests with beautiful animations
    `);
} 