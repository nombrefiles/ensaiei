<?php
$this->layout("theme", []);
?>

<div class="events-container">
    <div class="events-header">
        <h1>Meus Eventos</h1>
        <button class="btn-create-event" id="btnCreateEvent">
            <span>+</span> Criar Evento
        </button>
    </div>

    <div class="events-grid" id="eventsGrid">
        <div class="loading">
            <div class="spinner"></div>
        </div>
    </div>
</div>

<div class="modal" id="eventModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Criar Novo Evento</h2>
            <button class="modal-close" id="modalClose">&times;</button>
        </div>

        <form id="eventForm">
            <div class="modal-body">
                <div class="form-group">
                    <label for="eventTitle">Título do Evento *</label>
                    <input
                            type="text"
                            id="eventTitle"
                            name="title"
                            required
                            placeholder="Ex: Festival de Teatro de Rua"
                    >
                </div>

                <div class="form-group">
                    <label for="eventDescription">Descrição *</label>
                    <textarea
                            id="eventDescription"
                            name="description"
                            required
                            placeholder="Descreva os detalhes do evento..."
                    ></textarea>
                </div>

                <div class="form-group">
                    <label for="eventLocation">Local *</label>
                    <input
                            type="text"
                            id="eventLocation"
                            name="location"
                            required
                            placeholder="Ex: Teatro Municipal de Porto Alegre"
                    >
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="eventStartDate">Data de Início *</label>
                        <input
                                type="date"
                                id="eventStartDate"
                                name="startDate"
                                required
                        >
                    </div>

                    <div class="form-group">
                        <label for="eventStartTime">Horário de Início *</label>
                        <input
                                type="time"
                                id="eventStartTime"
                                name="startTime"
                                required
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="eventEndDate">Data de Término *</label>
                        <input
                                type="date"
                                id="eventEndDate"
                                name="endDate"
                                required
                        >
                    </div>

                    <div class="form-group">
                        <label for="eventEndTime">Horário de Término *</label>
                        <input
                                type="time"
                                id="eventEndTime"
                                name="endTime"
                                required
                        >
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" id="btnCancel">Cancelar</button>
                <button type="submit" class="btn-primary">Salvar Evento</button>
            </div>
        </form>
    </div>
</div>

<?php $this->start("specific-css"); ?>
<link rel="stylesheet" href="<?= url("assets/app/css/events.css"); ?>">
<?php $this->end(); ?>

<?php $this->start("specific-script"); ?>
<script src="<?= url("assets/app/js/events.js"); ?>"></script>
<?php $this->end(); ?>
