<?php
$this->layout("theme", []);
?>

<div class="home-container">
    <div class="search-section">
        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Pesquisar eventos..." class="search-input">
            <button class="search-btn" onclick="searchEvents()"><img src="../design/assets/images/lupinha.png" width="20px"></button>
        </div>
    </div>

    <div class="events-grid" id="eventsGrid">
        <div class="event-card" onclick="openEventModal(1)">
            <img src="../design/assets/images/saraulogo.png" alt="Evento" class="event-image">
            <div class="event-info">
                <h3 class="event-title">Sarau Cultural do IFSul</h3>
                <p class="event-date">15 de Outubro de 2025</p>
                <p class="event-location">R. Gen. Balbão, 81 - Centro, Charqueadas - RS, 96745-000<br>Defronte ao auditório</p>
            </div>
        </div>

        <div class="event-card" onclick="openEventModal(2)">
            <img src="../design/assets/images/piano.jpeg" alt="Evento" class="event-image">
            <div class="event-info">
                <h3 class="event-title">Concerto de Piano</h3>
                <p class="event-date">22 de Outubro, 2025</p>
                <p class="event-location">Teatro Municipal - Rio de Janeiro</p>
            </div>
        </div>

        <div class="event-card" onclick="openEventModal(3)">
            <img src="../design/assets/images/contemporanea.jpg" alt="Evento" class="event-image">
            <div class="event-info">
                <h3 class="event-title">Festival de Arte Contemporânea</h3>
                <p class="event-date">28 de Outubro, 2025</p>
                <p class="event-location">Museu de Arte - Belo Horizonte</p>
            </div>
        </div>

        <div class="event-card" onclick="openEventModal(4)">
            <img src="../design/assets/images/ux.jpg" alt="Evento" class="event-image">
            <div class="event-info">
                <h3 class="event-title">Palestra sobre Design UX</h3>
                <p class="event-date">5 de Novembro, 2025</p>
                <p class="event-location">Auditório Tech - Porto Alegre</p>
            </div>
        </div>

        <div class="event-card" onclick="openEventModal(5)">
            <img src="../design/assets/images/pintura.jpeg" alt="Evento" class="event-image">
            <div class="event-info">
                <h3 class="event-title">Exposição de Pintura</h3>
                <p class="event-date">10 de Novembro, 2025</p>
                <p class="event-location">Galeria Central - Salvador</p>
            </div>
        </div>

        <div class="event-card" onclick="openEventModal(6)">
            <img src="../design/assets/images/jazz.webp" alt="Evento" class="event-image">
            <div class="event-info">
                <h3 class="event-title">Show de Jazz</h3>
                <p class="event-date">18 de Novembro, 2025</p>
                <p class="event-location">Blue Note - Brasília</p>
            </div>
        </div>
    </div>

    <div class="no-results hidden" id="noResults">
        <p>Nenhum evento encontrado para sua pesquisa.</p>
    </div>
</div>

<div class="modal hidden" id="eventModal">
    <div class="modal-content event-modal-content">
        <div class="modal-header">
            <h3 id="modalTitle">Nome do Evento</h3>
            <button class="close-btn" onclick="closeEventModal()">×</button>
        </div>
        <div class="modal-body">
            <div class="event-details">
                <div class="detail-item"><strong>▪ Data:</strong> <span id="modalDate"></span></div>
                <div class="detail-item"><strong>▪ Local:</strong> <span id="modalLocation"></span></div>
                <div class="detail-item"><strong>▪ Horário:</strong> <span id="modalTime"></span></div>
                <div class="detail-item"><strong>▪ Organizador:</strong> <span id="modalOrganizer"></span></div>
                <div class="detail-item"><strong>▪ Atrações:</strong>
                    <div id="modalAttractions"></div>
                </div>
                <div class="detail-item description">
                    <strong>▪ Descrição:</strong>
                    <p id="modalDescription"></p>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-pink full-width" onclick="registerForEvent()">
                Inscrever-se no Evento
            </button>
        </div>
    </div>
</div>

<?php $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/app/css/home.css"); ?>">
<?php $this->end(); ?>

<?php $this->start("specific-script"); ?>
<script src="<?= url("assets/app/js/home.js"); ?>"></script>
<?php $this->end(); ?>
