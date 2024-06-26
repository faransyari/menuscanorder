<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
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

    </style>
</head>
<body>
    <?php include 'components/navbar_admin.php'; ?>

    <main class="container py-4">
        <h2>Welcome to the Admin Dashboard</h2>
    </main>
</body>
</html>
