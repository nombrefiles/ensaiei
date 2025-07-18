document.getElementById('form-login').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = {
        user: document.getElementById('username').value,
        password: document.getElementById('senha').value
    };
    
    try {
        const res = await fetch('http://localhost/ensaiei/api/users/login', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });
        
        const data = await res.json();
        
        if (res.ok) {
            localStorage.setItem('token', data.token);
            window.location.href = '/ensaiei-main/';
        } else {
            alert('Usuário ou senha inválidos');
        }
    } catch (error) {
        console.error('Erro ao fazer login:', error);
        alert('Erro ao tentar fazer login');
    }
});