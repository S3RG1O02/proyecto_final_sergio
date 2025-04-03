
function show(id, nombre) {
    let boton = document.getElementById("button_eliminar_" + id);

    let respuesta = confirm("Seguro quieres eliminar al empleado: " + nombre + "?");

    if (!respuesta) {
        boton.addEventListener("submit", function(event) {
            event.preventDefault();
        });;
        boton.name = "";
        boton.value = "";
    } else {
    }
}

function cambiarClase(claseActual, claseNueva, elemento_id) {
    const elemento = document.getElementById(elemento_id);
    if (elemento.classList.contains(claseActual)) {
        elemento.classList.remove(claseActual);
        if (claseNueva) {
            elemento.classList.add(claseNueva);
        }
    } else {
        elemento.remove();
    }
}