document.getElementById("login-form").addEventListener("submit", async function (e) {
    e.preventDefault();

    const email = document.getElementById("user").value.trim();
    const error = document.querySelector(".error").value.trim();

    try {
        const response = await fetch("http://localhost/ensaiei-main/api/users/getemail", {
            method: "GET",
            headers: {
                "email": email
            },
        });

        console.log("Resposta HTTP:", response.status, response.statusText);

        let data;

        try {
            data = await response.json();
            console.log("Resposta JSON da API:", data);
            localStorage.setItem("forgotData", JSON.stringify(data.data.email));
        } catch (jsonError) {
            const text = await response.text();
            console.warn("Resposta não é JSON, texto recebido:", text);
        }

    } catch (err) {
        console.error("Erro ao conectar com o servidor:", err);
    }
});