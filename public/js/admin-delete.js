/**
 * Admin Delete Operations with SweetAlert
 * Matching the glassmorphism and gradient design of main.css
 */

// SweetAlert custom styling to match main.css
const swalCustomStyles = {
    popup: 'glassmorphism-popup',
    title: 'swal-title',
    htmlContainer: 'swal-content',
    confirmButton: 'swal-confirm-btn',
    cancelButton: 'swal-cancel-btn',
    actions: 'swal-actions',
    icon: 'swal-icon'
};

// Initialize SweetAlert configurations
const initializeDeleteHandlers = () => {
    // Handle all delete forms with class 'delete-form'
    document.addEventListener('submit', function(e) {
        if (e.target.classList.contains('delete-form')) {
            e.preventDefault();
            handleDelete(e.target);
        }
    });

    // Handle all delete buttons with class 'delete-btn'
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-btn')) {
            e.preventDefault();
            const form = e.target.closest('form');
            if (form) {
                handleDelete(form);
            }
        }
    });
};

// Main delete handler function
const handleDelete = (form) => {
    const deleteType = form.dataset.deleteType || 'item';
    const deleteName = form.dataset.deleteName || '';
    const deleteWarning = form.dataset.deleteWarning || '';
    
    let title, text, icon;
    
    switch (deleteType) {
        case 'user':
        case 'client':
            title = `Delete ${deleteName ? deleteName : 'Client'}?`;
            text = `This will permanently delete the client${deleteName ? ` "${deleteName}"` : ''} and all their wedding cards. This action cannot be undone!`;
            icon = 'warning';
            break;
        case 'template':
            title = `Delete ${deleteName ? deleteName : 'Template'}?`;
            text = `This will permanently delete the template${deleteName ? ` "${deleteName}"` : ''}. Any wedding cards using this template will be affected!`;
            icon = 'warning';
            break;
        case 'approval':
            title = `Approve ${deleteName ? deleteName : 'Wedding Card'}?`;
            text = `This will approve and publish the wedding card${deleteName ? ` "${deleteName}"` : ''} immediately, making it visible to the public.`;
            icon = 'question';
            break;
        default:
            title = `Delete ${deleteType}?`;
            text = deleteWarning || `This will permanently delete this ${deleteType}. This action cannot be undone!`;
            icon = 'warning';
    }

    // Determine icon and button text based on type
    let iconClass, confirmButtonText;
    
    if (deleteType === 'approval') {
        iconClass = 'fas fa-check-circle approve-icon';
        confirmButtonText = '<i class="fas fa-check"></i> Yes, Approve It!';
    } else {
        iconClass = 'fas fa-trash-alt delete-icon';
        confirmButtonText = '<i class="fas fa-trash"></i> Yes, Delete It!';
    }

    Swal.fire({
        title: title,
        html: `<div class="delete-content">
            <div class="delete-icon-wrapper">
                <i class="${iconClass}"></i>
            </div>
            <p class="delete-text">${text}</p>
            ${deleteWarning ? `<div class="delete-extra-warning"><i class="fas fa-exclamation-triangle warning-icon"></i> ${deleteWarning}</div>` : ''}
        </div>`,
        icon: false, // We'll use custom HTML
        showCancelButton: true,
        confirmButtonText: confirmButtonText,
        cancelButtonText: '<i class="fas fa-times"></i> Cancel',
        customClass: swalCustomStyles,
        buttonsStyling: false,
        reverseButtons: true,
        allowOutsideClick: true,
        allowEscapeKey: true,
        showClass: {
            popup: 'animate__animated animate__bounceIn animate__fast'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOut animate__faster'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            performDelete(form, deleteType, deleteName);
        }
    });
};

// Perform the actual delete operation via AJAX
const performDelete = (form, deleteType, deleteName) => {
    const formData = new FormData(form);
    const actionUrl = form.action;
    
    // Show loading state
    const loadingTitle = deleteType === 'approval' ? 'Approving...' : 'Deleting...';
    const loadingText = deleteType === 'approval' ? 
        `Please wait while we approve ${deleteName || 'the wedding card'}...` : 
        `Please wait while we delete ${deleteName || 'the item'}...`;
    
    Swal.fire({
        title: loadingTitle,
        html: `<div class="loading-content">
            <div class="loading-spinner"></div>
            <p>${loadingText}</p>
        </div>`,
        icon: false,
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        customClass: swalCustomStyles,
        showClass: {
            popup: 'animate__animated animate__fadeIn animate__faster'
        }
    });

    // Perform AJAX request
    fetch(actionUrl, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showSuccessAndRedirect(data.message, data.redirect_url, deleteType);
        } else {
            showError(data.message || 'An error occurred while deleting.');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showError('An unexpected error occurred. Please try again.');
    });
};

