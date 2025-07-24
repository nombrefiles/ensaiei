document.getElementById("register-form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmar = document.getElementById("confirmar").value;
    const username = email.split("@")[0]; // gera username simples

    if (!name || !email || !password) {
        alert("Preencha todos os campos.");
        return;
    }
    if (password !== confirmar) {
        alert("Senhas não coincidem.");
        return;
    }

    const formData = new URLSearchParams();
    formData.append("name", name);
    formData.append("email", email);
    formData.append("password", password);
    formData.append("username", username);

    try {
        const response = await fetch("http://localhost/ensaiei-main/api/users/add", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: formData.toString(),
        });

        const data = await response.json();

        if (response.ok) {
            alert("Usuário criado com sucesso!");
            window.location.href = "http://localhost/ensaiei-main/login";
        } else {
            alert(`Erro: ${data.message || "Não foi possível criar o usuário."}`);
        }
    } catch (error) {
        alert("Erro na conexão, tente novamente.");
        console.error(error);
    }
});
