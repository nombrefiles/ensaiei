<?php
$this->layout("theme", []);
?>

<div class="verify-container">
    <h1>Verifique seu Email</h1>
    <p class="subtitle">
        Enviamos um código de 6 dígitos para o seu email.
        Digite o código abaixo para ativar sua conta.
    </p>

    <div class="email-display" id="emailDisplay">

    </div>

    <div id="errorMessage" class="error-message"></div>
    <div id="successMessage" class="success-message"></div>

    <form id="verifyForm">
        <div class="code-inputs">
            <input type="text" maxlength="1" class="code-input" id="code1" pattern="[0-9]" required>
            <input type="text" maxlength="1" class="code-input" id="code2" pattern="[0-9]" required>
            <input type="text" maxlength="1" class="code-input" id="code3" pattern="[0-9]" required>
            <input type="text" maxlength="1" class="code-input" id="code4" pattern="[0-9]" required>
            <input type="text" maxlength="1" class="code-input" id="code5" pattern="[0-9]" required>
            <input type="text" maxlength="1" class="code-input" id="code6" pattern="[0-9]" required>
        </div>

        <button type="submit" class="verify-btn" id="verifyBtn">
            Verificar Código
        </button>

        <div class="loading" id="loading">
            <div class="spinner"></div>
        </div>
    </form>

    <div class="actions">
        <button class="link-btn" id="resendBtn">
            Não recebeu o código? Reenviar
        </button>
        <button class="link-btn" id="cancelBtn" style="color: #999;">
            Email não existe? Cancelar registro
        </button>
    </div>
</div>

<?php  $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/email.css"); ?>">
<?php $this->end(); ?>

<?php  $this->start("specific-script"); ?>
<script src="<?= url("assets/web/js/email.js"); ?>"></script>
<?php $this->end(); ?>
