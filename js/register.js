$(document).ready(function() {
    $('#register-form').submit(function(e) {
        e.preventDefault();

        const full_name = $('#full_name').val();
        const email = $('#email').val();
        const password = $('#password').val();
        const country = $('#country').val();
        const city = $('#city').val();
        const contact_number = $('#contact_number').val();

        // Regex validations
        const nameRegex = /^[a-zA-Z\s]{2,50}$/;
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{8,}$/;
        const cityRegex = /^[a-zA-Z\s]{2,50}$/;
        const phoneRegex = /^\+?\d{10,14}$/;

        if (!nameRegex.test(full_name)) {
            $('#full_nameError').text('Invalid full name! Must be 2-50 letters and spaces.');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Invalid full name! Must be 2-50 letters and spaces.',
            });
            return;
        } else {
            $('#full_nameError').text('');
        }

        if (!emailRegex.test(email)) {
            $('#emailError').text('Invalid email format!');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Invalid email format!',
            });
            return;
        } else {
            $('#emailError').text('');
        }

        if (!passwordRegex.test(password)) {
            $('#passwordError').text('Password must be at least 8 characters, with at least one uppercase, one lowercase, and one number!');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password must be at least 8 characters, with at least one uppercase, one lowercase, and one number!',
            });
            return;
        } else {
            $('#passwordError').text('');
        }

        if (!country) {
            $('#countryError').text('Please select a country!');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please select a country!',
            });
            return;
        } else {
            $('#countryError').text('');
        }

        if (!cityRegex.test(city)) {
            $('#cityError').text('Invalid city! Must be 2-50 letters and spaces.');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Invalid city! Must be 2-50 letters and spaces.',
            });
            return;
        } else {
            $('#cityError').text('');
        }

        if (!phoneRegex.test(contact_number)) {
            $('#contact_numberError').text('Invalid contact number! Must be 10-14 digits, optional + prefix.');
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Invalid contact number! Must be 10-14 digits, optional + prefix.',
            });
            return;
        } else {
            $('#contact_numberError').text('');
        }

        // Show loader
        $('#loader').show();

        $.ajax({
            url: '../actions/register_customer_action.php',
            type: 'POST',
            dataType: 'json',
            data: {
                full_name: full_name,
                email: email,
                password: password,
                country: country,
                city: city,
                contact_number: contact_number,
                user_role : 2
            },
            success: function(response) {
                $('#loader').hide();
                console.log('Response:', response); // Debug log
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message || 'Failed to register',
                    });
                }
            },
            error: function(xhr, status, error) {
                $('#loader').hide();
                console.log('AJAX Error:', xhr.responseText, status, error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred! Please try again later.',
                });
            }
        });
    });
});