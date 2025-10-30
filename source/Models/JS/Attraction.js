export class Attraction {
    #id;
    #name;
    #type;
    #eventId;
    #startDatetime;
    #endDatetime;
    #specificLocation;
    #performers;
    #deleted;

    constructor(
        id = null,
        name = null,
        type = 'OTHER',
        eventId = null,
        startDatetime = null,
        endDatetime = null,
        specificLocation = null,
        performers = [],
        deleted = false
    ) {
        this.#id = id;
        this.#name = name;
        this.#type = type;
        this.#eventId = eventId;
        this.#startDatetime = startDatetime;
        this.#endDatetime = endDatetime;
        this.#specificLocation = specificLocation;
        this.#performers = performers || [];
        this.#deleted = deleted;
    }

    getId = function() {
        return this.#id;
    }

    getName = function() {
        return this.#name;
    }

    getType = function() {
        return this.#type;
    }

    getEventId = function() {
        return this.#eventId;
    }

    getStartDatetime = function() {
        return this.#startDatetime;
    }

    getEndDatetime = function() {
        return this.#endDatetime;
    }

    getSpecificLocation = function() {
        return this.#specificLocation;
    }

    getPerformers = function() {
        return this.#performers;
    }

    getDeleted = function() {
        return this.#deleted;
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

    setName = function(name) {
        this.#name = name;
    }

    setType = function(type) {
        this.#type = type;
    }

    setEventId = function(eventId) {
        this.#eventId = eventId;
    }


    setStartDatetime = function(startDatetime) {
        this.#startDatetime = startDatetime;
    }

    setEndDatetime = function(endDatetime) {
        this.#endDatetime = endDatetime;
    }

    setSpecificLocation = function(specificLocation) {
        this.#specificLocation = specificLocation;
    }

    setPerformers = function(performers) {
        this.#performers = performers || [];
    }

    setDeleted = function(deleted) {
        this.#deleted = deleted;
    }

    addPerformer = function(performerId) {
        if (performerId && !this.#performers.includes(performerId)) {
            this.#performers.push(performerId);
        }
    }

    removePerformer = function(performerId) {
        this.#performers = this.#performers.filter(id => id !== performerId);
    }

    hasPerformers = function() {
        return this.#performers.length > 0;
    }

    countPerformers = function() {
        return this.#performers.length;
    }

    formLoad = function(form) {
        if (!form) return;

        if (form.name) form.name.value = this.#name || '';
        if (form.type) form.type.value = this.#type || 'OTHER';
        if (form.eventId) form.eventId.value = this.#eventId || '';
        if (form.specificLocation) form.specificLocation.value = this.#specificLocation || '';

        if (form.startDate) form.startDate.value = this.getStartDate();
        if (form.startTime) form.startTime.value = this.getStartTime();
        if (form.endDate) form.endDate.value = this.getEndDate();
        if (form.endTime) form.endTime.value = this.getEndTime();

        if (form.performers) {
            if (form.performers.type === 'checkbox') {
                const checkboxes = form.querySelectorAll('input[name="performers[]"]');
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.#performers.includes(parseInt(checkbox.value));
                });
            } else if (form.performers.multiple) {
                Array.from(form.performers.options).forEach(option => {
                    option.selected = this.#performers.includes(parseInt(option.value));
                });
            }
        }
    }

    formExtract = function(form) {
        if (!form) return;

        this.#name = form.name?.value || null;
        this.#type = form.type?.value || 'OTHER';
        this.#eventId = form.eventId?.value ? parseInt(form.eventId.value) : null;
        this.#specificLocation = form.specificLocation?.value || null;

        if (form.startDate?.value && form.startTime?.value) {
            this.#startDatetime = `${form.startDate.value} ${form.startTime.value}`;
        }
        if (form.endDate?.value && form.endTime?.value) {
            this.#endDatetime = `${form.endDate.value} ${form.endTime.value}`;
        }

        this.#performers = [];
        if (form.performers) {
            if (form.performers.type === 'checkbox') {
                const checkboxes = form.querySelectorAll('input[name="performers[]"]:checked');
                checkboxes.forEach(checkbox => {
                    this.#performers.push(parseInt(checkbox.value));
                });
            } else if (form.performers.multiple) {
                const selectedOptions = Array.from(form.performers.selectedOptions);
                this.#performers = selectedOptions.map(option => parseInt(option.value));
            }
        }
    }

    findById = async function(id) {
        try {
            const response = await fetch(`/api/attraction/${id}`);
            if (!response.ok) {
                throw new Error('Erro ao buscar atração');
            }
            const data = await response.json();
            this.fill(data);
            return true;
        } catch (error) {
            console.error('Erro ao buscar atração:', error);
            return false;
        }
    }

    static findByEventId = async function(eventId) {
        try {
            const response = await fetch(`/api/attraction/events/${eventId}/`);
            if (!response.ok) {
                throw new Error('Erro ao buscar atrações do evento');
            }
            const data = await response.json();
            return data.map(item => {
                const attraction = new Attraction();
                attraction.fill(item);
                return attraction;
            });
        } catch (error) {
            console.error('Erro ao buscar atrações do evento:', error);
            return [];
        }
    }

    create = async function() {
        try {
            const url = `/api/attraction/event/${eventId}`;

            const response = await fetch(url, {
                method: POST,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.toJSON())
            });

            if (!response.ok) {
                throw new Error('Erro ao salvar atração');
            }

            const data = await response.json();
            if (data.id) this.#id = data.id;
            return true;
        } catch (error) {
            console.error('Erro ao salvar atração:', error);
            return false;
        }
    }

    delete = async function() {
        try {
            const response = await fetch(`/api/attraction/${this.#id}`, {
                method: 'DELETE'
            });

            if (!response.ok) {
                throw new Error('Erro ao deletar atração');
            }

            return true;
        } catch (error) {
            console.error('Erro ao deletar atração:', error);
            return false;
        }
    }

    fill = function(data) {
        if (!data) return;

        this.#id = data.id || null;
        this.#name = data.name || null;
        this.#type = data.type || 'OTHER';
        this.#eventId = data.eventId || null;
        this.#startDatetime = data.startDatetime || null;
        this.#endDatetime = data.endDatetime || null;
        this.#specificLocation = data.specificLocation || null;
        this.#performers = data.performers || [];
        this.#deleted = data.deleted || false;
    }

    toJSON = function() {
        return {
            id: this.#id,
            name: this.#name,
            type: this.#type,
            eventId: this.#eventId,
            startDatetime: this.#startDatetime,
            endDatetime: this.#endDatetime,
            specificLocation: this.#specificLocation,
            performers: this.#performers,
            deleted: this.#deleted
        };
    }
}