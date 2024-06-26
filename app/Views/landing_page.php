<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LandingPage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        body {
            font-family: 'Baloo 2', cursive;
            background-color: #000;
            color: #fff;
            height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            overflow: hidden;

        }

        .navbar {
            background-color: rgba(0, 0, 0, 0.8);
            backdrop-filter: blur(10px);
            position: absolute;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .navbar-nav .nav-link {
            color: #fff;
            margin-right: 10px;
        }

        .navbar-nav .nav-link:hover {
            color: #007bff;
        }

        .btn-sign-in {
            background-color: #007bff; 
            border: none; 
            border-radius: 20px; 
            color: #fff; 
            padding: 8px 20px; 
            font-size: 0.9rem; 
        }

        .btn-sign-in:hover {
            background-color: #0056b3; 
            color: #fff; 
        }

        h1, h2 {
            font-size: calc(3rem + 1vw);
            margin-bottom: 0.5rem; 
        }

        h2 {
            font-size: calc(1rem + 1vw);
        }

        .btn-start-ordering {
            background-color: #007bff; 
            color: #fff; 
            padding: 10px 30px; 
            margin-top: 20px;
            border: none; 
            border-radius: 20px; 
            font-size: 1rem; 
            transition: background-color 0.3s ease; 
        }

        .btn-start-ordering:hover {
            background-color: #0056b3; 
        }

        .register-link {
            color: #007bff; 
            text-decoration: none; 
            margin-top: 15px; 
            display: block; 
            transition: color 0.3s ease; 
        }

        .register-link:hover {
            color: #0056b3; 
        }

        @media (max-width: 768px) {
            .navbar-nav .nav-link {
                margin-right: 0; 
            }
        }
        .background-image {
            position: fixed;
            top: 50%;
            left: 0;
            width: 100%; 
            height: 100%; 
            background-image: url('<?php echo base_url('asset/foodpic.png'); ?>');
            background-repeat: no-repeat;
            background-position: center top; 
            background-size: cover; 
            z-index: -1;
            overflow: hidden; 
            backdrop-filter: blur(10px); 
        }


    </style>
</head>
<body>
    <header class="navbar navbar-expand-md navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">MenuScanOrder</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                </ul>
            </div>
        </div>
    </header>
    <div class="background-image"></div>

    <h2 id="typing-effect"></h2>
    <h1><b>MenuScanOrder.</b></h1>
    <button class="btn btn-start-ordering" aria-label="Start Ordering" onclick="handleOrdering()">Start Ordering</button>
    <a href="./register" class="register-link">Don't have an account?</a>
</body>

<script>
    const texts = ["Welcome to","欢迎光临","में आपका स्वागत है","へようこそ","Bienvenue à","Willkommen bei","환영합니다","Bienvenido a","Добро пожаловать в"];
    let count = 0;
    let index = 0;
    let currentText = '';
    let letter = '';

    (function type() {
    if (count === texts.length) {
        count = 0;
    }
    currentText = texts[count];
    letter = currentText.slice(0, ++index);

    document.getElementById('typing-effect').textContent = letter;
    if (letter.length === currentText.length) {
        if (count < texts.length - 1) {
        setTimeout(() => {
            index = 0;
            count++;
            setTimeout(type, 500); 
        }, 2000); 
        } else {
        setTimeout(() => {
            document.getElementById('typing-effect').textContent = '';
            count = 0;
            index = 0;
            setTimeout(type, 500); 
        }, 2000);
        }
    } else {
        setTimeout(type, 100); 
    }
    })();

</script>

<script>
function handleOrdering() {
    var userRole = <?= json_encode(session()->get('user_role')); ?>;

    if (userRole === 0) {
        window.location.href = 'https://infs3202-14206650.uqcloud.net/menuscanorder/index.php/admin';
    } else if (userRole === 1) {  // Any logged-in user that is not an admin
        window.location.href = 'https://infs3202-14206650.uqcloud.net/menuscanorder/index.php/loggedin';
    } else {  // Not logged in
        window.location.href = 'https://infs3202-14206650.uqcloud.net/menuscanorder/index.php/login';
    }
}
</script>



</html>
