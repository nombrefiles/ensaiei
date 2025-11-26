<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ensaiei</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url("assets/admin/css/theme.css") ?>">

</head>
<body>

<div class="topnav">
    <div class="topnav-left">
        <a href=""><img src="http://localhost/ensaiei-main/design/assets/images/logobranca.png" alt="Ensaiei" class="logo"></a>
    </div>
    <div class="topnav-right">
        <nav>
            <a href="<?=url("admin/perfil")?>">perfil</a>
            <a href="<?=url("admin/eventos")?>">eventos</a>
            <a href="<?=url("admin/sobre")?>">sobre</a>
            <a href="<?=url("admin/faqs")?>">faq</a>
            <a href="<?=url("admin/logout")?>">logout</a>
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
