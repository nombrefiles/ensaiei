<?php
$this->layout("theme", []);
?>
    <section>
      <h1>Hamilton <span style="font-size: 14px; color: #999;">(742913)</span></h1>
      <div class="subtitle">dirigido por <a href="#">Luísa Borba</a></div>
      <button class="edit-btn">Editar</button>
    </section>

    <section class="section">
      <h2>Descrição</h2>
      <p>Hamilton é um musical que mistura história e hip hop para contar a vida de Alexander Hamilton, um dos fundadores dos EUA. Nesta versão escolar dirigida por Luísa Borba, a obra ganha um olhar brasileiro, jovem e atual, mantendo a força da narrativa original com muita criatividade e energia.</p>
    </section>

    <section class="section">
      <h2>Gêneros</h2>
      <p>musical, biografia, drama, contemporâneo</p>
    </section>

    <section class="section">
      <h2>Atores elencados</h2>
      <p><span>Luísa Borba</span>, <span>Pedro Files</span>, Mariana Meyer, <span>Brenda Procaska</span>, Júlia de Borba</p>
    </section>

    <section class="section">
      <h2>Endereço e data</h2>
      <p>R. Gen. Balbão, 81 - Centro, Charqueadas<br>02/02/2026, 21h</p>
    </section>

    <section class="section">
      <h2>Roteiro</h2>
      <button class="script-btn">Baixar roteiro</button>
    </section>
  </main>
</body>
</html>
<?php  $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/events.css"); ?>">
    <?php $this->end(); ?>

    <?php  $this->start("specific-script"); ?>
<script src="<?= url("assets/web/js/about.js"); ?>"></script>
<?php $this->end(); ?>
