export class Question {
    #id;
    #idType;
    #question;
    #answer;
    #deleted;

    constructor(
        id = null,
        idType = null,
        question = null,
        answer = null,
        deleted = false
    ) {
        this.#id = id;
        this.#idType = idType;
        this.#question = question;
        this.#answer = answer;
        this.#deleted = deleted;
    }

    getId = function() {
        return this.#id;
    }

    getIdType = function() {
        return this.#idType;
    }

    getQuestion = function() {
        return this.#question;
    }

    getAnswer = function() {
        return this.#answer;
    }

    getDeleted = function() {
        return this.#deleted;
    }

    setId = function(id) {
        this.#id = id;
    }

    setIdType = function(idType) {
        this.#idType = idType;
    }

    setQuestion = function(question) {
        this.#question = question;
    }

    setAnswer = function(answer) {
        this.#answer = answer;
    }

    setDeleted = function(deleted) {
        this.#deleted = deleted;
    }

    isAnswered = function() {
        return this.#answer !== null && this.#answer.trim() !== '';
    }

    clearAnswer = function() {
        this.#answer = null;
    }

    formLoad = function(form) {
        if (!form) return;

        if (form.question) form.question.value = this.#question || '';
        if (form.answer) form.answer.value = this.#answer || '';
        if (form.idType) form.idType.value = this.#idType || '';
    }

    formExtract = function(form) {
        if (!form) return;

        this.#question = form.question?.value || null;
        this.#answer = form.answer?.value || null;
        this.#idType = form.idType?.value ? parseInt(form.idType.value) : null;
    }

    findById = async function(id) {
        try {
            const response = await fetch(`/api/questions/${id}`);
            if (!response.ok) {
                throw new Error('Erro ao buscar pergunta');
            }
            const data = await response.json();
            this.fill(data);
            return true;
        } catch (error) {
            console.error('Erro ao buscar pergunta:', error);
            return false;
        }
    }

    static findByType = async function(idType) {
        try {
            const response = await fetch(`/api/questions/type/${idType}`);
            if (!response.ok) {
                throw new Error('Erro ao buscar perguntas por tipo');
            }
            const data = await response.json();
            return data.map(item => {
                const question = new Question();
                question.fill(item);
                return question;
            });
        } catch (error) {
            console.error('Erro ao buscar perguntas por tipo:', error);
            return [];
        }
    }

    static findAll = async function() {
        try {
            const response = await fetch('/api/questions');
            if (!response.ok) {
                throw new Error('Erro ao buscar perguntas');
            }
            const data = await response.json();
            return data.map(item => {
                const question = new Question();
                question.fill(item);
                return question;
            });
        } catch (error) {
            console.error('Erro ao buscar perguntas:', error);
            return [];
        }
    }

    static findUnanswered = async function(idType = null) {
        try {
            const url = idType
                ? `/api/questions/unanswered?idType=${idType}`
                : '/api/questions/unanswered';

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Erro ao buscar perguntas não respondidas');
            }
            const data = await response.json();
            return data.map(item => {
                const question = new Question();
                question.fill(item);
                return question;
            });
        } catch (error) {
            console.error('Erro ao buscar perguntas não respondidas:', error);
            return [];
        }
    }

    save = async function() {
        try {
            const url = this.#id ? `/api/questions/${this.#id}` : '/api/questions';
            const method = this.#id ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.toJSON())
            });

            if (!response.ok) {
                throw new Error('Erro ao salvar pergunta');
            }

            const data = await response.json();
            if (data.id) this.#id = data.id;
            return true;
        } catch (error) {
            console.error('Erro ao salvar pergunta:', error);
            return false;
        }
    }

    saveAnswer = async function() {
        if (!this.#id) return false;

        try {
            const response = await fetch(`/api/questions/${this.#id}/answer`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ answer: this.#answer })
            });

            if (!response.ok) {
                throw new Error('Erro ao salvar resposta');
            }

            return true;
        } catch (error) {
            console.error('Erro ao salvar resposta:', error);
            return false;
        }
    }

    delete = async function() {
        if (!this.#id) return false;

        try {
            const response = await fetch(`/api/questions/${this.#id}`, {
                method: 'DELETE'
            });

            if (!response.ok) {
                throw new Error('Erro ao deletar pergunta');
            }

            return true;
        } catch (error) {
            console.error('Erro ao deletar pergunta:', error);
            return false;
        }
    }

    fill = function(data) {
        if (!data) return;

        this.#id = data.id || null;
        this.#idType = data.idType || null;
        this.#question = data.question || null;
        this.#answer = data.answer || null;
        this.#deleted = data.deleted || false;
    }

    toJSON = function() {
        return {
            id: this.#id,
            idType: this.#idType,
            question: this.#question,
            answer: this.#answer,
            deleted: this.#deleted
        };
    }

    validate = function() {
        const errors = [];

        if (!this.#question || this.#question.trim() === '') {
            errors.push('A pergunta não pode estar vazia');
        }

        if (this.#question && this.#question.length < 10) {
            errors.push('A pergunta deve ter pelo menos 10 caracteres');
        }

        if (this.#question && this.#question.length > 500) {
            errors.push('A pergunta deve ter no máximo 500 caracteres');
        }

        if (!this.#idType) {
            errors.push('O tipo da pergunta é obrigatório');
        }

        return {
            valid: errors.length === 0,
            errors: errors
        };
    }

    static getStats = async function(idType = null) {
        try {
            const url = idType
                ? `/api/questions/stats?idType=${idType}`
                : '/api/questions/stats';

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error('Erro ao buscar estatísticas');
            }
            return await response.json();
        } catch (error) {
            console.error('Erro ao buscar estatísticas:', error);
            return {
                total: 0,
                answered: 0,
                unanswered: 0
            };
        }
    }
}