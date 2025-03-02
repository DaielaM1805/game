document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("loginForm");

    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Evitamos que el formulario se envíe automáticamente

        const usuario = document.getElementById("usuario").value.trim();
        const contra = document.getElementById("contra").value.trim();
        const mensajeDiv = document.getElementById("mensaje");

        if (usuario === "" || contra === "") {
            mensajeDiv.innerHTML = '<p class="text-danger">Todos los campos son obligatorios.</p>';
            return;
        }

        // Enviar datos con Fetch API
        fetch("/game/include/procesarlogin.php", {
            method: "POST",
            body: new URLSearchParams(new FormData(form)),
            headers: { "Content-Type": "application/x-www-form-urlencoded" }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === "success") {
                window.location.href = "http://localhost/game/jugador/index.php"; 
            } else {
                mensajeDiv.innerHTML = `<p class="text-danger">${data.message}</p>`;
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
