<?php
$this->layout("theme", []);
?>

    <?php  $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/about.css"); ?>">
    <?php $this->end(); ?>

    <?php  $this->start("specific-script"); ?>
<script src="<?= url("assets/web/js/about.js"); ?>"></script>
<?php $this->end(); ?>