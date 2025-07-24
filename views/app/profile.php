<?php
$this->layout("theme", []);
?>

    <section class="card">
        <img class="foto-perfil" src="" alt="Foto de perfil">
        <h1>Carregando...</h1>
        <p class="arroba">@carregando</p>
        <h2>Biografia</h2>
        <p class="bio">Carregando biografia...</p>
    </section>


<?php  $this->start("specific-css"); ?>
    <link rel="stylesheet" href="<?= url("assets/web/css/profile.css"); ?>">
<?php $this->end(); ?>

<?php  $this->start("specific-script"); ?>
    <script src="<?= url("assets/app/js/profile.js"); ?>"></script>
<?php $this->end(); ?>