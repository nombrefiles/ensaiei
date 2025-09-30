document.getElementById("email").addEventListener("submit", async function (e) {
    e.preventDefault();

    console.log("clicou no botao")

    try{
        const response = await fetch("http://localhost/ensaiei-main/api/users/confirm", {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({ user, password })
        });
        console.log(response);
    }catch (e){

    }
});
