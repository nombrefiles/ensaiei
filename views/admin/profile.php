<?php
$this->layout("theme", []);
?>


        <section class="card">
            <img class="foto-perfil" src="<?= $photo ?>" alt="<?= $name ?>">
            <h1><?= $name ?></h1>
            <p class="arroba">@<?= $username ?></p>
            <h2>Biografia</h2>
            <p class="bio"><?= $bio ?></p>
        </section>

<?php  $this->start("specific-css"); ?>
    <link rel="stylesheet" href="<?= url("assets/web/css/profile.css"); ?>">
<?php $this->end(); ?>

<?php  $this->start("specific-script"); ?>
    <script src="<?= url("assets/web/js/profile.js"); ?>"></script>
<?php $this->end(); ?>