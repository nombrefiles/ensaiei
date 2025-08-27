document.addEventListener("DOMContentLoaded", async function () {
    const token = localStorage.getItem("token");

    if (!token) {
        alert("Você não está logado.");
        window.location.href = "/";
        return;
    }

    try {
        const response = await fetch("http://localhost/ensaiei-main/api/users/perfil", {
            method: "GET",
            headers: {
                "Content-Type": "application/json",
                "token": token
            }
        });

        if (!response.ok) {
            const errorData = await response.json();
            alert("Erro ao carregar perfil: " + errorData.message);
            return;
        }

        const data = await response.json();
        const user = data.data;
        console.log(user);

        document.querySelector(".foto-perfil").src = user.photo;
        document.querySelector("h1").textContent = user.name;
        document.querySelector(".arroba").textContent = "@" + user.username;
        document.querySelector(".bio").textContent = user.bio;

    } catch (err) {
        console.error("Erro ao buscar perfil:", err);
        alert("Erro de conexão. Tente novamente mais tarde.");
    }

    document.getElementById("photoInput").addEventListener("change", async function () {
        const file = this.files[0];
        if (!file) return;

        const token = localStorage.getItem("token");
        const formData = new FormData();
        formData.append("photo", file);

        try {
            const response = await fetch("http://localhost/ensaiei-main/api/users/photo", {
                method: "POST",
                headers: {
                    "token": token
                },
                body: formData
            });

            const data = await response.json();

            if (!response.ok) {
                alert("Erro: " + data.message);
                return;
            }

            document.querySelector(".foto-perfil").src = data.data?.photo || document.querySelector(".foto-perfil").src;
            alert("Foto atualizada com sucesso!");

        } catch (err) {
            console.error("Erro:", err);
            alert("Erro de conexão. Tente novamente mais tarde.");
        }
    });

});
