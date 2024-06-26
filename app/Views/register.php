<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500&display=swap" rel="stylesheet">
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Baloo 2', cursive;
            background-color: #000; /* Dark theme background */
            color: #fff; /* Light text color for contrast */
            height: 80vh; /* Full height of the viewport */
            margin: 0; /* Reset default margin */
            display: flex; /* Enable flex container */
            flex-direction: column; /* Stack children vertically */
            justify-content: center; /* Center children vertically */
            align-items: center; /* Center children horizontally */
            text-align: center; /* Center text */
            overflow: hidden; /* Disable scrolling */
        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.8); /* Semi-transparent black */
            backdrop-filter: blur(10px); /* Frosted glass effect */
            position: absolute; /* Take navbar out of flow */
            top: 0; /* Position at top */
            width: 100%; /* Full width */
            z-index: 1000; /* Ensure it's above other items */
        }

        .navbar-nav .nav-link {
            color: #fff; /* White text color for navbar items */
            margin-right: 10px; /* Spacing between navbar items */
        }

        .navbar-nav .nav-link:hover {
            color: #007bff; /* Blue color for hover state */
        }

        .btn-sign-in {
            background-color: #007bff; /* Blue background color for 'Sign In' button */
            border: none; /* No border */
            border-radius: 20px; /* Rounded borders for the 'Sign In' button */
            color: #fff; /* White text color */
            padding: 8px 20px; /* Padding inside the button */
            font-size: 0.9rem; /* Font size for the text inside the button */
        }

        .btn-sign-in:hover {
            background-color: #0056b3; /* Darker blue on hover */
            color: #fff; /* Ensure text remains white on hover */
        }

        h1, h2 {
            font-size: calc(3rem + 1vw); /* Responsive font size for h1 */
            margin-bottom: 0.5rem; /* Spacing between h1 and h2 */
        }

        h2 {
            font-size: calc(1rem + 1vw); /* Responsive font size for h2 */
        }

        .register-link {
            color: #007bff; /* Blue text color */
            text-decoration: none; /* No underline */
            margin-top: 15px; /* Space above the link */
            display: block; /* Ensure it's on a new line */
            transition: color 0.3s ease; /* Smooth color transition */
        }

        .register-link:hover {
            color: #0056b3; /* Darker blue on hover */
        }

        /* Mobile responsiveness */
        @media (max-width: 768px) {
            .navbar-nav .nav-link {
                margin-right: 0; /* Remove spacing for mobile view */
            }
        }
        .background-image {
            position: fixed;
            top: 50%; /* Position the top of the image at the middle of the viewport */
            left: 0;
            width: 100%; /* Adjust the width as desired */
            height: 100%; /* Double the viewport height to cover half of it */
            background-image: url('<?php echo base_url('asset/foodpic.png'); ?>');
            background-repeat: no-repeat;
            background-position: center top; /* Position the image at the top center */
            background-size: cover; /* Resize the image to cover the container */
            z-index: -1; /* Ensure it's behind other items */
            overflow: hidden; /* Hide the half of the image that goes below the container */
            backdrop-filter: blur(10px); /* Frosted glass effect */
        }

        /* Custom styles for registration form */
        .form-label {
            text-align: left; /* Align label text to the left */
            color: #fff; /* White text color */
        }

        .form-control {
            text-align: left; /* Align input text to the left */
            background-color: #000; /* Black background color */
            border: 1px solid #fff; /* White border */
            color: #fff; /* White text color */
            border-radius: 20px; /* Rounded borders for the 'Sign In' button */
            padding: 8px 20px; /* Padding inside the button */
            font-size: 0.9rem; /* Font size for the text inside the button */
        }

        .form-control:focus {
            background-color: #000; /* Black background color on focus */
            border-color: #fff; /* White border color on focus */
            color: #fff; /* White text color on focus */
        }

        .btn-primary {
            background-color: #007bff; /* Blue background color for 'Sign In' button */
            border: none; /* No border */
            border-radius: 20px; /* Rounded borders for the 'Sign In' button */
            color: #fff; /* White text color */
            padding: 8px 20px; /* Padding inside the button */
            font-size: 0.9rem; /* Font size for the text inside the button */
        }

        .btn-primary:hover {
            background-color: #0056b3; /* Darker blue on hover */
            border-color: #fff; /* White border color on hover */
        }
    </style>
</head>
<body>
    <header class="navbar navbar-expand-md navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="./">MenuScanOrder</a>
            <ul class="navbar-nav me-auto"></ul>
            <div class="d-flex">
                <button class="btn btn-sign-in" onclick="window.location.href='./login'" type="button">Login</button>
            </div>
        </div>
    </header>

    <div class="background-image"></div>
    <h1><b>MenuScanOrder.</b></h1>
    <h2>Create an Account</h2>
    <div class="container mt-5 login-container">
        <div class="row justify-content-center">
            <div class="col-md-4">
            <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label for="fullName" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Full Name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Email Address" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="registerAsAdmin" name="registerAsAdmin">
                    <label class="form-check-label" for="registerAsAdmin">Register as Admin</label>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>

            </div>
        </div>
    </div>
</body>
</html>
