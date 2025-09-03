// Dados dos eventos (em produção, viriam da API/banco de dados)
const eventsData = {
    1: {
        title: "Workshop de Fotografia",
        date: "15 de Outubro, 2025",
        location: "Centro Cultural - São Paulo",
        price: "R$ 150,00",
        time: "14:00",
        capacity: "30 pessoas",
        image: "https://via.placeholder.com/300x200",
        description: "Um workshop completo sobre técnicas avançadas de fotografia, cobrindo desde conceitos básicos até técnicas profissionais. Ideal para iniciantes e fotógrafos intermediários que desejam aprimorar suas habilidades. Incluído equipamentos para prática e certificado de participação."
    },
    2: {
        title: "Concerto de Piano",
        date: "22 de Outubro, 2025",
        location: "Teatro Municipal - Rio de Janeiro",
        price: "R$ 80,00",
        time: "20:00",
        capacity: "400 pessoas",
        image: "https://via.placeholder.com/300x200",
        description: "Uma noite inesquecível com as mais belas peças clássicas interpretadas por renomados pianistas. O repertório inclui obras de Chopin, Beethoven e Mozart. Uma experiência cultural única em um dos teatros mais tradicionais do país."
    },
    3: {
        title: "Festival de Arte Contemporânea",
        date: "28 de Outubro, 2025",
        location: "Museu de Arte - Belo Horizonte",
        price: "Gratuito",
        time: "10:00",
        capacity: "200 pessoas",
        image: "https://via.placeholder.com/300x200",
        description: "Evento gratuito apresentando as mais recentes tendências em arte contemporânea. Exposições de artistas locais e nacionais, performances ao vivo e workshops interativos. Uma oportunidade única de conhecer novos talentos e técnicas artísticas."
    },
    4: {
        title: "Palestra sobre Design UX",
        date: "5 de Novembro, 2025",
        location: "Auditório Tech - Porto Alegre",
        price: "R$ 50,00",
        time: "19:00",
        capacity: "150 pessoas",
        image: "https://via.placeholder.com/300x200",
        description: "Palestra ministrada por especialistas da área sobre as tendências atuais em User Experience Design. Abordagem prática com cases reais e dicas valiosas para profissionais da área. Networking incluído."
    },
    5: {
        title: "Exposição de Pintura",
        date: "10 de Novembro, 2025",
        location: "Galeria Central - Salvador",
        price: "R$ 25,00",
        time: "15:00",
        capacity: "80 pessoas",
        image: "https://via.placeholder.com/300x200",
        description: "Exposição coletiva de pintores baianos contemporâneos. Diversas técnicas e estilos em uma mostra que celebra a rica cultura artística da Bahia. Visita guiada inclusa no ingresso."
    },
    6: {
        title: "Show de Jazz",
        date: "18 de Novembro, 2025",
        location: "Blue Note - Brasília",
        price: "R$ 120,00",
        time: "21:00",
        capacity: "120 pessoas",
        image: "https://via.placeholder.com/300x200",
        description: "Uma noite especial de jazz com músicos renomados nacionais e internacionais. Ambiente intimista com drinks especiais e petiscos gourmet. Reservas limitadas para uma experiência exclusiva."
    }
};

// Variáveis globais
let allEvents = [];
let filteredEvents = [];

// Inicialização
document.addEventListener('DOMContentLoaded', function() {
    loadEvents();
    setupSearch();
    setupModalEvents();
});

// Carrega eventos na página
function loadEvents() {
    const eventCards = document.querySelectorAll('.event-card');
    allEvents = Array.from(eventCards);
    filteredEvents = [...allEvents];
}

// Configurar funcionalidade de pesquisa
function setupSearch() {
    const searchInput = document.getElementById('searchInput');

    // Pesquisa em tempo real
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase().trim();
        filterEvents(searchTerm);
    });

    // Pesquisa ao pressionar Enter
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            searchEvents();
        }
    });
}

