const API_BASE = "http://localhost/ensaiei-main/api";
let currentEvents = [];
let currentEventId = null;
let currentPhotos = [];
let newPhotosToUpload = [];

document.addEventListener('DOMContentLoaded', function() {
    checkAuth();
    loadEvents();
    setupEventListeners();
});


function checkAuth() {
    const token = localStorage.getItem('token');
    if (!token) {
        alert('Você precisa estar logado para acessar esta página.');
        window.location.href = '/ensaiei-main/login';
        return;
    }
}

function handlePhotoSelect(e) {
    const files = Array.from(e.target.files);
    handlePhotoFiles(files);
}

function handlePhotoFiles(files) {
    files.forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                newPhotosToUpload.push({
                    file: file,
                    preview: e.target.result,
                    isNew: true
                });
                renderPhotoPreview();
            };
            reader.readAsDataURL(file);
        }
    });
}

function renderPhotoPreview() {
    const container = document.getElementById('photoPreviewGrid');
    if (!container) return;

    const currentPhotosArray = Array.isArray(currentPhotos) ? currentPhotos : [];
    const newPhotosArray = Array.isArray(newPhotosToUpload) ? newPhotosToUpload : [];
    const allPhotos = [...currentPhotosArray, ...newPhotosArray];

    if (allPhotos.length === 0) {
        container.innerHTML = '<p style="text-align: center; color: #999; grid-column: 1/-1;">Nenhuma foto adicionada</p>';
        return;
    }

    container.innerHTML = allPhotos.map((photo, index) => {
        const isMain = photo.isMain || index === 0;
        const photoUrl = photo.isNew ? photo.preview : photo.photo;
        const photoId = photo.id || null;

        return `
            <div class="photo-preview-item ${isMain ? 'main-photo' : ''}">
                <img src="${photoUrl}" alt="Foto do evento">
                ${isMain ? '<span class="main-badge">Principal</span>' : ''}
                <div class="photo-actions">
                    ${!isMain && !photo.isNew ? `<button class="photo-action-btn" onclick="setMainPhoto(${photoId})">⭐ Principal</button>` : ''}
                    <button class="photo-action-btn delete" onclick="${photo.isNew ? `removeNewPhoto(${index - currentPhotosArray.length})` : `deletePhoto(${photoId})`}">🗑️ Remover</button>
                </div>
            </div>
        `;
    }).join('');
}

// Carregar fotos existentes do evento
async function loadEventPhotos(eventId) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_BASE}/event/${eventId}/photos`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'token': token
            }
        });

        const data = await response.json();

        if (response.ok) {
            // Garantir que seja sempre um array
            currentPhotos = Array.isArray(data) ? data :
                (data.data && Array.isArray(data.data)) ? data.data : [];
            renderPhotoPreview();
        } else {
            console.error('Erro ao carregar fotos:', data);
            currentPhotos = [];
            renderPhotoPreview();
        }
    } catch (error) {
        console.error('Erro ao carregar fotos:', error);
        currentPhotos = [];
        renderPhotoPreview();
    }
}

// Remover foto nova (antes de fazer upload)
function removeNewPhoto(index) {
    newPhotosToUpload.splice(index, 1);
    renderPhotoPreview();
}

// Definir foto como principal
async function setMainPhoto(photoId) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_BASE}/event/photos/${photoId}/main`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'token': token
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao definir foto principal');
        }

        // Recarregar fotos
        await loadEventPhotos(currentEventId);
        alert('Foto principal definida com sucesso!');

    } catch (error) {
        console.error('Erro ao definir foto principal:', error);
        alert('Erro ao definir foto principal: ' + error.message);
    }
}

// Deletar foto existente
async function deletePhoto(photoId) {
    if (!confirm('Tem certeza que deseja excluir esta foto?')) {
        return;
    }

    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_BASE}/event/photos/${photoId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'token': token
            }
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao deletar foto');
        }

        // Recarregar fotos
        await loadEventPhotos(currentEventId);
        alert('Foto deletada com sucesso!');

    } catch (error) {
        console.error('Erro ao deletar foto:', error);
        alert('Erro ao deletar foto: ' + error.message);
    }
}

// Fazer upload das novas fotos
async function uploadNewPhotos(eventId) {
    if (newPhotosToUpload.length === 0) {
        return true;
    }

    const token = localStorage.getItem('token');
    const formData = new FormData();

    newPhotosToUpload.forEach((photo, index) => {
        formData.append('photos[]', photo.file);
    });

    try {
        const response = await fetch(`${API_BASE}/event/${eventId}/photos`, {
            method: 'POST',
            headers: {
                'token': token
            },
            body: formData
        });

        const data = await response.json();

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao fazer upload das fotos');
        }

        console.log('Upload de fotos concluído:', data);
        return true;

    } catch (error) {
        console.error('Erro ao fazer upload das fotos:', error);
        alert('Erro ao fazer upload das fotos: ' + error.message);
        return false;
    }
}

