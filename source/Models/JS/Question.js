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
}