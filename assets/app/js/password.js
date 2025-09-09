document.getElementById("password-form").addEventListener("submit", async function (e) {
    e.preventDefault();

    const newPassword = document.getElementById("newPassword").value.trim();
    const oldPassword = document.getElementById("oldPassword").value.trim();
    const body = new URLSearchParams();
    body.append("newPassword", newPassword);
    body.append("oldPassword", oldPassword);
    const token = localStorage.getItem("token");

    console.log("tentando a troca de senha com:", { newPassword, oldPassword });

    try {
        const response = await fetch("http://localhost/ensaiei-main/api/users/password", {
            method: "PUT",
            headers: {
                "Content-Type": "application/json",
                "token": token
            },
            body: body
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
            alert(data.message || "Erro ao alterar senha");
            return;
        }

        alert(data.message || "Senha alterada com sucesso!");
        window.location.href = "http://localhost/ensaiei-main/app/perfil";

    } catch (err) {
        console.error("Erro ao conectar com o servidor:", err);
        alert("Erro ao conectar com o servidor. Tente novamente mais tarde.");
    }
});
