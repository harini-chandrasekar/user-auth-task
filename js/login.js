$(document).ready(function() {
    $('#loginForm').on('submit', function(e) {
        e.preventDefault();
        
        const email = $('#email').val();
        const password = $('#password').val();
        
        $.ajax({
            type: 'POST',
            url: 'http://localhost/USER-AUTH/php/login.php',
            data: {
                email: email,
                password: password
            },datatupe:"jSON",
            success: function(response) {

                if (response.success) {
                    localStorage.setItem('sessionToken', response.token);
                    localStorage.setItem('userId', response.userId);
                    localStorage.setItem('email', email);
                    showMessage('Login successful! Redirecting...', 'success');
                    setTimeout(() => {
                        window.location.href = 'profile.html';
                    }, 2000);
                } else {
                    showMessage(response.message, 'danger');
                }
            },
            error: function (xhr) {
    console.error(xhr.responseText);
    alert(xhr.responseText); // shows exact PHP error
}

        });
    });
    
    function showMessage(message, type) {
        $('#message').html(`<div class="alert alert-${type}">${message}</div>`);
    }
});
