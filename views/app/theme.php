<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>FAQ - Ensaiei</title>
    <link rel="stylesheet" href="../css/about.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url("assets/web/css/theme.css") ?>">

<!--// admin do evento -> lista as inscriçoes -> cadastro admin -> perfil do admin-->
</head>
<body>

<div class="topnav">
    <div class="topnav-left">
        <img src="http://localhost/ensaiei-main/design/assets/images/bicolor.png" alt="Ensaiei" class="logo">
    </div>
    <div class="topnav-right">
        <nav>
            <a href="<?=url("app/perfil")?>">perfil</a>
            <a href="<?=url("app/eventos")?>">eventos</a>
            <a href="<?=url("app/sobre")?>">sobre</a>
            <a href="<?=url("app/faqs")?>">faq</a>
            <a href="<?=url("app/logout")?>">logout</a>
        </nav>
    </div>
</div>

<main class="container">
    <?= $this->section("content") ?>
</main>

<?php if ($this->section("specific-css")): ?>
    <?= $this->section("specific-css"); ?>
<?php endif; ?>

<?php if ($this->section("specific-script")): ?>
    <?= $this->section("specific-script"); ?>
<?php endif; ?>
</body>
</html>
