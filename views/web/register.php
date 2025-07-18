<?php
$this->layout("theme", []);
?>
<nav class="top">
      <img src="design/assets/images/logoSolo.png" alt="Logo" width="40">
      <h2>Criar conta</h2>
    </nav>
    <div class="form-box">
      <form method="POST" id="form-register">
        <label for="nome">Nome</label>
        <input type="text" id="nome" name="nome" required>

        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" required>

        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" required>

        <label for="confirmar">Confirmar senha</label>
        <input type="password" id="confirmar" name="confirmar" required>

        <button type="submit">Cadastrar-se</button>
        <span>Já tem uma conta? <a href="login.html">Fazer log-in</a></span>
      </form>
    </div>

   <?php  $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/register.css"); ?>">
    <?php $this->end(); ?>

    <?php  $this->start("specific-script"); ?>
<script src="<?= url("assets/web/js/register.js"); ?>"></script>
<?php $this->end(); ?>