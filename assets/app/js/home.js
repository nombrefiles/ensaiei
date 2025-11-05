const eventsData = {
    1: {
        title: "Workshop de Fotografia",
        description: "Um workshop completo sobre técnicas avançadas de fotografia, cobrindo desde conceitos básicos até técnicas profissionais. Ideal para iniciantes e fotógrafos intermediários que desejam aprimorar suas habilidades. Incluído equipamentos para prática e certificado de participação.",
        location: "Centro Cultural - São Paulo",
        startDatetime: "2025-10-15 14:00:00",
        endDatetime: "2025-10-15 18:00:00",
        organizer: "Instituto de Fotografia",
        attractions: [
            { name: "Técnicas Avançadas de Fotografia", type: "OTHER" },
            { name: "Prática com Equipamentos", type: "OTHER" }
        ],
        image: "https://via.placeholder.com/300x200"
    },
    2: {
        title: "Concerto de Piano",
        description: "Uma noite inesquecível com as mais belas peças clássicas interpretadas por renomados pianistas. O repertório inclui obras de Chopin, Beethoven e Mozart. Uma experiência cultural única em um dos teatros mais tradicionais do país.",
        location: "Teatro Municipal - Rio de Janeiro",
        startDatetime: "2025-10-22 20:00:00",
        endDatetime: "2025-10-22 22:30:00",
        organizer: "Orquestra Sinfônica do Rio",
        attractions: [
            { name: "Concerto de Piano Clássico", type: "MUSIC" },
            { name: "Obras de Chopin e Beethoven", type: "MUSIC" }
        ],
        image: "https://via.placeholder.com/300x200"
    },
    3: {
        title: "Festival de Arte Contemporânea",
        description: "Evento gratuito apresentando as mais recentes tendências em arte contemporânea. Exposições de artistas locais e nacionais, performances ao vivo e workshops interativos. Uma oportunidade única de conhecer novos talentos e técnicas artísticas.",
        location: "Museu de Arte - Belo Horizonte",
        startDatetime: "2025-10-28 10:00:00",
        endDatetime: "2025-10-28 18:00:00",
        organizer: "Museu de Arte Contemporânea",
        attractions: [
            { name: "Exposição de Arte Contemporânea", type: "VISUAL" },
            { name: "Performances ao Vivo", type: "THEATER" },
            { name: "Workshops Interativos", type: "OTHER" }
        ],
        image: "https://via.placeholder.com/300x200"
    },
    4: {
        title: "Palestra sobre Design UX",
        description: "Palestra ministrada por especialistas da área sobre as tendências atuais em User Experience Design. Abordagem prática com cases reais e dicas valiosas para profissionais da área. Networking incluído.",
        location: "Auditório Tech - Porto Alegre",
        startDatetime: "2025-11-05 19:00:00",
        endDatetime: "2025-11-05 21:00:00",
        organizer: "Tech Community POA",
        attractions: [
            { name: "Tendências em UX Design", type: "OTHER" },
            { name: "Cases Práticos", type: "OTHER" }
        ],
        image: "https://via.placeholder.com/300x200"
    },
    5: {
        title: "Exposição de Pintura",
        description: "Exposição coletiva de pintores baianos contemporâneos. Diversas técnicas e estilos em uma mostra que celebra a rica cultura artística da Bahia. Visita guiada inclusa no ingresso.",
        location: "Galeria Central - Salvador",
        startDatetime: "2025-11-10 15:00:00",
        endDatetime: "2025-11-10 19:00:00",
        organizer: "Galeria de Arte da Bahia",
        attractions: [
            { name: "Pinturas Contemporâneas", type: "VISUAL" },
            { name: "Visita Guiada", type: "OTHER" }
        ],
        image: "https://via.placeholder.com/300x200"
    },
    6: {
        title: "Show de Jazz",
        description: "Uma noite especial de jazz com músicos renomados nacionais e internacionais. Ambiente intimista com drinks especiais e petiscos gourmet. Reservas limitadas para uma experiência exclusiva.",
        location: "Blue Note - Brasília",
        startDatetime: "2025-11-18 21:00:00",
        endDatetime: "2025-11-18 23:30:00",
        organizer: "Blue Note Brasil",
        attractions: [
            { name: "Jazz ao Vivo", type: "MUSIC" },
            { name: "Músicos Internacionais", type: "MUSIC" }
        ],
        image: "https://via.placeholder.com/300x200"
    }
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('Página carregada, inicializando...');
    loadEvents();
    setupSearch();
    setupModalEvents();

    const eventCards = document.querySelectorAll('.event-card');
    eventCards.forEach((card, index) => {
        const eventId = index + 1;
        console.log(`Adicionando listener para card ${eventId}`);

        card.addEventListener('click', function() {
            console.log(`Card ${eventId} clicado!`);
            openEventModal(eventId);
        });

        card.removeAttribute('onclick');
    });
});

