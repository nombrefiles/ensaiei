document.addEventListener("DOMContentLoaded", async function () {
    const token = localStorage.getItem("token");
    if (!token) {
        alert("Você não está logado.");
        window.location.href = "/";
        return;
    }
    await loadProfile();

    document.getElementById("photoInput").addEventListener("change", async function () {
        const file = this.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("photo", file);

        try {
            const response = await fetch("http://localhost/ensaiei-main/api/users/photo", {
                method: "POST",
                headers: { "token": token },
                body: formData
            });
            const data = await response.json();
            if (!response.ok) {
                alert("Erro: " + data.message);
                return;
            }
            document.querySelector(".foto-perfil").src = data.data?.photo || document.querySelector(".foto-perfil").src;
        } catch {
            alert("Erro de conexão. Tente novamente mais tarde.");
        }
    });
});

async function loadProfile() {
    const token = localStorage.getItem("token");
    try {
        const response = await fetch("http://localhost/ensaiei-main/api/users/perfil", {
            method: "GET",
            headers: { "Content-Type": "application/json", "token": token }
        });
        if (!response.ok) {
            const errorData = await response.json();
            alert("Erro ao carregar perfil: " + errorData.message);
            return;
        }
        const data = await response.json();
        const user = data.data;
        document.querySelector(".foto-perfil").src = user.photo;
        document.querySelector("h1").textContent = user.name;
        document.querySelector(".arroba").textContent = "@" + user.username;
        document.querySelector(".bio").textContent = user.bio;
    } catch {
        alert("Erro de conexão. Tente novamente mais tarde.");
    }
}

function editProfile() {
    const modal = document.querySelector(".modal");
    const nameInput = document.getElementById("nameInput");
    const usernameInput = document.getElementById("usernameInput");
    const bioInput = document.getElementById("bioInput");
    if (!modal || !nameInput || !usernameInput || !bioInput) return;

    nameInput.value = document.querySelector("h1").textContent;
    usernameInput.value = document.querySelector(".arroba").textContent.replace("@", "");
    bioInput.value = document.querySelector(".bio").textContent;

    modal.classList.remove("hidden");
}

function closeModal() {
    const modal = document.querySelector(".modal");
    if (modal) modal.classList.add("hidden");
}

async function saveProfile() {
    const token = localStorage.getItem("token");
    const nameInput = document.getElementById("nameInput");
    const usernameInput = document.getElementById("usernameInput");
    const bioInput = document.getElementById("bioInput");

    const updateData = {
        name: nameInput.value.trim(),
        username: usernameInput.value.trim(),
        bio: bioInput.value.trim()
    };

    if (!updateData.name || !updateData.username || !updateData.bio) return;

    try {
        const response1 = await fetch("http://localhost/ensaiei-main/api/users/update", {
            method: "PUT",
            headers: { "Content-Type": "application/json", "token": token },
            body: JSON.stringify(updateData)
        });
        if (response1.ok) {
            document.querySelector("h1").textContent = updateData.name;
            document.querySelector(".arroba").textContent = "@" + updateData.username;
            document.querySelector(".bio").textContent = updateData.bio;
            closeModal();
            return;
        }

        const formData = new FormData();
        formData.append('name', updateData.name);
        formData.append('username', updateData.username);
        formData.append('bio', updateData.bio);
        const response2 = await fetch("http://localhost/ensaiei-main/api/users/update", {
            method: "PUT",
            headers: { "token": token },
            body: formData
        });
        if (response2.ok) {
            document.querySelector("h1").textContent = updateData.name;
            document.querySelector(".arroba").textContent = "@" + updateData.username;
            document.querySelector(".bio").textContent = updateData.bio;
            closeModal();
            return;
        }

        const urlEncodedData = new URLSearchParams();
        urlEncodedData.append('name', updateData.name);
        urlEncodedData.append('username', updateData.username);
        urlEncodedData.append('bio', updateData.bio);
        const response3 = await fetch("http://localhost/ensaiei-main/api/users/update", {
            method: "PUT",
            headers: { "Content-Type": "application/x-www-form-urlencoded", "token": token },
            body: urlEncodedData
        });
        if (response3.ok) {
            document.querySelector("h1").textContent = updateData.name;
            document.querySelector(".arroba").textContent = "@" + updateData.username;
            document.querySelector(".bio").textContent = updateData.bio;
            closeModal();
            return;
        }

        alert("Algo de errado aconteceu, tente novamente mais tarde.");

    } catch {
        alert("Erro de conexão. Tente novamente mais tarde.");
    }
}
