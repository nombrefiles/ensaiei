export class User {
    #id;
    #role;
    #name;
    #email;
    #password;
    #photo;
    #username;
    #bio;
    #deleted;
    #emailVerified;
    #verificationCode;
    #verificationCodeExpires;

    constructor(
        id = null,
        role = null,
        name = null,
        email = null,
        password = null,
        photo = null,
        username = null,
        bio = null,
        deleted = false,
        emailVerified = false,
        verificationCode = null,
        verificationCodeExpires = null
    ) {
        this.#id = id;
        this.#role = role;
        this.#name = name;
        this.#email = email;
        this.#password = password;
        this.#photo = photo;
        this.#username = username;
        this.#bio = bio;
        this.#deleted = deleted;
        this.#emailVerified = emailVerified;
        this.#verificationCode = verificationCode;
        this.#verificationCodeExpires = verificationCodeExpires;
    }

    getId = function() {
        return this.#id;
    }

    getRole = function() {
        return this.#role;
    }

    getName = function() {
        return this.#name;
    }

    getEmail = function() {
        return this.#email;
    }

    getPassword = function() {
        return this.#password;
    }

    getPhoto = function() {
        return this.#photo;
    }

    getUsername = function() {
        return this.#username;
    }

    getBio = function() {
        return this.#bio;
    }

    getDeleted = function() {
        return this.#deleted;
    }

    getEmailVerified = function() {
        return this.#emailVerified;
    }

    getVerificationCode = function() {
        return this.#verificationCode;
    }

    getVerificationCodeExpires = function() {
        return this.#verificationCodeExpires;
    }

    setId = function(id) {
        this.#id = id;
    }

    setRole = function(role) {
        this.#role = role;
    }

    setName = function(name) {
        this.#name = name;
    }

    setEmail = function(email) {
        this.#email = email;
    }

    setPassword = function(password) {
        this.#password = password;
    }

    setPhoto = function(photo) {
        this.#photo = photo;
    }

    setUsername = function(username) {
        this.#username = username;
    }

    setBio = function(bio) {
        this.#bio = bio;
    }

    setDeleted = function(deleted) {
        this.#deleted = deleted;
    }

    setEmailVerified = function(emailVerified) {
        this.#emailVerified = emailVerified;
    }

    setVerificationCode = function(verificationCode) {
        this.#verificationCode = verificationCode;
    }

    setVerificationCodeExpires = function(verificationCodeExpires) {
        this.#verificationCodeExpires = verificationCodeExpires;
    }

    formLoad = function(form) {
        if (!form) return;

        if (form.name) form.name.value = this.#name || '';
        if (form.email) form.email.value = this.#email || '';
        if (form.username) form.username.value = this.#username || '';
        if (form.bio) form.bio.value = this.#bio || '';
        if (form.role) form.role.value = this.#role || '';
        if (form.photo) {
            const photoPreview = form.querySelector('.photo-preview');
            if (photoPreview && this.#photo) {
                photoPreview.src = this.getPhotoUrl();
            }
        }
    }

    formExtract = function(form) {
        if (!form) return;

        this.#name = form.name?.value || null;
        this.#email = form.email?.value || null;
        this.#username = form.username?.value || null;
        this.#bio = form.bio?.value || null;
        this.#role = form.role?.value || null;

        if (form.password?.value && form.password.value.trim() !== '') {
            this.#password = form.password.value;
        }
    }

    findById = async function(id) {
        try {
            const response = await fetch(`/api/users/${id}`);
            if (!response.ok) {
                throw new Error('Erro ao buscar usuário');
            }
            const data = await response.json();
            this.fill(data);
            return true;
        } catch (error) {
            console.error('Erro ao buscar usuário:', error);
            return false;
        }
    }

    findByEmail = async function(email) {
        try {
            const response = await fetch(`/api/users/email/${encodeURIComponent(email)}`);
            if (!response.ok) {
                throw new Error('Erro ao buscar usuário por email');
            }
            const data = await response.json();
            this.fill(data);
            return true;
        } catch (error) {
            console.error('Erro ao buscar usuário por email:', error);
            return false;
        }
    }

    findByUsername = async function(username) {
        try {
            const response = await fetch(`/api/users/username/${encodeURIComponent(username)}`);
            if (!response.ok) {
                throw new Error('Erro ao buscar usuário por username');
            }
            const data = await response.json();
            this.fill(data);
            return true;
        } catch (error) {
            console.error('Erro ao buscar usuário por username:', error);
            return false;
        }
    }

    static findAll = async function() {
        try {
            const response = await fetch('/api/users');
            if (!response.ok) {
                throw new Error('Erro ao buscar usuários');
            }
            const data = await response.json();
            return data.map(item => {
                const user = new User();
                user.fill(item);
                return user;
            });
        } catch (error) {
            console.error('Erro ao buscar usuários:', error);
            return [];
        }
    }

    login = async function(email, password) {
        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Erro ao fazer login');
            }

            const data = await response.json();
            this.fill(data.user);

            if (data.token) {
                localStorage.setItem('authToken', data.token);
            }

            return true;
        } catch (error) {
            console.error('Erro no login:', error);
            return false;
        }
    }

    static logout = async function() {
        try {
            await fetch('/api/auth/logout', {
                method: 'POST'
            });

            localStorage.removeItem('authToken');
            return true;
        } catch (error) {
            console.error('Erro no logout:', error);
            return false;
        }
    }

    register = async function() {
        try {
            const response = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.toJSON())
            });

            if (!response.ok) {
                const error = await response.json();
                throw new Error(error.message || 'Erro ao registrar usuário');
            }

            const data = await response.json();
            this.fill(data);
            return true;
        } catch (error) {
            console.error('Erro ao registrar:', error);
            return false;
        }
    }

    save = async function() {
        try {
            const url = this.#id ? `/api/users/${this.#id}` : '/api/users';
            const method = this.#id ? 'PUT' : 'POST';

            const response = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(this.toJSON())
            });

            if (!response.ok) {
                throw new Error('Erro ao salvar usuário');
            }

            const data = await response.json();
            if (data.id) this.#id = data.id;
            return true;
        } catch (error) {
            console.error('Erro ao salvar usuário:', error);
            return false;
        }
    }

    delete = async function() {
        if (!this.#id) return false;

        try {
            const response = await fetch(`/api/users/${this.#id}`, {
                method: 'DELETE'
            });

            if (!response.ok) {
                throw new Error('Erro ao deletar usuário');
            }

            return true;
        } catch (error) {
            console.error('Erro ao deletar usuário:', error);
            return false;
        }
    }

    verifyEmail = async function(code) {
        try {
            const response = await fetch('/api/auth/verify-email', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: this.#email,
                    code: code
                })
            });

            if (!response.ok) {
                throw new Error('Erro ao verificar email');
            }

            this.#emailVerified = true;
            return true;
        } catch (error) {
            console.error('Erro ao verificar email:', error);
            return false;
        }
    }

    static requestPasswordReset = async function(email) {
        try {
            const response = await fetch('/api/auth/forgot-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email })
            });

            if (!response.ok) {
                throw new Error('Erro ao solicitar recuperação de senha');
            }

            return true;
        } catch (error) {
            console.error('Erro ao solicitar recuperação de senha:', error);
            return false;
        }
    }

    static resetPassword = async function(email, code, newPassword) {
        try {
            const response = await fetch('/api/auth/reset-password', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email,
                    code,
                    newPassword
                })
            });

            if (!response.ok) {
                throw new Error('Erro ao redefinir senha');
            }

            return true;
        } catch (error) {
            console.error('Erro ao redefinir senha:', error);
            return false;
        }
    }

    uploadPhoto = async function(file) {
        try {
            const formData = new FormData();
            formData.append('photo', file);

            const response = await fetch(`/api/users/${this.#id}/photo`, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Erro ao fazer upload da foto');
            }

            const data = await response.json();
            this.#photo = data.photo;
            return true;
        } catch (error) {
            console.error('Erro ao fazer upload da foto:', error);
            return false;
        }
    }

    static getCurrentUser = async function() {
        try {
            const response = await fetch('/api/auth/me');
            if (!response.ok) {
                return null;
            }
            const data = await response.json();
            const user = new User();
            user.fill(data);
            return user;
        } catch (error) {
            console.error('Erro ao buscar usuário atual:', error);
            return null;
        }
    }

    fill = function(data) {
        if (!data) return;

        this.#id = data.id || null;
        this.#role = data.role || null;
        this.#name = data.name || null;
        this.#email = data.email || null;
        this.#password = data.password || null;
        this.#photo = data.photo || null;
        this.#username = data.username || null;
        this.#bio = data.bio || null;
        this.#deleted = data.deleted || false;
        this.#emailVerified = data.emailVerified || data.email_verified || false;
        this.#verificationCode = data.verificationCode || data.verification_code || null;
        this.#verificationCodeExpires = data.verificationCodeExpires || data.verification_code_expires || null;
    }

    toJSON = function() {
        const json = {
            id: this.#id,
            role: this.#role,
            name: this.#name,
            email: this.#email,
            photo: this.#photo,
            username: this.#username,
            bio: this.#bio,
            deleted: this.#deleted,
            emailVerified: this.#emailVerified
        };

        if (this.#password) {
            json.password = this.#password;
        }

        return json;
    }

    getPhotoUrl = function() {
        if (!this.#photo) return '/assets/images/default-avatar.png';
        return this.#photo.startsWith('http') ? this.#photo : `/uploads/users/${this.#photo}`;
    }

    validate = function() {
        const errors = [];

        if (!this.#name || this.#name.trim() === '') {
            errors.push('O nome é obrigatório');
        }

        if (!this.#email || this.#email.trim() === '') {
            errors.push('O email é obrigatório');
        } else if (!this.isValidEmail(this.#email)) {
            errors.push('Email inválido');
        }

        if (!this.#username || this.#username.trim() === '') {
            errors.push('O username é obrigatório');
        } else if (this.#username.length < 3) {
            errors.push('O username deve ter pelo menos 3 caracteres');
        }

        if (!this.#id && (!this.#password || this.#password.trim() === '')) {
            errors.push('A senha é obrigatória');
        }

        if (this.#password && this.#password.length < 6) {
            errors.push('A senha deve ter pelo menos 6 caracteres');
        }

        return {
            valid: errors.length === 0,
            errors: errors
        };
    }

    isValidEmail = function(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    isAdmin = function() {
        return this.#role === 'ADMIN';
    }

    isOrganizer = function() {
        return this.#role === 'ORGANIZER';
    }

    isPerformer = function() {
        return this.#role === 'PERFORMER';
    }

    isUser = function() {
        return this.#role === 'USER';
    }
}