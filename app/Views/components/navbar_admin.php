
    <style>
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
    </style>

    <header class="navbar navbar-expand-md navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MenuScanOrder Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="/menuscanorder/admin/">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="/menuscanorder/admin/orders">Orders</a></li>
                    <li class="nav-item"><a class="nav-link" href="/menuscanorder/admin/menu_management">Menu Management</a></li>
                    <li class="nav-item"><a class="nav-link" href="/menuscanorder/admin/generate_qr">Generate QR</a></li>
                </ul>
                <div class="d-flex">
                    <button class="btn btn-sign-in" onclick="window.location.href='./logout'" type="button">Logout</button>
                </div>
            </div>
        </div>
    </header>
