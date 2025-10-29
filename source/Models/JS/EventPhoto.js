export class EventPhoto {
    #id;
    #eventId;
    #photo;
    #isMain;

    constructor(
        id = null,
        eventId = null,
        photo = null,
        isMain = false
    ) {
        this.#id = id;
        this.#eventId = eventId;
        this.#photo = photo;
        this.#isMain = isMain;
    }
    
    getId = function() {
        return this.#id;
    }

    getEventId = function() {
        return this.#eventId;
    }

    getPhoto = function() {
        return this.#photo;
    }

    getIsMain = function() {
        return this.#isMain;
    }
    
    setId = function(id) {
        this.#id = id;
    }

    setEventId = function(eventId) {
        this.#eventId = eventId;
    }

    setPhoto = function(photo) {
        this.#photo = photo;
    }

    setIsMain = function(isMain) {
        this.#isMain = isMain;
    }
    
    findById = async function(id) {
        try {
            const response = await fetch(`/api/event-photos/${id}`);
            if (!response.ok) {
                throw new Error('Erro ao buscar foto');
            }
            const data = await response.json();
            this.fill(data);
            return true;
        } catch (error) {
            console.error('Erro ao buscar foto:', error);
            return false;
        }
    }
    
    static findByEventId = async function(eventId) {
        try {
            const response = await fetch(`/api/events/${eventId}/photos`);
            if (!response.ok) {
                throw new Error('Erro ao buscar fotos do evento');
            }
            const data = await response.json();
            return data.map(item => {
                const photo = new EventPhoto();
                photo.fill(item);
                return photo;
            });
        } catch (error) {
            console.error('Erro ao buscar fotos do evento:', error);
            return [];
        }
    }
    
    static findMainPhotoByEventId = async function(eventId) {
        try {
            const response = await fetch(`/api/events/${eventId}/photos/main`);
            if (!response.ok) {
                return null;
            }
            const data = await response.json();
            return data.photo || null;
        } catch (error) {
            console.error('Erro ao buscar foto principal:', error);
            return null;
        }
    }
    
    upload = async function(file) {
        try {
            const formData = new FormData();
            formData.append('photo', file);
            formData.append('eventId', this.#eventId);
            formData.append('isMain', this.#isMain ? '1' : '0');

            const response = await fetch('/api/event-photos', {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Erro ao fazer upload da foto');
            }

            const data = await response.json();
            this.fill(data);
            return true;
        } catch (error) {
            console.error('Erro ao fazer upload da foto:', error);
            return false;
        }
    }
    
    setAsMain = async function() {
        if (!this.#id) return false;

        try {
            const response = await fetch(`/api/event-photos/${this.#id}/set-main`, {
                method: 'PUT'
            });

            if (!response.ok) {
                throw new Error('Erro ao definir foto principal');
            }

            this.#isMain = true;
            return true;
        } catch (error) {
            console.error('Erro ao definir foto principal:', error);
            return false;
        }
    }
    
    delete = async function() {
        if (!this.#id) return false;

        try {
            const response = await fetch(`/api/event-photos/${this.#id}`, {
                method: 'DELETE'
            });

            if (!response.ok) {
                throw new Error('Erro ao deletar foto');
            }

            return true;
        } catch (error) {
            console.error('Erro ao deletar foto:', error);
            return false;
        }
    }

    static countByEventId = async function(eventId) {
        try {
            const response = await fetch(`/api/events/${eventId}/photos/count`);
            if (!response.ok) {
                throw new Error('Erro ao contar fotos');
            }
            const data = await response.json();
            return data.total || 0;
        } catch (error) {
            console.error('Erro ao contar fotos:', error);
            return 0;
        }
    }

    fill = function(data) {
        if (!data) return;

        this.#id = data.id || null;
        this.#eventId = data.eventId || null;
        this.#photo = data.photo || null;
        this.#isMain = data.isMain || false;
    }

    toJSON = function() {
        return {
            id: this.#id,
            eventId: this.#eventId,
            photo: this.#photo,
            isMain: this.#isMain
        };
    }

    getPhotoUrl = function() {
        if (!this.#photo) return null;
        return this.#photo.startsWith('http') ? this.#photo : `/uploads/${this.#photo}`;
    }

    static validateImageFile = function(file) {
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
        const maxSize = 5 * 1024 * 1024;

        if (!validTypes.includes(file.type)) {
            console.error('Tipo de arquivo inválido. Use JPEG, PNG, GIF ou WebP.');
            return false;
        }

        if (file.size > maxSize) {
            console.error('Arquivo muito grande. Tamanho máximo: 5MB.');
            return false;
        }

        return true;
    }

    static createPreview = function(file, callback) {
        if (!EventPhoto.validateImageFile(file)) {
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            if (callback) {
                callback(e.target.result);
            }
        };
        reader.readAsDataURL(file);
    }
}