// Filtrar eventos
function filterEvents(searchTerm) {
    const eventsGrid = document.getElementById('eventsGrid');
    const noResults = document.getElementById('noResults');

    if (!searchTerm) {
        // Mostrar todos os eventos
        allEvents.forEach(card => {
            card.style.display = 'block';
        });
        eventsGrid.style.display = 'grid';
        noResults.classList.add('hidden');
        return;
    }

    let hasResults = false;

    allEvents.forEach(card => {
        const title = card.querySelector('.event-title').textContent.toLowerCase();
        const location = card.querySelector('.event-location').textContent.toLowerCase();
        const date = card.querySelector('.event-date').textContent.toLowerCase();

        if (title.includes(searchTerm) ||
            location.includes(searchTerm) ||
            date.includes(searchTerm)) {
            card.style.display = 'block';
            hasResults = true;
        } else {
            card.style.display = 'none';
        }
    });

    // Mostrar/ocultar mensagem de "sem resultados"
    if (hasResults) {
        eventsGrid.style.display = 'grid';
        noResults.classList.add('hidden');
    } else {
        eventsGrid.style.display = 'none';
        noResults.classList.remove('hidden');
    }
}

// Função de pesquisa (chamada pelo botão)
function searchEvents() {
    const searchInput = document.getElementById('searchInput');
    const searchTerm = searchInput.value.toLowerCase().trim();
    filterEvents(searchTerm);
}

// Configurar eventos do modal
function setupModalEvents() {
    const modal = document.getElementById('eventModal');

    // Fechar modal ao clicar fora dele
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeEventModal();
        }
    });

    // Fechar modal com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeEventModal();
        }
    });
}

// Função para formatar data
function formatDate(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleDateString('pt-BR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
}

// Função para formatar horário
function formatTime(startDateTime, endDateTime) {
    const start = new Date(startDateTime);
    const end = new Date(endDateTime);
    return `das ${start.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })} às ${end.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;
}

// Função para traduzir tipo de atração
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

// Abrir modal do evento
function openEventModal(eventId) {
    const event = eventsData[eventId];
    if (!event) return;

    // Preencher dados do modal
    document.getElementById('modalTitle').textContent = event.title;
    document.getElementById('modalDate').textContent = formatDate(event.startDatetime);
    document.getElementById('modalTime').textContent = formatTime(event.startDatetime, event.endDatetime);
    document.getElementById('modalLocation').textContent = event.location;
    document.getElementById('modalOrganizer').textContent = event.organizer;
    document.getElementById('modalDescription').textContent = event.description;
    document.getElementById('modalImage').src = event.image;

    // Preencher atrações
    const attractionsContainer = document.getElementById('modalAttractions');
    attractionsContainer.innerHTML = '';

    event.attractions.forEach(attraction => {
        const attractionDiv = document.createElement('div');
        attractionDiv.className = 'attraction-item';
        attractionDiv.innerHTML = `
            <span class="attraction-name">${attraction.name}</span>
            <span class="attraction-type">${translateAttractionType(attraction.type)}</span>
        `;
        attractionsContainer.appendChild(attractionDiv);
    });

    // Mostrar modal
    const modal = document.getElementById('eventModal');
    modal.classList.remove('hidden');
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
}

// Fechar modal do evento
function closeEventModal() {
    console.log('Fechando modal...'); // Debug
    const modal = document.getElementById('eventModal');
    if (modal) {
        modal.classList.remove('show');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
        console.log('Modal fechado');
    }
}

// Inscrever-se no evento
function registerForEvent() {
    // Aqui você faria a chamada para a API de inscrição
    alert('Funcionalidade de inscrição será implementada em breve!');

    // Exemplo de implementação:
    // const eventTitle = document.getElementById('modalTitle').textContent;
    // console.log('Inscrevendo no evento:', eventTitle);

    // Fechar modal após inscrição
    closeEventModal();
}

// Função utilitária para limpar pesquisa
function clearSearch() {
    const searchInput = document.getElementById('searchInput');
    searchInput.value = '';
    filterEvents('');
}