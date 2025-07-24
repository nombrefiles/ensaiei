// document.addEventListener("DOMContentLoaded", async function () {
//     const token = localStorage.getItem("token");
//
//     if (!token) {
//         alert("Você não está logado.");
//         window.location.href = "/";
//         return;
//     }
//
//     try {
//         const response = await fetch("http://localhost/ensaiei-main/api/users/profile", {
//             method: "GET",
//             headers: {
//                 "Content-Type": "application/json",
//                 "token": token
//             }
//         });
//
//         if (!response.ok) {
//             const errorData = await response.json();
//             alert("Erro ao carregar perfil: " + errorData.message);
//             return;
//         }
//
//         const data = await response.json();
//         const user = data.data;
//
//         document.querySelector(".foto-perfil").src = user.photo;
//         document.querySelector("h1").textContent = user.name;
//         document.querySelector(".arroba").textContent = "@" + user.username;
//         document.querySelector(".bio").textContent = user.bio;
//
//     } catch (err) {
//         console.error("Erro ao buscar perfil:", err);
//         alert("Erro de conexão. Tente novamente mais tarde.");
//     }
// });
