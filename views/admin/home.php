<?php
$this->layout("theme", []);
?>

    <div class="container">
        <div class="header">
            <h1>Painel Administrativo</h1>
            <p style="color: #999;">Gerencie os eventos da plataforma</p>
        </div>

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

<?php $this->start("specific-css"); ?>
    <link rel="stylesheet" href="<?= url("assets/admin/css/admin.css"); ?>">
<?php $this->end(); ?>

<?php $this->start("specific-script"); ?>
    <script src="<?= url("assets/admin/js/admin.js"); ?>"></script>
<?php $this->end(); ?>