const API_BASE = "http://localhost/ensaiei-main/api";
let currentEvents = [];
let currentEventId = null;

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    loadEvents();
    setupEventListeners();
});

// Verificar autenticação
function checkAuth() {
    const token = localStorage.getItem('token');
    if (!token) {
        alert('Você precisa estar logado para acessar esta página.');
        window.location.href = '/ensaiei-main/login';
        return;
    }
}

// Configurar event listeners
function setupEventListeners() {
    const createBtn = document.getElementById('btnCreateEvent');
    const modal = document.getElementById('eventModal');
    const closeBtn = document.getElementById('modalClose');
    const cancelBtn = document.getElementById('btnCancel');
    const eventForm = document.getElementById('eventForm');

    if (createBtn) {
        createBtn.addEventListener('click', openCreateModal);
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', closeModal);
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', closeModal);
    }

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModal();
            }
        });
    }

    if (eventForm) {
        eventForm.addEventListener('submit', handleSubmit);
    }

    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
        }
    });
}

// Carregar eventos
async function loadEvents() {
    const container = document.getElementById('eventsGrid');
    const token = localStorage.getItem('token');

    container.innerHTML = '<div class="loading"><div class="spinner"></div></div>';

    try {
        const response = await fetch(`${API_BASE}/event/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'token': token
            }
        });

        const data = await response.json();
        console.log('Resposta da API:', data);

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao carregar eventos');
        }

        // A API pode retornar data.data ou diretamente um array
        let events = [];
        if (Array.isArray(data)) {
            events = data;
        } else if (Array.isArray(data.data)) {
            events = data.data;
        } else if (data.data && typeof data.data === 'object') {
            // Se for um objeto único, transforma em array
            events = [data.data];
        }

        console.log('Eventos processados:', events);
        currentEvents = events;
        renderEvents(events);

    } catch (error) {
        console.error('Erro ao carregar eventos:', error);
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">⚠️</div>
                <h3>Erro ao carregar eventos</h3>
                <p>${error.message}</p>
                <button class="btn-primary" onclick="loadEvents()">Tentar novamente</button>
            </div>
        `;
    }
}

// Renderizar eventos
function renderEvents(events) {
    const container = document.getElementById('eventsGrid');

    console.log('Renderizando eventos:', events);

    if (!events || !Array.isArray(events) || events.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">🎭</div>
                <h3>Nenhum evento criado ainda</h3>
                <p>Comece criando seu primeiro evento!</p>
                <button class="btn-primary" onclick="openCreateModal()">Criar primeiro evento</button>
            </div>
        `;
        return;
    }

    container.innerHTML = events.map(event => {
        console.log('Processando evento:', event);

        // Formatar data de forma segura
        let dateDisplay = 'Data não informada';
        if (event.startDatetime) {
            dateDisplay = formatDate(event.startDatetime);
        } else if (event.startDate) {
            dateDisplay = event.startDate;
        }

        return `
            <div class="event-card">
                <div class="event-card-image">
                    🎭
                </div>
                <div class="event-card-body">
                    <h3 class="event-card-title">${event.title || 'Sem título'}</h3>
                    <div class="event-card-info">
                        <div class="event-info-item">
                            <strong>📅</strong>
                            <span>${dateDisplay}</span>
                        </div>
                        <div class="event-info-item">
                            <strong>📍</strong>
                            <span>${event.location || 'Local não informado'}</span>
                        </div>
                    </div>
                    <p class="event-card-description">${event.description || 'Sem descrição'}</p>
                    <div class="event-card-actions">
                        <button class="btn-view" onclick="viewEvent(${event.id})">Ver detalhes</button>
                        <button class="btn-edit" onclick="openEditModal(${event.id})">Editar</button>
                        <button class="btn-delete" onclick="deleteEvent(${event.id})">Excluir</button>
                    </div>
                </div>
            </div>
        `;
    }).join('');
}

// Abrir modal de criação
function openCreateModal() {
    currentEventId = null;
    document.getElementById('modalTitle').textContent = 'Criar Novo Evento';
    document.getElementById('eventForm').reset();
    document.getElementById('eventModal').classList.add('active');
}

// Abrir modal de edição
async function openEditModal(eventId) {
    currentEventId = eventId;
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_BASE}/event/${eventId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'token': token
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao carregar evento');
        }

        const event = data.data || data;

        // Preencher formulário
        document.getElementById('eventTitle').value = event.title || '';
        document.getElementById('eventDescription').value = event.description || '';
        document.getElementById('eventLocation').value = event.location || '';

        // Formatar datas
        if (event.startDate && event.startTime) {
            const [day, month, year] = event.startDate.split('/');
            document.getElementById('eventStartDate').value = `${year}-${month}-${day}`;
            document.getElementById('eventStartTime').value = event.startTime;
        }

        if (event.endDate && event.endTime) {
            const [day, month, year] = event.endDate.split('/');
            document.getElementById('eventEndDate').value = `${year}-${month}-${day}`;
            document.getElementById('eventEndTime').value = event.endTime;
        }

        document.getElementById('modalTitle').textContent = 'Editar Evento';
        document.getElementById('eventModal').classList.add('active');

    } catch (error) {
        console.error('Erro ao carregar evento:', error);
        alert('Erro ao carregar dados do evento: ' + error.message);
    }
}

// Fechar modal
function closeModal() {
    document.getElementById('eventModal').classList.remove('active');
    document.getElementById('eventForm').reset();
    currentEventId = null;
}

// Submeter formulário
async function handleSubmit(e) {
    e.preventDefault();

    const token = localStorage.getItem('token');

    // Pegar valores diretamente dos inputs
    const title = document.getElementById('eventTitle').value.trim();
    const description = document.getElementById('eventDescription').value.trim();
    const location = document.getElementById('eventLocation').value.trim();
    const startDate = document.getElementById('eventStartDate').value;
    const startTime = document.getElementById('eventStartTime').value;
    const endDate = document.getElementById('eventEndDate').value;
    const endTime = document.getElementById('eventEndTime').value;

    // Validação manual
    if (!title || !description || !location || !startDate || !startTime || !endDate || !endTime) {
        alert('Por favor, preencha todos os campos obrigatórios');
        return;
    }

    // Converter datas do formato YYYY-MM-DD para DD/MM/YYYY
    const [startYear, startMonth, startDay] = startDate.split('-');
    const [endYear, endMonth, endDay] = endDate.split('-');

    const eventData = {
        title: title,
        description: description,
        location: location,
        startDate: `${startDay}/${startMonth}/${startYear}`,
        startTime: startTime,
        endDate: `${endDay}/${endMonth}/${endYear}`,
        endTime: endTime
    };

    console.log('Enviando dados:', eventData);

    try {
        const url = currentEventId
            ? `${API_BASE}/event/update/${currentEventId}`
            : `${API_BASE}/event/add`;

        const method = currentEventId ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'token': token
            },
            body: JSON.stringify(eventData)
        });

        const data = await response.json();
        console.log('Resposta da API:', data);

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao salvar evento');
        }

        alert(currentEventId ? 'Evento atualizado com sucesso!' : 'Evento criado com sucesso!');
        closeModal();
        loadEvents();

    } catch (error) {
        console.error('Erro ao salvar evento:', error);
        alert('Erro ao salvar evento: ' + error.message);
    }
}

// Ver detalhes do evento
function viewEvent(eventId) {
    window.location.href = `/ensaiei-main/app/eventos/${eventId}`;
}

// Deletar evento
async function deleteEvent(eventId) {
    if (!confirm('Tem certeza que deseja excluir este evento?')) {
        return;
    }

    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_BASE}/event/delete/${eventId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'token': token
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao excluir evento');
        }

        alert('Evento excluído com sucesso!');
        loadEvents();

    } catch (error) {
        console.error('Erro ao excluir evento:', error);
        alert('Erro ao excluir evento: ' + error.message);
    }
}

// Formatar data
function formatDate(dateString) {
    if (!dateString) return 'Data não informada';

    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch {
        return dateString;
    }
}