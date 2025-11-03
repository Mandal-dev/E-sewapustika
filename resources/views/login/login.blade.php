<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Police Login</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        body {
            height: 100vh;
            display: flex;
            margin: 0;
        }

        .left-side {
            background: linear-gradient(135deg, #ff9f43, #ff7b00);
            width: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .right-side {
            width: 50%;
            display: none;
        }

        @media (min-width: 768px) {
            .right-side {
                display: block;
            }
        }

        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            padding: 40px;
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        .login-card img.logo {
            width: 80px;
            margin-bottom: 15px;
        }

        .btn-orange {
            background-color: #ff7b00;
            color: white;
            font-weight: 500;
        }

        .btn-orange:hover {
            background-color: #ff6a00;
            color: #fff;
        }

        .form-control {
            border-radius: 8px;
        }

        .object-fit-cover {
            object-fit: cover;
        }
    </style>
</head>

<body>



    <!-- Right Side -->
    <div class="right-side" >>
      @yield('data')
    </div>
    <div class="left-side">
        <img src="{{ asset('img/police image.png') }}" alt="Police Officer" class="w-100 h-100 object-fit-cover">
    </div>
</body>
</html>
