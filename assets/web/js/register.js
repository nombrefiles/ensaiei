document.getElementById('form-register').addEventListener('submit', async (e) => {
    e.preventDefault();

    const senha = document.getElementById('senha').value;
    const confirmar = document.getElementById('confirmar').value;

    if (senha !== confirmar) {
        alert('As senhas não conferem!');
        return;
    }

    const formData = {
        name: document.getElementById('nome').value,
        username: document.getElementById('nome').value.toLowerCase().replace(/\s+/g, ''),
        email: document.getElementById('email').value,
        password: senha
    };

    console.log("Enviando dados:", formData);

    try {
        const res = await fetch('http://localhost/ensaiei-main/api/users/add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        });

        const text = await res.text();
        console.log("Resposta bruta do servidor:", text);

        try {
            const data = JSON.parse(text);
            console.log("JSON da resposta:", data);

            if (res.ok) {
                alert('Cadastro realizado com sucesso!');
                window.location.href = 'login.html';
            } else {
                alert(`Erro no cadastro: ${data.message || 'Erro desconhecido'}`);
            }
        } catch (jsonError) {
            console.error("Erro ao decodificar JSON:", jsonError);
            alert("Resposta do servidor não é um JSON válido.");
        }
    } catch (error) {
        console.error('Erro ao fazer cadastro:', error);
        alert('Erro ao tentar realizar o cadastro. Verifique sua conexão ou tente mais tarde.');
    }
});