// Show success message and redirect
const showSuccessAndRedirect = (message, redirectUrl, deleteType) => {
    const successTitle = deleteType === 'approval' ? 'Approved Successfully!' : 'Deleted Successfully!';
    
    Swal.fire({
        title: successTitle,
        html: `<div class="success-content">
            <div class="success-icon-wrapper">
                <i class="fas fa-check-circle success-icon"></i>
            </div>
            <p class="success-text">${message}</p>
            <div class="success-animation">
                <div class="sparkle"></div>
                <div class="sparkle"></div>
                <div class="sparkle"></div>
            </div>
        </div>`,
        icon: false,
        confirmButtonText: '<i class="fas fa-arrow-right"></i> Continue',
        customClass: swalCustomStyles,
        buttonsStyling: false,
        allowOutsideClick: true,
        showClass: {
            popup: 'animate__animated animate__bounceIn animate__faster'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOut animate__faster'
        }
    }).then(() => {
        if (redirectUrl) {
            window.location.href = redirectUrl;
        } else {
            location.reload();
        }
    });
};

// Show error message
const showError = (message) => {
    Swal.fire({
        title: 'Delete Failed',
        html: `<div class="error-content">
            <div class="error-icon-wrapper">
                <i class="fas fa-exclamation-triangle error-icon"></i>
            </div>
            <p class="error-text">${message}</p>
        </div>`,
        icon: false,
        confirmButtonText: '<i class="fas fa-times"></i> Close',
        customClass: swalCustomStyles,
        buttonsStyling: false,
        showClass: {
            popup: 'animate__animated animate__shakeX animate__faster'
        }
    });
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', initializeDeleteHandlers);

// Add custom CSS styles
const addCustomStyles = () => {
    const style = document.createElement('style');
    style.textContent = `
        /* SweetAlert Custom Styles to match main.css */
        .glassmorphism-popup {
            background: linear-gradient(145deg, #ffffff 0%, #f8f9ff 100%) !important;
            backdrop-filter: none !important;
            border: 3px solid transparent !important;
            background-clip: padding-box !important;
            border-radius: 30px !important;
            box-shadow: 
                0 25px 50px rgba(255, 107, 157, 0.25),
                0 0 0 1px rgba(255, 107, 157, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
            color: #2c3e50 !important;
            position: relative !important;
            overflow: hidden !important;
        }

        .glassmorphism-popup::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255, 107, 157, 0.05) 0%, rgba(196, 69, 105, 0.05) 100%);
            border-radius: 30px;
            z-index: -1;
        }

        .swal-title {
            font-family: 'Poppins', sans-serif !important;
            font-weight: 800 !important;
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 50%, #e74c3c 100%) !important;
            -webkit-background-clip: text !important;
            -webkit-text-fill-color: transparent !important;
            background-clip: text !important;
            font-size: 32px !important;
            margin-bottom: 25px !important;
            text-shadow: 0 2px 10px rgba(255, 107, 157, 0.3) !important;
            letter-spacing: -0.5px !important;
        }

        .swal-content {
            font-family: 'Poppins', sans-serif !important;
            color: #2c3e50 !important;
        }

        .delete-content, .success-content, .error-content, .loading-content {
            text-align: center;
            padding: 20px;
        }

        .delete-icon-wrapper, .success-icon-wrapper, .error-icon-wrapper {
            margin-bottom: 20px;
            position: relative;
        }

        .delete-icon {
            font-size: 80px;
            background: linear-gradient(135deg, #ff6b9d 0%, #e74c3c 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: deleteIconBounce 2s ease-in-out infinite;
            filter: drop-shadow(0 4px 15px rgba(231, 76, 60, 0.3));
        }

        .approve-icon {
            font-size: 80px;
            background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: approveIconBounce 2s ease-in-out infinite;
            filter: drop-shadow(0 4px 15px rgba(56, 161, 105, 0.3));
        }

        .success-icon {
            font-size: 60px;
            color: #11998e;
            animation: successIconBounce 0.8s ease-out;
        }

        .error-icon {
            font-size: 60px;
            color: #fc4a1a;
            animation: errorIconShake 0.8s ease-out;
        }

        .delete-text, .success-text, .error-text {
            font-size: 16px;
            line-height: 1.6;
            margin: 15px 0;
            color: #2c3e50;
        }

        .delete-extra-warning {
            background: linear-gradient(135deg, rgba(255, 107, 157, 0.1) 0%, rgba(231, 76, 60, 0.1) 100%);
            border: 2px solid rgba(255, 107, 157, 0.3);
            border-radius: 20px;
            padding: 20px;
            margin-top: 20px;
            color: #c44569;
            font-weight: 600;
            box-shadow: 0 8px 25px rgba(255, 107, 157, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .warning-icon {
            color: #f39c12;
            font-size: 18px;
            flex-shrink: 0;
        }

        .loading-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid rgba(255, 107, 157, 0.2);
            border-left: 4px solid #ff6b9d;
            border-radius: 50%;
            animation: loadingSpin 1s linear infinite;
            margin: 0 auto 20px;
        }

        .success-animation {
            position: relative;
            height: 40px;
            margin-top: 20px;
        }

        .sparkle {
            position: absolute;
            width: 8px;
            height: 8px;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border-radius: 50%;
            animation: sparkleFloat 2s ease-in-out infinite;
        }

        .sparkle:nth-child(1) { left: 20%; animation-delay: 0s; }
        .sparkle:nth-child(2) { left: 50%; animation-delay: 0.5s; }
        .sparkle:nth-child(3) { left: 80%; animation-delay: 1s; }

        .swal-confirm-btn {
            background: linear-gradient(135deg, #ff6b9d 0%, #c44569 100%) !important;
            color: white !important;
            border: none !important;
            border-radius: 15px !important;
            padding: 15px 30px !important;
            font-weight: 600 !important;
            font-family: 'Poppins', sans-serif !important;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            box-shadow: 0 8px 25px rgba(255, 107, 157, 0.3) !important;
            margin: 0 10px !important;
        }

        .swal-confirm-btn:hover {
            transform: translateY(-2px) scale(1.05) !important;
            box-shadow: 0 12px 35px rgba(255, 107, 157, 0.4) !important;
        }

        .swal-cancel-btn {
            background: linear-gradient(135deg, #f8f9ff 0%, #e8ecf4 100%) !important;
            color: #2c3e50 !important;
            border: 2px solid #cbd5e0 !important;
            border-radius: 15px !important;
            padding: 15px 30px !important;
            font-weight: 600 !important;
            font-family: 'Poppins', sans-serif !important;
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275) !important;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1) !important;
            margin: 0 10px !important;
        }

        .swal-cancel-btn:hover {
            background: linear-gradient(135deg, #ffffff 0%, #f0f4f8 100%) !important;
            transform: translateY(-2px) scale(1.02) !important;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15) !important;
            border-color: #a0aec0 !important;
        }

        .swal-actions {
            margin-top: 30px !important;
            justify-content: center !important;
            gap: 15px !important;
        }

        /* Animations */
        @keyframes deleteIconBounce {
            0%, 20%, 50%, 80%, 100% { 
                transform: translateY(0) scale(1); 
                opacity: 0.9; 
            }
            40% { 
                transform: translateY(-15px) scale(1.15); 
                opacity: 1; 
            }
            60% { 
                transform: translateY(-8px) scale(1.08); 
                opacity: 0.95; 
            }
        }

        @keyframes approveIconBounce {
            0%, 20%, 50%, 80%, 100% { 
                transform: translateY(0) scale(1); 
                opacity: 0.9; 
            }
            40% { 
                transform: translateY(-15px) scale(1.15); 
                opacity: 1; 
            }
            60% { 
                transform: translateY(-8px) scale(1.08); 
                opacity: 0.95; 
            }
        }

        @keyframes successIconBounce {
            0% { transform: scale(0); }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); }
        }

        @keyframes errorIconShake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }

        @keyframes loadingSpin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @keyframes sparkleFloat {
            0%, 100% { transform: translateY(0px) scale(1); opacity: 1; }
            50% { transform: translateY(-20px) scale(1.2); opacity: 0.8; }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .glassmorphism-popup {
                margin: 20px !important;
                border-radius: 20px !important;
            }
            
            .swal-confirm-btn, .swal-cancel-btn {
                padding: 12px 20px !important;
                font-size: 14px !important;
            }
            
            .delete-icon, .success-icon, .error-icon {
                font-size: 45px !important;
            }
        }
    `;
    document.head.appendChild(style);
};

// Initialize styles when script loads
addCustomStyles(); 