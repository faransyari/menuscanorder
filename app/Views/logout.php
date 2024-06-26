<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500&display=swap" rel="stylesheet">
    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Baloo 2', cursive;
            margin-top: 56px; /* Reset default margin */
            display: flex; /* Enable flex container */
            flex-direction: column; /* Stack children vertically */
            justify-content: center; /* Center children vertically */
            align-items: center; /* Center children horizontally */
            text-align: center; /* Center text */

        }

        h1, h2 {
            font-size: calc(3rem + 1vw); /* Responsive font size for h1 */
            margin-bottom: 0.5rem; /* Spacing between h1 and h2 */
        }

        h2 {
            font-size: calc(1rem + 1vw); /* Responsive font size for h2 */
        }

    </style>
</head>
<body>
    <?php include 'components/navbar_admin.php'; ?>

<body>

<div class="container mt-5">
    <div class="text-center">
        <h2>Are you sure you want to logout?</h2>
        <button id="logoutButton" class="btn btn-danger">Logout</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('logoutButton').addEventListener('click', function() {
        // Perform logout operation
        // This could involve clearing session storage, cookies, or making a server-side request to end the session
        // For demonstration purposes, we'll just display an alert and redirect
        alert('You have been logged out.');
        window.location.href = '.html'; // Redirect to login page or home page
    });
</script>
</body>
</html>
