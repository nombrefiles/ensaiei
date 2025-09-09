<?php
$this->layout("theme", []);
?>

<nav class="top">
    <h2>Trocar senha</h2>
</nav>

<div class="form-box">
    <form id="password-form">
        <label for="password">Senha atual</label>
        <input type="password" id="oldPassword" name="oldPassword" required>

        <label for="password">Nova senha</label>
        <input type="password" id="newPassword" name="newPassword" required>

        <button type="submit">Trocar senha</button>
    </form>
</div>

<?php $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/login.css"); ?>">
<?php $this->end(); ?>

<?php $this->start("specific-script"); ?>
<script src="<?= url("assets/app/js/password.js"); ?>"></script>
<?php $this->end(); ?>
