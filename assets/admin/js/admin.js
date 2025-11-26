 const API_BASE = "http://localhost/ensaiei-main/api";

    document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    loadStats();
    loadPendingEvents();
});

    function checkAuth() {
    const token = localStorage.getItem('token');
    if (!token) {
    alert('Você precisa estar logado.');
    window.location.href = '/ensaiei-main/login';
    return;
}
}

    async function loadStats() {
    const token = localStorage.getItem('token');

    try {
    const response = await fetch(`${API_BASE}/admin/events/stats`, {
    method: 'GET',
    headers: {
    'Content-Type': 'application/json',
    'token': token
}
});

    const data = await response.json();

    if (!response.ok) {
    throw new Error(data.message);
}

    const stats = data.data || data;

    document.getElementById('statTotal').textContent = stats.total || 0;
    document.getElementById('statPending').textContent = stats.pending || 0;
    document.getElementById('statApproved').textContent = stats.approved || 0;
    document.getElementById('statRejected').textContent = stats.rejected || 0;

} catch (error) {
    console.error('Erro ao carregar estatísticas:', error);
}
}

    async function loadPendingEvents() {
    const token = localStorage.getItem('token');
    const container = document.getElementById('eventsGrid');

    container.innerHTML = '<div class="loading"><div class="spinner"></div></div>';

    try {
    const response = await fetch(`${API_BASE}/admin/events/pending`, {
    method: 'GET',
    headers: {
    'Content-Type': 'application/json',
    'token': token
}
});

    const data = await response.json();

    if (!response.ok) {
    throw new Error(data.message);
}

    const events = data.data || data;

    if (!events || events.length === 0) {
    container.innerHTML = `
                        <div class="empty-state">
                            <div class="empty-state-icon">✅</div>
                            <h3>Nenhum evento pendente</h3>
                            <p>Todos os eventos foram revisados!</p>
                        </div>
                    `;
    return;
}

    container.innerHTML = events.map(event => `
                    <div class="event-card" id="event-${event.id}">
                        <div class="event-header">
                            <div>
                                <h3 class="event-title">${event.title}</h3>
                                <p class="event-organizer">
                                    Por: ${event.organizerName || 'Organizador'}
                                    (@${event.organizerUsername || 'user'})
                                </p>
                            </div>
                            <span class="status-badge">Pendente</span>
                        </div>

                        <div class="event-info">
                            <div class="info-item">
                                <span class="info-label">Data de Início</span>
                                <span class="info-value">${formatDate(event.startDatetime)}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Data de Término</span>
                                <span class="info-value">${formatDate(event.endDatetime)}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Local</span>
                                <span class="info-value">${event.location}</span>
                            </div>
                        </div>

                        <p class="event-description">${event.description}</p>

                        <div class="event-actions">
                            <button class="btn btn-approve" onclick="approveEvent(${event.id})">
                                ✓ Aprovar Evento
                            </button>
                            <button class="btn btn-reject" onclick="rejectEvent(${event.id})">
                                ✗ Rejeitar Evento
                            </button>
                        </div>
                    </div>
                `).join('');

} catch (error) {
    console.error('Erro ao carregar eventos:', error);
    container.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-state-icon">⚠️</div>
                        <h3>Erro ao carregar eventos</h3>
                        <p>${error.message}</p>
                    </div>
                `;
}
}

    async function approveEvent(eventId) {
    if (!confirm('Tem certeza que deseja aprovar este evento?')) {
    return;
}

    const token = localStorage.getItem('token');

    try {
    const response = await fetch(`${API_BASE}/admin/events/${eventId}/approve`, {
    method: 'PUT',
    headers: {
    'Content-Type': 'application/json',
    'token': token
}
});

    const data = await response.json();

    if (!response.ok) {
    throw new Error(data.message);
}

    alert('Evento aprovado com sucesso!');

    // Remover card do evento
    const card = document.getElementById(`event-${eventId}`);
    if (card) {
    card.style.transition = 'all 0.3s ease';
    card.style.opacity = '0';
    card.style.transform = 'scale(0.9)';
    setTimeout(() => card.remove(), 300);
}

    // Recarregar stats
    loadStats();

} catch (error) {
    console.error('Erro ao aprovar evento:', error);
    alert('Erro ao aprovar evento: ' + error.message);
}
}

    async function rejectEvent(eventId) {
    if (!confirm('Tem certeza que deseja rejeitar este evento?')) {
    return;
}

    const token = localStorage.getItem('token');

    try {
    const response = await fetch(`${API_BASE}/admin/events/${eventId}/reject`, {
    method: 'PUT',
    headers: {
    'Content-Type': 'application/json',
    'token': token
}
});

    const data = await response.json();

    if (!response.ok) {
    throw new Error(data.message);
}

    alert('Evento rejeitado.');

    // Remover card do evento
    const card = document.getElementById(`event-${eventId}`);
    if (card) {
    card.style.transition = 'all 0.3s ease';
    card.style.opacity = '0';
    card.style.transform = 'scale(0.9)';
    setTimeout(() => card.remove(), 300);
}

    // Recarregar stats
    loadStats();

} catch (error) {
    console.error('Erro ao rejeitar evento:', error);
    alert('Erro ao rejeitar evento: ' + error.message);
}
}

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