let allEvents = [];
let filteredEvents = [];

function loadEvents() {
    const eventCards = document.querySelectorAll('.event-card');
    allEvents = Array.from(eventCards);
    filteredEvents = [...allEvents];
    console.log('Eventos carregados:', allEvents.length);
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
    const eventsGrid = document.getElementById('eventsGrid');
    const noResults = document.getElementById('noResults');

    if (!searchTerm) {
        allEvents.forEach(card => {
            card.style.display = 'block';
        });
        if (eventsGrid) eventsGrid.style.display = 'grid';
        if (noResults) noResults.classList.add('hidden');
        return;
    }

    let hasResults = false;

    allEvents.forEach(card => {
        const title = card.querySelector('.event-title')?.textContent.toLowerCase() || '';
        const location = card.querySelector('.event-location')?.textContent.toLowerCase() || '';
        const date = card.querySelector('.event-date')?.textContent.toLowerCase() || '';

        if (title.includes(searchTerm) ||
            location.includes(searchTerm) ||
            date.includes(searchTerm)) {
            card.style.display = 'block';
            hasResults = true;
        } else {
            card.style.display = 'none';
        }
    });

    if (hasResults) {
        if (eventsGrid) eventsGrid.style.display = 'grid';
        if (noResults) noResults.classList.add('hidden');
    } else {
        if (eventsGrid) eventsGrid.style.display = 'none';
        if (noResults) noResults.classList.remove('hidden');
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
            if (modal && modal.style.display === 'flex') {
                closeEventModal();
            }
        }
    });
}

function formatDate(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleDateString('pt-BR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

function formatTime(startDateTime, endDateTime) {
    const start = new Date(startDateTime);
    const end = new Date(endDateTime);
    return `das ${start.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })} às ${end.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;
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

function openEventModal(eventId) {
    console.log('=== ABRINDO MODAL ===');
    console.log('Event ID:', eventId);

    const event = eventsData[eventId];
    if (!event) {
        console.log('ERRO: Evento não encontrado para ID:', eventId);
        return;
    }

    console.log('Dados do evento encontrado:', event);

    const modal = document.getElementById('eventModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalDate = document.getElementById('modalDate');
    const modalTime = document.getElementById('modalTime');
    const modalLocation = document.getElementById('modalLocation');
    const modalOrganizer = document.getElementById('modalOrganizer');
    const modalDescription = document.getElementById('modalDescription');
    const modalImage = document.getElementById('modalImage');
    const modalAttractions = document.getElementById('modalAttractions');

    console.log('Elementos encontrados:', {
        modal: !!modal,
        modalTitle: !!modalTitle,
        modalDate: !!modalDate,
        modalTime: !!modalTime,
        modalLocation: !!modalLocation,
        modalOrganizer: !!modalOrganizer,
        modalDescription: !!modalDescription,
        modalImage: !!modalImage,
        modalAttractions: !!modalAttractions
    });

    if (!modal) {
        console.log('ERRO: Modal não encontrado!');
        return;
    }

    if (modalTitle) modalTitle.textContent = event.title;
    if (modalDate) modalDate.textContent = formatDate(event.startDatetime);
    if (modalTime) modalTime.textContent = formatTime(event.startDatetime, event.endDatetime);
    if (modalLocation) modalLocation.textContent = event.location;
    if (modalOrganizer) modalOrganizer.textContent = event.organizer;
    if (modalDescription) modalDescription.textContent = event.description;
    if (modalImage) modalImage.src = event.image;

    if (modalAttractions) {
        modalAttractions.innerHTML = '';

        event.attractions.forEach(attraction => {
            const attractionDiv = document.createElement('div');
            attractionDiv.className = 'attraction-item';
            attractionDiv.innerHTML = `
                <span class="attraction-name">${attraction.name}</span>
                <span class="attraction-type">${translateAttractionType(attraction.type)}</span>
            `;
            modalAttractions.appendChild(attractionDiv);
        });
    }

    modal.style.display = 'flex';
    modal.style.opacity = '1';
    document.body.style.overflow = 'hidden';

    console.log('Modal exibido! Display:', modal.style.display, 'Opacity:', modal.style.opacity);
}

function closeEventModal() {
    console.log('=== FECHANDO MODAL ===');
    const modal = document.getElementById('eventModal');

    if (modal) {
        modal.style.display = 'none';
        modal.style.opacity = '0';
        document.body.style.overflow = 'auto';
        console.log('Modal fechado');
    }
}

function registerForEvent() {
    alert('Funcionalidade de inscrição será implementada em breve!');
    closeEventModal();
}

// Função utilitária para limpar pesquisa
function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.value = '';
        filterEvents('');
    }
}