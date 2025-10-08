<?php
$this->layout("theme", []);
?>

<nav class="top">
    <img src="design/assets/images/logoSolo.png" alt="Logo" width="40">
    <h2>Esqueci minha senha</h2>
</nav>

<div class="form-box">
    <form id="login-form">
        <label for="email">Insira o e-mail</label>
        <input type="text" id="user" name="user" required>

        <button type="submit">Prosseguir</button>
        <span class="error"></span>
    </form>
</div>

<?php $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/login.css"); ?>">
<?php $this->end(); ?>

<?php $this->start("specific-script"); ?>
<script src="<?= url("assets/web/js/pick.js"); ?>"></script>
<?php $this->end(); ?>
