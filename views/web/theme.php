<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>FAQ - Ensaiei</title>
    <link rel="stylesheet" href="../css/about.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700&family=Inter&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= url("assets/web/css/theme.css") ?>">


</head>
<body>

<div class="topnav">
    <div class="topnav-left">
        <img src="design/assets/images/bicolor.png" alt="Ensaiei" class="logo">
    </div>
    <div class="topnav-right">
        <nav>
            <a href="<?=url("/sobre")?>">sobre</a>
            <a href="<?=url("/faqs")?>">faq</a>
            <a href="<?=url("/login")?>">entrar</a>
            <a href="<?=url("/cadastro")?>">cadastro</a>
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
