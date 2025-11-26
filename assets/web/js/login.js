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

        if (response.status === 403 && data.data?.needsVerification) {
            const goToVerification = confirm(
                "Seu email ainda não foi verificado.\n\n" +
                "Clique em OK para ir para a página de verificação e inserir o código que enviamos para seu email."
            );

            if (goToVerification) {
                const verificationData = {
                    userId: data.data.userId,
                    email: user.includes('@') ? user : '', //
                };

                localStorage.setItem("verificationData", JSON.stringify(verificationData));
                window.location.href = "http://localhost/ensaiei-main/verify-email";
            }
            return;
        }

        if (!response.ok) {
            alert(data.message || "Email ou senha inválidos");
            return;
        }


        localStorage.setItem("token", data.data.token);

        console.log("DADOS RECEBIDOS:", data);
        console.log("ROLE RECEBIDA:", data.data.user.role);

        if ((data.data.user.role || "").toUpperCase() === "ADMIN") {
            window.location.href = "http://localhost/ensaiei-main/admin";
        } else {
            window.location.href = "http://localhost/ensaiei-main/app/hi";
        }

    } catch (err) {
        console.error("Erro ao conectar com o servidor:", err);
        alert("Erro ao conectar com o servidor. Tente novamente mais tarde.");
    }
});