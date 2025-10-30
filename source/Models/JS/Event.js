export class Event {
    #id;
    #title;
    #description;
    #location;
    #latitude;
    #longitude;
    #startDatetime;
    #endDatetime;
    #deleted;
    #organizerId;
    #attractions;

    constructor(
        id = null,
        title = null,
        description = null,
        location = null,
        latitude = null,
        longitude = null,
        startDatetime = null,
        endDatetime = null,
        deleted = false,
        organizerId = null
    ) {
        this.#id = id;
        this.#title = title;
        this.#description = description;
        this.#location = location;
        this.#latitude = latitude;
        this.#longitude = longitude;
        this.#startDatetime = startDatetime;
        this.#endDatetime = endDatetime;
        this.#deleted = deleted;
        this.#organizerId = organizerId;
        this.#attractions = [];
    }


    getId = function() {
        return this.#id;
    }

    getTitle = function() {
        return this.#title;
    }

    getDescription = function() {
        return this.#description;
    }

    getLocation = function() {
        return this.#location;
    }

    getLatitude = function() {
        return this.#latitude;
    }

    getLongitude = function() {
        return this.#longitude;
    }

    getStartDatetime = function() {
        return this.#startDatetime;
    }

    getEndDatetime = function() {
        return this.#endDatetime;
    }

    getDeleted = function() {
        return this.#deleted;
    }

    getOrganizerId = function() {
        return this.#organizerId;
    }

    getAttractions = function() {
        return this.#attractions;
    }

    getStartDate = function() {
        if (!this.#startDatetime) return '';
        const date = new Date(this.#startDatetime);
        return date.toLocaleDateString('pt-BR');
    }

    getStartTime = function() {
        if (!this.#startDatetime) return '';
        const date = new Date(this.#startDatetime);
        return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }

    getEndDate = function() {
        if (!this.#endDatetime) return '';
        const date = new Date(this.#endDatetime);
        return date.toLocaleDateString('pt-BR');
    }

    getEndTime = function() {
        if (!this.#endDatetime) return '';
        const date = new Date(this.#endDatetime);
        return date.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
    }

    setId = function(id) {
        this.#id = id;
    }

    setTitle = function(title) {
        this.#title = title;
    }

    setDescription = function(description) {
        this.#description = description;
    }

    setLocation = function(location) {
        this.#location = location;
    }

    setLatitude = function(latitude) {
        this.#latitude = latitude;
    }

    setLongitude = function(longitude) {
        this.#longitude = longitude;
    }

    setStartDatetime = function(startDatetime) {
        this.#startDatetime = startDatetime;
    }

    setEndDatetime = function(endDatetime) {
        this.#endDatetime = endDatetime;
    }

    setDeleted = function(deleted) {
        this.#deleted = deleted;
    }

    setOrganizerId = function(organizerId) {
        this.#organizerId = organizerId;
    }

    setAttractions = function(attractions) {
        this.#attractions = attractions;
    }

    addAttraction = function(attraction) {
        if (attraction && attraction.id && attraction.name) {
            this.#attractions.push(attraction);
        }
    }

    removeAttraction = function(attractionId) {
        this.#attractions = this.#attractions.filter(
            attraction => attraction.id !== attractionId
        );
    }

    hasAttractions = function() {
        return this.#attractions.length > 0;
    }

    countAttractions = function() {
        return this.#attractions.length;
    }

    formLoad = function(form) {
        if (!form) return;

        if (form.title) form.title.value = this.#title || '';
        if (form.description) form.description.value = this.#description || '';
        if (form.location) form.location.value = this.#location || '';
        if (form.latitude) form.latitude.value = this.#latitude || '';
        if (form.longitude) form.longitude.value = this.#longitude || '';

        if (form.startDate) form.startDate.value = this.getStartDate();
        if (form.startTime) form.startTime.value = this.getStartTime();
        if (form.endDate) form.endDate.value = this.getEndDate();
        if (form.endTime) form.endTime.value = this.getEndTime();
    }

    formExtract = function(form) {
        if (!form) return;

        this.#title = form.title?.value || null;
        this.#description = form.description?.value || null;
        this.#location = form.location?.value || null;
        this.#latitude = form.latitude?.value ? parseFloat(form.latitude.value) : null;
        this.#longitude = form.longitude?.value ? parseFloat(form.longitude.value) : null;

        if (form.startDate?.value && form.startTime?.value) {
            this.#startDatetime = `${form.startDate.value} ${form.startTime.value}`;
        }
        if (form.endDate?.value && form.endTime?.value) {
            this.#endDatetime = `${form.endDate.value} ${form.endTime.value}`;
        }
    }

    findById = async function(id) {
        try {
            const response = await fetch(`/api/events/${id}`);
            if (!response.ok) {
                throw new Error('Erro ao buscar evento');
            }
            const data = await response.json();
            this.fill(data);
            return true;
        } catch (error) {
            console.error('Erro ao buscar evento:', error);
            return false;
        }
    }

    save = async function() {
        try {
            const url = this.#id ? `/api/events/${this.#id}` : '/api/events';
            const method = this.#id ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.toJSON())
            });

            if (!response.ok) {
                throw new Error('Erro ao salvar evento');
            }

            const data = await response.json();
            if (data.id) this.#id = data.id;
            return true;
        } catch (error) {
            console.error('Erro ao salvar evento:', error);
            return false;
        }
    }

    delete = async function() {
        try {
            const response = await fetch(`/api/events/${this.#id}`, {
                method: 'DELETE'
            });

            if (!response.ok) {
                throw new Error('Erro ao deletar evento');
            }

            return true;
        } catch (error) {
            console.error('Erro ao deletar evento:', error);
            return false;
        }
    }

    loadAttractions = async function() {
        if (!this.#id) return false;

        try {
            const response = await fetch(`/api/events/${this.#id}/attractions`);
            if (!response.ok) {
                throw new Error('Erro ao carregar atrações');
            }
            const data = await response.json();
            this.#attractions = data;
            return true;
        } catch (error) {
            console.error('Erro ao carregar atrações:', error);
            return false;
        }
    }

    fill = function(data) {
        if (!data) return;

        this.#id = data.id || null;
        this.#title = data.title || null;
        this.#description = data.description || null;
        this.#location = data.location || null;
        this.#latitude = data.latitude || null;
        this.#longitude = data.longitude || null;
        this.#startDatetime = data.startDatetime || null;
        this.#endDatetime = data.endDatetime || null;
        this.#deleted = data.deleted || false;
        this.#organizerId = data.organizerId || null;
        this.#attractions = data.attractions || [];
    }

    toJSON = function() {
        return {
            id: this.#id,
            title: this.#title,
            description: this.#description,
            location: this.#location,
            latitude: this.#latitude,
            longitude: this.#longitude,
            startDatetime: this.#startDatetime,
            endDatetime: this.#endDatetime,
            deleted: this.#deleted,
            organizerId: this.#organizerId,
            attractions: this.#attractions
        };
    }
}