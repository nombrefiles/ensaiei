<?php
$this->layout("theme", []);
?>

    <aside class="menu-lateral">
        <h3>Suas peças</h3>
        <ul>
            <li>Hamlet</li>
            <li>Romeu e Julieta</li>
            <li>O Auto da Compadecida</li>
            <li>Antígona</li>
            <li>As Vozes do Portão</li>
            <li class="ativo">Hamilton</li>
            <li>Mean Girls</li>
        </ul>
    </aside>
    <section class="conteudo">
        <h1>Hamilton <span class="codigo">(742913)</span></h1>
        <p class="direcao">dirigido por <a href="#">Luísa Borba</a></p>
        <button class="editar-btn">Editar</button>

        <h2>Descrição</h2>
        <p class="descricao">
            Hamilton é um musical que mistura história e hip hop para contar a vida de Alexander Hamilton, um dos fundadores dos EUA.
            Nesta versão escolar dirigida por Luísa Borba, a obra ganha um olhar brasileiro, jovem e atual, mantendo a força da narrativa original com muita criatividade e energia.
        </p>

        <h2>Gêneros</h2>
        <p>musical, biografia, drama, contemporâneo</p>

        <h2>Atores elencados</h2>
        <p>
            <strong>Luísa Borba</strong>, <strong>Pedro Files</strong>, Mariana Meyer, <strong>Brenda Procaska</strong>, Júlia de Borba
        </p>

        <h2>Endereço e data</h2>
        <p>R. Gen. Balbão, 81 - Centro, Charqueadas<br>02/02/2026, 21h</p>

        <h2>Roteiro</h2>
        <button class="baixar-btn">Baixar roteiro</button>
    </section>


<?php  $this->start("specific-css"); ?>
    <link rel="stylesheet" href="<?= url("assets/web/css/events.css"); ?>">
<?php $this->end(); ?>

<?php  $this->start("specific-script"); ?>
    <script src="<?= url("assets/web/js/events.js"); ?>"></script>
<?php $this->end(); ?>