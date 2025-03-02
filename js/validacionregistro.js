document.addEventListener("DOMContentLoaded", function () {
    const form = document.getElementById("registroForm");
    const inputs = form.querySelectorAll("input, select");
    const errores = {};
    
    const reglas = {
        id_usuario: /^[0-9]{6,10}$/, // Solo números, entre 6 y 10 caracteres
        user_name: /^[a-zA-Z0-9_-]{4,40}$/, // Letras, números, guion y guion bajo, entre 4 y 40 caracteres
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/, // Formato de email válido
        contra: /^.{6,20}$/, // Entre 6 y 20 caracteres
        contra2: (value) => value === document.getElementById("contra").value, // Coincide con la contraseña
    };
    
    function validarInput(input) {
        const id = input.id;
        let valido = true;
        
        if (id in reglas) {
            if (typeof reglas[id] === "function") {
                valido = reglas[id](input.value);
            } else {
                valido = reglas[id].test(input.value);
            }
        }
        
        if (!valido || input.value.trim() === "") {
            mostrarError(input, "Dato inválido");
            errores[id] = true;
        } else {
            quitarError(input);
            delete errores[id];
        }
    }
    
    function mostrarError(input, mensaje) {
        let errorDiv = input.nextElementSibling;
        if (!errorDiv || !errorDiv.classList.contains("error")) {
            errorDiv = document.createElement("div");
            errorDiv.classList.add("error");
            input.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = mensaje;
        input.classList.add("input-error");
    }
    
    function quitarError(input) {
        let errorDiv = input.nextElementSibling;
        if (errorDiv && errorDiv.classList.contains("error")) {
            errorDiv.remove();
        }
        input.classList.remove("input-error");
    }
    
    inputs.forEach(input => {
        input.addEventListener("input", () => validarInput(input));
    });
    
    form.addEventListener("submit", function (event) {
        event.preventDefault(); // Evita el envío normal
        inputs.forEach(input => validarInput(input));

        if (Object.keys(errores).length > 0) {
            alert("Por favor, corrige los errores antes de enviar.");
            return;
        }

        // Enviar datos con fetch
        let formData = new FormData(form);

        fetch("registro.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.json()) // Suponiendo que `registro.php` responde con JSON
        .then(data => {
            if (data.status === "success") {
                alert(data.message);
                window.location.href = "login.php"; // Redirige al login si todo está bien
            } else {
                alert("Error en el registro: " + data.message);
            }
        })
        .catch(error => console.error("Error:", error));
    });
});
