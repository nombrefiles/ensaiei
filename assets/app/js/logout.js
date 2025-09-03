document.addEventListener("DOMContentLoaded", async function (){
    localStorage.removeItem("token");
    window.location.href = "http://localhost/ensaiei-main/login";
})