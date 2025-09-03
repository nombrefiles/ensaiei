<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ensaiei</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url("assets/web/css/theme.css") ?>">
    
</head>
<body>

<div class="topnav">
    <div class="topnav-left">
        <a href="hi"><img src="http://localhost/ensaiei-main/design/assets/images/bicolor.png" alt="Ensaiei" class="logo"></a>
    </div>
    <div class="topnav-right">
        <nav>
            <a href="<?=url("app/perfil")?>">perfil</a>
            <a href="<?=url("app/eventos")?>">eventos</a>
            <a href="<?=url("app/sobre")?>">sobre</a>
            <a href="<?=url("app/faqs")?>">faq</a>
            <a href="<?=url("app/bye")?>">logout</a>
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
