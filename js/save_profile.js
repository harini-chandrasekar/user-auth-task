$(document).ready(function () {

    $('#profileForm').on('submit', function (e) {
        e.preventDefault(); // stop page reload

        const profileData = {
            userId: localStorage.getItem('userId'),
            fullName: $('#fullName').val(),
            age: $('#age').val(),
            dob: $('#dob').val(),
            contact: $('#contact').val(),
            address: $('#address').val()
        };

        $.ajax({
            type: 'POST',
            url: 'php/save_profile_file.php', // backend file
            data: profileData,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    $('#message').html(
                        `<div class="alert alert-success">${response.message}</div>`
                    );
                } else {
                    $('#message').html(
                        `<div class="alert alert-danger">${response.message}</div>`
                    );
                }
            },
            error: function () {
                $('#message').html(
                    `<div class="alert alert-danger">Server error</div>`
                );
            }
        });
    });

});
