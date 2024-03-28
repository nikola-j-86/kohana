$(document).ready(function() {
    $('#login-form').submit(function(event) {
        event.preventDefault();
        $.ajax({
            type: 'POST',
            url: '/kohana/login/authenticate',
            data: $(this).serialize(),
            success: function(response) {
                if (response.success) {
                    window.location.href = response.redirect;
                } else {
                    alert('Login failed. Please check your credentials.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});