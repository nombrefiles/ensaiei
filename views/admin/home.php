<?php
$this->layout("theme", []);
?>


<div class="container">

    <div class="stats-grid" id="statsGrid">
        <div class="stat-card">
            <div class="stat-value" id="statTotal">-</div>
            <div class="stat-label">Total de Eventos</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="statPending">-</div>
            <div class="stat-label">Aguardando Aprovação</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="statApproved">-</div>
            <div class="stat-label">Aprovados</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" id="statRejected">-</div>
            <div class="stat-label">Rejeitados</div>
        </div>
    </div>

    <div class="events-section">
        <h2 class="section-title">Eventos Pendentes de Aprovação</h2>
        <div class="events-grid" id="eventsGrid">
            <div class="loading">
                <div class="spinner"></div>
            </div>
        </div>
    </div>
</div>

<?php  $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/web/css/admin.css"); ?>">
<?php $this->end(); ?>

<?php  $this->start("specific-script"); ?>
<script src="<?= url("assets/web/js/admin.js"); ?>"></script>
<?php $this->end(); ?>