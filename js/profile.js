$(document).ready(function() {
    const sessionToken = localStorage.getItem('sessionToken');
    const userId = localStorage.getItem('userId');
    const email = localStorage.getItem('email');
    
    if (!sessionToken || !userId) {
        window.location.href = 'login.html';
        return;
    }
    
    $('#email').val(email);
    loadProfile();
    
    function loadProfile() {
        $.ajax({
            type: 'POST',
            url: 'php/profile.php',
            data: {
                action: 'get',
                userId: userId,
                token: sessionToken
            },
            success: function(response) {
                const result = JSON.parse(response);
                if (result.success) {
                    $('#fullName').val(result.data.fullName || '');
                    $('#age').val(result.data.age || '');
                    $('#dob').val(result.data.dob || '');
                    $('#contact').val(result.data.contact || '');
                    $('#address').val(result.data.address || '');
                }
            }
        });
    }
    
    $('#profileForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            type: 'POST',
            url: 'php/profile.php',
            data: {
                action: 'update',
                userId: userId,
                token: sessionToken,
                fullName: $('#fullName').val(),
                age: $('#age').val(),
                dob: $('#dob').val(),
                contact: $('#contact').val(),
                address: $('#address').val()
            },
            success: function(response) {
    const result = JSON.parse(response);   

    if (result.success) {
        showMessage(result.message || 'Profile updated successfully!', 'success');
    } else {
        showMessage(result.message || 'Update failed', 'danger');
    }
}

        });
    });
    
    $('#logoutBtn').on('click', function() {
        localStorage.clear();
        window.location.href = 'index.html';
    });
    
    function showMessage(message, type) {
        $('#message').html(`<div class="alert alert-${type}">${message}</div>`);
    }
});
