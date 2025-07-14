<?php
$this->layout("theme", []);
?>

   <div class="faq-section">
        <h2>Sobre nós</h2>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">Desenvolvedores<span class="arrow">▼</span></div>
            <div class="faq-content">
                Oi! Somos Pedro Files e Luísa Borba, estudantes do Ensino Técnico Integrado de Informática, no IFSul Campus Charqueadas. Atualmente estamos no terceiro ano do curso, e desenvolvendo o projeto Ensaiei, programado em PHP e JavaScript.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">O projeto<span class="arrow">▼</span></div>
            <div class="faq-content">
                O projeto Ensaiei é um software de gerenciador de peças de teatro, principalmente para grupos de teatro independentes, oferecendi funcionalidades como agendamento de ensaios, divisão de falas, controle de figurino e cronograma de apresentações.
            </div>
        </div>

        <div class="faq-item">
            <div class="faq-title" onclick="toggleFAQ(this)">Redes Sociais<span class="arrow">▼</span></div>
            <div class="faq-content">
                Instagram -- @ensaiei
                Facebook -- Ensaiei
                Twitter -- @ensaiei
                LinkedIn -- @ensaiei
                GitHub -- @ensaiei
            </div>
        </div>
    </div>

    <?php  $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/about.css"); ?>">
    <?php $this->end(); ?>

    <?php  $this->start("specific-script"); ?>
<script src="<?= url("assets/web/js/about.js"); ?>"></script>
<?php $this->end(); ?>