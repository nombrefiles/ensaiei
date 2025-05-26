<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Perfil - Ensaiei</title>
    <style>
        body {
            margin: 0;
            font-family: 'Didot', serif;
            background-color: #f9f9f9;
            overflow-x: hidden;
        }

        @font-face {
            font-family: 'playfull-italic';
            src: url("http://localhost/ENSAIEI/design/assets/PlayfairDisplay-ExtraBoldItalic.ttf") format("truetype");
        }

        @font-face {
            font-family: 'playfull';
            src: url("http://localhost/ENSAIEI/design/assets/PlayfairDisplay-SemiBold.ttf") format("truetype");
        }

        @font-face {
            font-family: 'jakarta';
            src: url("http://localhost/ENSAIEI/design/assets/PlusJakartaSans-Regular.ttf") format("truetype");
        }

        @font-face {
            font-family: 'jakarta-bold';
            src: url("http://localhost/ENSAIEI/design/assets/PlusJakartaSans-SemiBold.ttf") format("truetype");
        }

        .topnav {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 40px;
            background-color: white;
            border-bottom: 1px solid #ccc;
            position: sticky;
            top: 0;
            z-index: 1000;
            box-sizing: border-box;
            flex-wrap: wrap;
            overflow-x: hidden;
        }

        .topnav-left .logo {
            width: 150px;
            display: flex;
            align-items: center;
            flex-shrink: 0;
        }

        .topnav-right {
            display: flex;
            align-items: center;
            gap: 30px;
            flex-wrap: wrap;
        }

        .add-button {
            background-color: #eee;
            border-radius: 4px;
            width: auto;
            height: 30px;
            cursor: pointer;
            font-family: 'jakarta', sans-serif;
            padding: 5px;
            font-size: 12px;
            transition: background-color 0.3s ease;
            text-align: center;
            line-height: 30px;
            border: #ccc 0.5px solid;
        }

        .add-button:hover {
            background-color: #ddd;
        }

        .topnav-right nav {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .topnav-right nav a {
            text-decoration: none;
            font-size: 1.2rem;
            color: #222;
            font-family: 'jakarta', sans-serif;
        }

        .topnav-right nav a:nth-child(2) {
            color: #e91e63;
            font-weight: bold;
            font-family: 'jakarta-bold', sans-serif;
        }

        .topnav-right nav a:hover {
            color: #e91e63;
        }


        main {
            display: flex;
            justify-content: center;
            padding: 60px 20px;
        }

        .card {
            background: #fff;
            border-radius: 12px;
            padding: 40px;
            max-width: 400px;
            width: 100%;
            text-align: center;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }

        .foto-perfil {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #e5005a;
            margin-bottom: 20px;
        }

        h1 {
            margin: 0;
            font-size: 1.6rem;
            font-family: 'playfull', serif;
            color: #222;
            margin-bottom: 10px;
            font-weight: normal;
            line-height: 1.2;
        }

        .arroba {
            color: #e5005a;
            font-size: 0.9rem;
            margin: 5px 0;
            font-family: jakarta, sans-serif;
        }

        h2 {
            font-size: larger;
            margin-top: 30px;
            margin-bottom: 10px;
            font-family: 'playfull', serif;

        }

        .bio {
            font-size: 0.9rem;
            color: #333;
            margin-bottom: 30px;
            font-family: jakarta, sans-serif;
        }
    </style>
</head>
<body>
<div class="topnav">
    <div class="topnav-left">
        <img src="http://localhost/ENSAIEI/design/assets/images/bicolor.png" alt="Ensaiei" class="logo">
    </div>
    <div class="topnav-right">
        <button class="add-button"><a href="http://localhost/ENSAIEI/design/html/update.html">editar perfil</a></button>
        <nav>
            <a href="plays.html">peças</a>
            <a href="profile.html">perfil</a>
            <a href="about.html">sobre</a>
            <a href="faq.html">faq</a>
        </nav>
    </div>
</div>

<main>
    <section class="card">
        <img class="foto-perfil" src="<?= $photo ?>" alt="<?= $name ?>">
        <h1><?= $name ?></h1>
        <p class="arroba">@<?= $username ?></p>
        <h2>Biografia</h2>
        <p class="bio"><?= $bio ?></p>
    </section>
</main>
</body>
</html>
