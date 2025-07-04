<?php
$this->layout("theme", []);
?>

<nav class="top">
      <img src="design/assets/images/logoSolo.png" alt="Logo" width="40">
      <h2>Entrar</h2>
    </nav>
    <div class="form-box">
      <form>
        <label for="username">Username ou e-mail</label>
        <input type="text" id="username" name="username" required>

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Entrar</button>
        <span>Não tem uma conta? <a href="signin.html">Criar uma conta</a></span>
      </form>
    </div>

       <?php  $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/login.css"); ?>">
    <?php $this->end(); ?>

    <?php  $this->start("specific-script"); ?>
<script src="<?= url("assets/web/js/login.js"); ?>"></script>
<?php $this->end(); ?>