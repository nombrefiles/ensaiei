const API_BASE = "http://localhost/ensaiei-main/api";

document.addEventListener('DOMContentLoaded', function() {
    console.log('Página carregada, carregando eventos...');
    loadEvents();
    setupSearch();
    setupModalEvents();
});

let allEvents = [];

async function loadEvents() {
    const eventsGrid = document.getElementById('eventsGrid');

    eventsGrid.innerHTML = '<div class="loading"><div class="spinner"></div></div>';

    try {
        const response = await fetch(`${API_BASE}/event/`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();
        console.log('Eventos recebidos:', data);

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao carregar eventos');
        }

        let events = [];
        if (Array.isArray(data)) {
            events = data;
        } else if (Array.isArray(data.data)) {
            events = data.data;
        } else if (data.data && typeof data.data === 'object') {
            events = [data.data];
        }

        // Filtrar apenas eventos aprovados
        events = events.filter(event => event.status === 'APPROVED');

        allEvents = events;
        renderEvents(events);

    } catch (error) {
        console.error('Erro ao carregar eventos:', error);
        eventsGrid.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">⚠️</div>
                <h3>Erro ao carregar eventos</h3>
                <p>${error.message}</p>
                <button class="btn-primary" onclick="loadEvents()">Tentar novamente</button>
            </div>
        `;
    }
}

function renderEvents(events) {
    const eventsGrid = document.getElementById('eventsGrid');

    if (!events || events.length === 0) {
        eventsGrid.innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">🎭</div>
                <h3>Nenhum evento disponível</h3>
                <p>Novos eventos serão exibidos aqui em breve!</p>
            </div>
        `;
        return;
    }

    eventsGrid.innerHTML = events.map(event => {
        const eventDate = formatDate(event.startDatetime || event.startDate);

        return `
            <div class="event-card" data-event-id="${event.id}">
                <div class="event-card-image no-photo">🎭</div>
                <div class="event-info">
                    <h3 class="event-title">${event.title || 'Sem título'}</h3>
                    <p class="event-date">${eventDate}</p>
                    <p class="event-location">${event.location || 'Local não informado'}</p>
                </div>
            </div>
        `;
    }).join('');

    // Adicionar event listeners para abrir modal
    document.querySelectorAll('.event-card').forEach(card => {
        card.addEventListener('click', function() {
            const eventId = this.dataset.eventId;
            openEventModal(eventId);
        });
    });

    // Carregar fotos dos eventos
    events.forEach(event => {
        loadEventMainPhoto(event.id);
    });
}

async function loadEventMainPhoto(eventId) {
    try {
        const response = await fetch(`${API_BASE}/event/${eventId}/photos`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            const data = await response.json();
            const photos = data.data || data;

            if (photos && Array.isArray(photos) && photos.length > 0) {
                const mainPhoto = photos.find(p => p.isMain) || photos[0];
                const cardImage = document.querySelector(`[data-event-id="${eventId}"] .event-card-image`);

                if (cardImage) {
                    cardImage.classList.remove('no-photo');
                    cardImage.innerHTML = `<img src="${mainPhoto.photo}" alt="Foto do evento">`;
                }
            }
        }
    } catch (error) {
        console.error('Erro ao carregar foto do evento:', error);
    }
}

function formatDate(dateString) {
    if (!dateString) return 'Data não informada';

    try {
        const date = new Date(dateString);
        return date.toLocaleDateString('pt-BR', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    } catch {
        return dateString;
    }
}

function setupSearch() {
    const searchInput = document.getElementById('searchInput');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            filterEvents(searchTerm);
        });

        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                searchEvents();
            }
        });
    }
}

function filterEvents(searchTerm) {
    if (!searchTerm) {
        renderEvents(allEvents);
        return;
    }

    const filtered = allEvents.filter(event => {
        const title = (event.title || '').toLowerCase();
        const location = (event.location || '').toLowerCase();
        const description = (event.description || '').toLowerCase();

        return title.includes(searchTerm) ||
            location.includes(searchTerm) ||
            description.includes(searchTerm);
    });

    if (filtered.length === 0) {
        document.getElementById('eventsGrid').innerHTML = `
            <div class="empty-state">
                <div class="empty-state-icon">🔍</div>
                <h3>Nenhum evento encontrado</h3>
                <p>Tente pesquisar com outros termos</p>
            </div>
        `;
    } else {
        renderEvents(filtered);
    }
}

function searchEvents() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        const searchTerm = searchInput.value.toLowerCase().trim();
        filterEvents(searchTerm);
    }
}

function setupModalEvents() {
    const modal = document.getElementById('eventModal');

    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeEventModal();
            }
        });
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('eventModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeEventModal();
            }
        }
    });
}

async function openEventModal(eventId) {
    console.log('Abrindo modal para evento:', eventId);

    try {
        const response = await fetch(`${API_BASE}/event/${eventId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao carregar evento');
        }

        const event = data.data || data;

        // Preencher modal
        document.getElementById('modalTitle').textContent = event.title;
        document.getElementById('modalDate').textContent = formatDate(event.startDatetime);
        document.getElementById('modalTime').textContent = formatTime(event.startDatetime, event.endDatetime);
        document.getElementById('modalLocation').textContent = event.location;
        document.getElementById('modalOrganizer').textContent = 'Organizador'; // Você pode buscar o nome do organizador se necessário
        document.getElementById('modalDescription').textContent = event.description;

        // Carregar atrações
        const attractionsResponse = await fetch(`${API_BASE}/attraction/event/${eventId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (attractionsResponse.ok) {
            const attractionsData = await attractionsResponse.json();
            const attractions = attractionsData.data?.event?.attractions || [];

            const attractionsContainer = document.getElementById('modalAttractions');

            if (attractions && attractions.length > 0) {
                attractionsContainer.innerHTML = attractions.map(attraction => `
                    <div class="attraction-item">
                        <span class="attraction-name">${attraction.name}</span>
                        <span class="attraction-type">${translateAttractionType(attraction.type)}</span>
                    </div>
                `).join('');
            } else {
                attractionsContainer.innerHTML = '<p style="color: #999;">Nenhuma atração cadastrada</p>';
            }
        }

        // Mostrar modal
        const modal = document.getElementById('eventModal');
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

    } catch (error) {
        console.error('Erro ao abrir modal:', error);
        alert('Erro ao carregar detalhes do evento: ' + error.message);
    }
}

function formatTime(startDateTime, endDateTime) {
    try {
        const start = new Date(startDateTime);
        const end = new Date(endDateTime);
        return `das ${start.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })} às ${end.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;
    } catch {
        return 'Horário não informado';
    }
}

function translateAttractionType(type) {
    const types = {
        'MUSIC': 'Música',
        'VISUAL': 'Visual',
        'THEATER': 'Teatro',
        'DANCE': 'Dança',
        'CINEMA': 'Cinema',
        'OTHER': 'Outro'
    };
    return types[type] || type;
}

function closeEventModal() {
    const modal = document.getElementById('eventModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function registerForEvent() {
    alert('Funcionalidade de inscrição será implementada em breve!');
    closeEventModal();
}