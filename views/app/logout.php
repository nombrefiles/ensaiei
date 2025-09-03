<?php
$this->layout("theme", []);
?>

<h1>Redirecionando-te</h1>

<?php $this->start("specific-script"); ?>
    <script src="<?= url("assets/app/js/logout.js"); ?>"></script>
<?php $this->end(); ?>