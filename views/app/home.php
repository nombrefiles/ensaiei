<?php
$this->layout("theme", []);
?>

    <h1>Testando</h1>

<?php $this->start("specific-css"); ?>
    <link rel="stylesheet" href="<?= url("assets/app/css/home.css"); ?>">
<?php $this->end(); ?>

<?php $this->start("specific-script"); ?>
    <script src="<?= url("assets/app/js/home.js"); ?>"></script>
<?php $this->end(); ?>