// Configurar event listeners
function setupEventListeners() {
    const createBtn = document.getElementById('btnCreateEvent');
    const modal = document.getElementById('eventModal');
    const closeBtn = document.getElementById('modalClose');
    const cancelBtn = document.getElementById('btnCancel');
    const eventForm = document.getElementById('eventForm');
    const photoInput = document.getElementById('photoInput');
    const photoUploadArea = document.getElementById('photoUploadArea');

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

    // Upload de fotos
    if (photoInput) {
        photoInput.addEventListener('change', handlePhotoSelect);
    }

    if (photoUploadArea) {
        photoUploadArea.addEventListener('click', () => photoInput.click());

        // Drag and drop
        photoUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            photoUploadArea.classList.add('dragover');
        });

        photoUploadArea.addEventListener('dragleave', () => {
            photoUploadArea.classList.remove('dragover');
        });

        photoUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            photoUploadArea.classList.remove('dragover');
            const files = Array.from(e.dataTransfer.files).filter(file => file.type.startsWith('image/'));
            if (files.length > 0) {
                handlePhotoFiles(files);
            }
        });
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

        // Buscar foto principal (será carregada dinamicamente)
        const cardId = `event-card-${event.id}`;

        return `
            <div class="event-card" id="${cardId}">
                <div class="event-card-image no-photo">
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

    // Carregar fotos principais de cada evento
    events.forEach(event => {
        loadEventMainPhoto(event.id);
    });
}

// Carregar foto principal do evento para o card
async function loadEventMainPhoto(eventId) {
    const token = localStorage.getItem('token');

    try {
        const response = await fetch(`${API_BASE}/event/${eventId}/photos`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'token': token
            }
        });

        if (response.ok) {
            const data = await response.json();
            const photos = data.data || data;

            if (photos && Array.isArray(photos) && photos.length > 0) {
                const mainPhoto = photos.find(p => p.isMain) || photos[0];
                const cardImage = document.querySelector(`#event-card-${eventId} .event-card-image`);

                if (cardImage) {
                    cardImage.classList.remove('no-photo');
                    cardImage.innerHTML = `<img src="${mainPhoto.photo}" alt="Foto do evento">`;
                }
            }
        }
    } catch (error) {
        console.error('Erro ao carregar foto principal:', error);
    }
}

// Abrir modal de criação
function openCreateModal() {
    currentEventId = null;
    currentPhotos = [];
    newPhotosToUpload = [];
    document.getElementById('modalTitle').textContent = 'Criar Novo Evento';
    document.getElementById('eventForm').reset();
    renderPhotoPreview();
    document.getElementById('eventModal').classList.add('active');
}

// Abrir modal de edição
async function openEditModal(eventId) {
    currentEventId = eventId;
    currentPhotos = [];
    newPhotosToUpload = [];
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
        console.log('Dados do evento para edição:', data);

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao carregar evento');
        }

        const event = data.data || data;

        // Preencher formulário
        document.getElementById('eventTitle').value = event.title || '';
        document.getElementById('eventDescription').value = event.description || '';
        document.getElementById('eventLocation').value = event.location || '';

        // Formatar datas - pode vir em diferentes formatos
        if (event.startDate && event.startTime) {
            // Formato DD/MM/YYYY
            const [day, month, year] = event.startDate.split('/');
            document.getElementById('eventStartDate').value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            document.getElementById('eventStartTime').value = event.startTime;
        } else if (event.startDatetime) {
            // Formato ISO ou timestamp
            const startDate = new Date(event.startDatetime);
            const startYear = startDate.getFullYear();
            const startMonth = String(startDate.getMonth() + 1).padStart(2, '0');
            const startDay = String(startDate.getDate()).padStart(2, '0');
            const startHour = String(startDate.getHours()).padStart(2, '0');
            const startMinute = String(startDate.getMinutes()).padStart(2, '0');

            document.getElementById('eventStartDate').value = `${startYear}-${startMonth}-${startDay}`;
            document.getElementById('eventStartTime').value = `${startHour}:${startMinute}`;
        }

        if (event.endDate && event.endTime) {
            // Formato DD/MM/YYYY
            const [day, month, year] = event.endDate.split('/');
            document.getElementById('eventEndDate').value = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            document.getElementById('eventEndTime').value = event.endTime;
        } else if (event.endDatetime) {
            // Formato ISO ou timestamp
            const endDate = new Date(event.endDatetime);
            const endYear = endDate.getFullYear();
            const endMonth = String(endDate.getMonth() + 1).padStart(2, '0');
            const endDay = String(endDate.getDate()).padStart(2, '0');
            const endHour = String(endDate.getHours()).padStart(2, '0');
            const endMinute = String(endDate.getMinutes()).padStart(2, '0');

            document.getElementById('eventEndDate').value = `${endYear}-${endMonth}-${endDay}`;
            document.getElementById('eventEndTime').value = `${endHour}:${endMinute}`;
        }

        // Carregar fotos do evento
        await loadEventPhotos(eventId);

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
    currentPhotos = [];
    newPhotosToUpload = [];
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

    // Criar URLSearchParams para enviar como form data
    const formData = new URLSearchParams();
    formData.append('title', title);
    formData.append('description', description);
    formData.append('location', location);
    formData.append('startDate', `${startDay}/${startMonth}/${startYear}`);
    formData.append('startTime', startTime);
    formData.append('endDate', `${endDay}/${endMonth}/${endYear}`);
    formData.append('endTime', endTime);

    console.log('Enviando dados:', {
        title,
        description,
        location,
        startDate: `${startDay}/${startMonth}/${startYear}`,
        startTime,
        endDate: `${endDay}/${endMonth}/${endYear}`,
        endTime
    });

    try {
        const url = currentEventId
            ? `${API_BASE}/event/update/${currentEventId}`
            : `${API_BASE}/event/add`;

        const method = currentEventId ? 'PUT' : 'POST';

        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'token': token
            },
            body: formData.toString()
        });

        const data = await response.json();
        console.log('Resposta da API:', data);

        if (!response.ok) {
            throw new Error(data.message || 'Erro ao salvar evento');
        }

        // Se é criação, pegar o ID do evento criado
        const eventId = currentEventId || (data.data && data.data.id);

        // Fazer upload das fotos novas
        if (newPhotosToUpload.length > 0 && eventId) {
            await uploadNewPhotos(eventId);
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