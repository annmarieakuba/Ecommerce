$(document).ready(function() {
    $('#login-form').submit(function(e) {
        e.preventDefault();

        const email = $('#email').val();
        const password = $('#password').val();

        // Clear previous errors
        $('.error').text('');

        // Regex validations
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        // Validate email
        if (!email || email.trim() === '') {
            showError('Please enter your email address');
            return;
        }

        if (!emailRegex.test(email)) {
            showError('Please enter a valid email address');
            return;
        }

        // Validate password
        if (!password || password.trim() === '') {
            showError('Please enter your password');
            return;
        }

        if (password.length < 6) {
            showError('Password must be at least 6 characters long');
            return;
        }

        // Show loader
        $('#loader').show();

        $.ajax({
            url: '../actions/login_customer_action.php',
            type: 'POST',
            dataType: 'json',
            data: {
                email: email,
                password: password
            },
            success: function(response) {
                $('#loader').hide();
                console.log('Login response:', response);
                
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Welcome back, ' + response.customer.name + '!',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        // Redirect to index page
                        window.location.href = '../index.php';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Login Failed',
                        text: response.message || 'Invalid email or password',
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#loader').hide();
                console.log('AJAX Error:', xhr.responseText, status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred! Please try again later.',
                });
            }
        });
    });

    function showError(message) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: message,
        });
    }
});
