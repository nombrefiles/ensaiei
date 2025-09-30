document.getElementById("register-form").addEventListener("submit", async (e) => {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmar = document.getElementById("confirmar").value;
    const username = email.split("@")[0];

    if (!name || !email || !password) {
        alert("Preencha todos os campos.");
        return;
    }

    if (password !== confirmar) {
        alert("Senhas não coincidem.");
        return;
    }

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        alert("Digite um email válido.");
        return;
    }

    if (password.length < 6) {
        alert("A senha deve ter no mínimo 6 caracteres.");
        return;
    }

    const formData = new URLSearchParams();
    formData.append("name", name);
    formData.append("email", email);
    formData.append("password", password);
    formData.append("username", username);

    const submitBtn = e.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.disabled = true;
    submitBtn.textContent = "Criando conta...";

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
            const verificationData = {
                userId: data.data.userId,
                email: data.data.email,
                name: data.data.name
            };

            localStorage.setItem("verificationData", JSON.stringify(verificationData));

            alert("Conta criada! Verifique seu email para ativar sua conta.");

            window.location.href = "http://localhost/ensaiei-main/email";
        } else {
            alert(`Erro: ${data.message || "Não foi possível criar o usuário."}`);
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    } catch (error) {
        alert("Erro na conexão, tente novamente.");
        console.error(error);
        submitBtn.disabled = false;
        submitBtn.textContent = originalText;
    }
});