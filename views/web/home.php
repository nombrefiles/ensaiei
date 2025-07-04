<?php
$this->layout("theme", []);
?>



    <p>De roteiros a aplausos, <span class="tudo">tudo</span> sob controle.</p>
    <div class="btn">
        <a href="/signin.html"><button>Entrar</button></a>
        <a href="/login.html"><button>Cadastre-se</button></a>
    </div>

<img class="bg" src="design/assets/images/cover.png" alt="">


 <?php  $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/home.css"); ?>">
    <?php $this->end(); ?>

    <?php  $this->start("specific-script"); ?>
<script src="<?= url("assets/web/js/home.js"); ?>"></script>
<?php $this->end(); ?>