document.getElementById("login-form").addEventListener("submit", async function (e) {
    e.preventDefault();

    const user = document.getElementById("user").value.trim();
    const password = document.getElementById("password").value.trim();

    console.log("Tentando login com:", { user, password });

    try {
        const response = await fetch("http://localhost/ensaiei-main/api/users/login", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ user, password })
        });

        console.log("Resposta HTTP:", response.status, response.statusText);

        let data;

        try {
            data = await response.json();
            console.log("Resposta JSON da API:", data);
        } catch (jsonError) {
            const text = await response.text();
            console.warn("Resposta não é JSON, texto recebido:", text);
            alert("Erro inesperado da API:\n" + text);
            return;
        }

        if (!response.ok) {
            alert(data.message || "Email ou senha inválidos");
            return;
        }

        localStorage.setItem("token", data.token);
        window.location.href = "app/perfil";

    } catch (err) {
        console.error("Erro ao conectar com o servidor:", err);
        alert("Erro ao conectar com o servidor. Tente novamente mais tarde.");
    }
});
