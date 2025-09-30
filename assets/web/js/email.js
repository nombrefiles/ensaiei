document.addEventListener("DOMContentLoaded", function() {
    const verificationData = JSON.parse(localStorage.getItem("verificationData"));

    if (!verificationData || !verificationData.userId || !verificationData.email) {
        alert("Dados de verificação não encontrados. Redirecionando...");
        window.location.href = "http://localhost/ensaiei-main/cadastro";
        return;
    }

    document.getElementById("emailDisplay").textContent = verificationData.email;

    const inputs = document.querySelectorAll(".code-input");
    const verifyBtn = document.getElementById("verifyBtn");
    const resendBtn = document.getElementById("resendBtn");
    const cancelBtn = document.getElementById("cancelBtn");
    const errorMessage = document.getElementById("errorMessage");
    const successMessage = document.getElementById("successMessage");
    const loading = document.getElementById("loading");
    const verifyForm = document.getElementById("verifyForm");

    inputs.forEach((input, index) => {
        input.addEventListener("input", function(e) {
            // Apenas números
            this.value = this.value.replace(/[^0-9]/g, "");

            if (this.value.length === 1 && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        input.addEventListener("keydown", function(e) {
            if (e.key === "Backspace" && !this.value && index > 0) {
                inputs[index - 1].focus();
            }
        });

        input.addEventListener("paste", function(e) {
            e.preventDefault();
            const pastedData = e.clipboardData.getData("text").replace(/[^0-9]/g, "");

            if (pastedData.length === 6) {
                inputs.forEach((input, i) => {
                    input.value = pastedData[i] || "";
                });
                inputs[5].focus();
            }
        });
    });

    // Auto-focus no primeiro input
    inputs[0].focus();

    // Função para mostrar erro
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = "block";
        successMessage.style.display = "none";
    }

    function showSuccess(message) {
        successMessage.textContent = message;
        successMessage.style.display = "block";
        errorMessage.style.display = "none";
    }

    function clearMessages() {
        errorMessage.style.display = "none";
        successMessage.style.display = "none";
    }

    verifyForm.addEventListener("submit", async function(e) {
        e.preventDefault();
        clearMessages();

        const code = Array.from(inputs).map(input => input.value).join("");

        if (code.length !== 6) {
            showError("Por favor, digite o código completo de 6 dígitos");
            return;
        }

        const formDataVerify = new URLSearchParams();
        formDataVerify.append("userId", verificationData.userId);
        formDataVerify.append("code", code);

        verifyBtn.disabled = true;
        loading.style.display = "block";

        try {
            const response = await fetch("http://localhost/ensaiei-main/api/users/verifyemail", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: formDataVerify.toString()
            });

            const data = await response.json();

            if (!response.ok) {
                showError(data.message || "Código inválido ou expirado");
                verifyBtn.disabled = false;
                loading.style.display = "none";

                inputs.forEach(input => input.value = "");
                inputs[0].focus();
                return;
            }

            showSuccess("Email verificado com sucesso! Redirecionando...");

            if (data.data && data.data.token) {
                localStorage.setItem("token", data.data.token);
            }

            localStorage.removeItem("verificationData");

            setTimeout(() => {
                window.location.href = "http://localhost/ensaiei-main/app/hi";
            }, 2000);

        } catch (error) {
            console.error("Erro ao verificar código:", error);
            showError("Erro ao conectar com o servidor. Tente novamente.");
            verifyBtn.disabled = false;
            loading.style.display = "none";
        }
    });

    resendBtn.addEventListener("click", async function() {
        if (resendBtn.disabled) return;

        clearMessages();
        resendBtn.disabled = true;
        resendBtn.textContent = "Reenviando...";

        const formDataResend = new URLSearchParams();
        formDataResend.append("userId", verificationData.userId);

        try {
            const response = await fetch("http://localhost/ensaiei-main/api/users/resendcode", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: formDataResend.toString()
            });

            const data = await response.json();

            if (!response.ok) {
                showError(data.message || "Erro ao reenviar código");
                resendBtn.disabled = false;
                resendBtn.textContent = "Não recebeu o código? Reenviar";
                return;
            }

            showSuccess("Novo código enviado! Verifique seu email.");

            inputs.forEach(input => input.value = "");
            inputs[0].focus();

            let countdown = 60;
            resendBtn.textContent = `Reenviar em ${countdown}s`;

            const interval = setInterval(() => {
                countdown--;
                resendBtn.textContent = `Reenviar em ${countdown}s`;

                if (countdown <= 0) {
                    clearInterval(interval);
                    resendBtn.disabled = false;
                    resendBtn.textContent = "Não recebeu o código? Reenviar";
                }
            }, 1000);

        } catch (error) {
            console.error("Erro ao reenviar código:", error);
            showError("Erro ao conectar com o servidor. Tente novamente.");
            resendBtn.disabled = false;
            resendBtn.textContent = "Não recebeu o código? Reenviar";
        }
    });

    // Cancelar registro
    cancelBtn.addEventListener("click", async function() {
        const confirmed = confirm(
            "Tem certeza que deseja cancelar seu registro?\n\n" +
            "Esta ação não pode ser desfeita e você precisará se cadastrar novamente."
        );

        if (!confirmed) return;

        cancelBtn.disabled = true;
        cancelBtn.textContent = "Cancelando...";

        const formDataDelete = new URLSearchParams();
        formDataDelete.append("userId", verificationData.userId);

        try {
            const response = await fetch("http://localhost/ensaiei-main/api/users/cancelregistration", {
                method: "DELETE",
                headers: {
                    "Content-Type": "application/json"
                },
                body: formDataDelete.toString()
            });

            const data = await response.json();

            if (!response.ok) {
                alert("Erro ao cancelar registro: " + (data.message || "Erro desconhecido"));
                cancelBtn.disabled = false;
                cancelBtn.textContent = "Email não existe? Cancelar registro";
                return;
            }

            alert("Registro cancelado com sucesso.");
            localStorage.removeItem("verificationData");
            window.location.href = "http://localhost/ensaiei-main/cadastro";

        } catch (error) {
            console.error("Erro ao cancelar registro:", error);
            alert("Erro ao conectar com o servidor. Tente novamente.");
            cancelBtn.disabled = false;
            cancelBtn.textContent = "Email não existe? Cancelar registro";
        }
    });
});
