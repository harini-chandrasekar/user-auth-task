$(document).ready(function() {
    $('#registerForm').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val();
        const password = $('#password').val();
        const confirmPassword = $('#confirmPassword').val();
        
        if (password !== confirmPassword) {
            showMessage('Passwords do not match!', 'danger');
            return;
        }
        
        $.ajax({
            type: 'POST',
            url: "http://localhost/USER-AUTH/php/register.php"
,

            data: {
                email: email,
                password: password
            },datatype:JSON,
            success: function(response) {
                
                if (response.success) {
                    showMessage('Registration successful! Redirecting to login...', 'success');
                    setTimeout(() => {
                        window.location.href = 'login.html';
                    }, 2000);
                } else {
                    showMessage(response.message, 'danger');
                }
            },
            error: function() {
                showMessage('An error occurred. Please try again.', 'danger');
            }
        });
    });
    
    function showMessage(message, type) {
        $('#message').html(`<div class="alert alert-${type}">${message}</div>`);
    }
});
