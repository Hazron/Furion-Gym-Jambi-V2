<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Furion Gym</title>

    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            background: #000;
            color: white;
        }

        /* ---------------- NAVBAR ---------------- */
        .navbar {
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            padding: 14px 35px; /* dulu 18px 60px */
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
            background: rgba(0, 32, 128, 0.92);
            backdrop-filter: blur(6px);
        }

        .navbar .logo {
            font-size: 26px;
            font-weight: 800;
            letter-spacing: 1px;
        }

        .navbar .menu {
            display: flex;
            gap: 32px;
        }

        .navbar .menu a {
            text-decoration: none;
            color: white;
            font-size: 15px;
            font-weight: 500;
        }

        .navbar .menu a:hover {
            color: #FFD500;
        }

        .navbar .btn-quote {
            background: white;
            color: #0033cc;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: bold;
            text-decoration: none;
        }

        .navbar .btn-quote:hover {
            background: #FFD500;
            color: #000;
        }

        /* ---------------- HERO SECTION ---------------- */
        .hero {
            position: relative;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }

        .hero img.bg {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.45;
            position: absolute;
            top: 0;
            left: 0;
        }

        .hero .blue-gradient {
            position: absolute;
            top: 0;
            width: 100%;
            height: 220px;
            background: linear-gradient(to bottom, rgba(0, 80, 255, 0.9), transparent);
        }

        .hero .content {
            position: relative;
            padding: 140px 80px;
            max-width: 700px;
        }

        .hero h1 {
            font-size: 64px;
            font-weight: 900;
            line-height: 1.1;
            margin: 0;
        }

        .yellow {
            background: #ffd500;
            color: #000;
            padding: 5px 10px;
            display: inline-block;
        }

        .blue {
            color: #3fa9ff;
        }

        .cta {
            margin-top: 30px;
        }

        .cta a {
            background: white;
            color: #0033cc;
            padding: 12px 22px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0px 4px 10px rgba(255,255,255,0.3);
        }

        .nav-arrows {
            position: absolute;
            right: 40px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            gap: 12px;
        }

        .arrow {
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.7);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 20px;
            color: #0033cc;
            font-weight: bold;
        }

        .arrow:hover {
            background: white;
        }
        @media (max-width: 768px) {
    .hero {
        flex-direction: column;
        text-align: center;
    }

    .hero img {
        width: 100%;
        height: auto;
    }
}

    </style>
</head>

<body>

<!-- ███████████████ NAVBAR ██████████████ -->
<nav class="navbar">
    <div class="logo">FURION GYM</div>

    <div class="menu">
        <a href="#">Home</a>
        <a href="#">About</a>
        <a href="#">Services</a>
        <a href="#">Projects</a>
        <a href="#">Blogs</a>
        <a href="#">Contact</a>
    </div>

@auth
    <div class="nav-right">
        <a href="{{ url('/dashboard') }}" class="btn-quote">Dashboard</a>
    </div>
@else
    <div class="nav-right">
        <a href="{{ url('/login') }}" class="btn-quote">Login</a>
    </div>
@endauth

</nav>



<!-- ███████████████ HERO SECTION ██████████████ -->
<section class="hero">
<img class="bg" src="{{ asset('assets/bggym.png') }}" alt="Gym Background">


    <div class="blue-gradient"></div>

    <div class="content">
        <h1>
            <span class="yellow">PUSH</span><br>
            YOURSELF<br>
            <span class="blue">BECAUSE</span><br>
            NO ONE<br>
            <span class="yellow">ELSE WILL</span>
        </h1>

        <div class="cta">
            <a href="#membership">CEK KEANGGOTAAN</a>
        </div>
    </div>

    <div class="nav-arrows">
        <div class="arrow">◀</div>
        <div class="arrow">▶</div>
    </div>

</section>

</body>
</html>
