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
        font-family: 'Poppins', sans-serif;
        background-color: #f8f9fa;
    }

    /* Left Section (Login Form Section) */
    .right-side {

        width: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    /* Right Section (Image or Illustration Section) */
    .right-side {
        width: 50%;
        display: none;
        justify-content: center;
        align-items: center;
        background-color: #fff;
        overflow: hidden;
        position: relative;
    }

    .right-side img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    @media (min-width: 768px) {
        .right-side {
            display: flex;
        }
    }

    /* Login Card */
    .login-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        padding: 40px;
        width: 100%;
        max-width: 400px;
        text-align: center;
        transition: all 0.3s ease;
    }

    .login-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }

    /* Logo */
    .login-card img.logo {
        width: 80px;
        margin-bottom: 15px;
    }

    /* Heading */
    .login-card h3 {
        margin-bottom: 25px;
        color: #333;
        font-weight: 600;
    }

    /* Input Fields */
    .form-control {
        border-radius: 8px;
        padding: 10px 12px;
        font-size: 15px;
    }

    .form-control:focus {
        border-color: #ff7b00;
        box-shadow: 0 0 0 0.2rem rgba(255, 123, 0, 0.25);
    }

    /* Buttons */
    .btn-orange {
        background-color: #ff7b00;
        color: #fff;
        font-weight: 500;
        border-radius: 8px;
        padding: 10px;
        width: 100%;
        border: none;
        transition: 0.3s;
    }

    .btn-orange:hover {
        background-color: #ff6a00;
        color: #fff;
    }

    /* Image Fit Helper */
    .object-fit-cover {
        object-fit: cover;
    }

    /* Responsive Adjustments */
    @media (max-width: 767px) {
        .left-side {
            width: 100%;
            padding: 40px 20px;
        }
    }

    /* Optional: Slight margin for right side image on large screens */
    .right-side {
        margin-top: 150px !important;
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
