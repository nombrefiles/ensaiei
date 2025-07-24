// document.getElementById("register-form").addEventListener("submit", async function (e) {
//     e.preventDefault();
//
//     const name = document.getElementById("name").value.trim();
//     const email = document.getElementById("email").value.trim();
//     const password = document.getElementById("password").value.trim();
//
//     try {
//         const response = await fetch("http://localhost/ensaiei-main/api/users/login/add", {
//             method: "POST",
//             headers: { "Content-Type": "application/json" },
//             body: JSON.stringify({ name, email, password })
//         });
//
//         if (!response.ok) {
//             const err = await response.json();
//             alert(err.message || "Erro no cadastro");
//             return;
//         }
//
//         alert("Cadastro realizado com sucesso!");
//         window.location.href = "/login"; // redireciona
//     } catch (error) {
//         console.error(error);
//         alert("Erro ao cadastrar");
//     }
// });
