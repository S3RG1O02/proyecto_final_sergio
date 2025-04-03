document.getElementById('input_ia').addEventListener('input', function () {
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('enviar_mensaje').click();
        }
    })
    this.style.height = 'auto';  // Resetea la altura antes de medir
    this.style.height = (this.scrollHeight) + 'px';  // Ajusta la altura al contenido

});


var div_principal = document.getElementById('text_inicial');
var input_ia = document.getElementById('input_ia');
var app_container = document.getElementById('app_container');

document.getElementById('enviar_mensaje').addEventListener('click', function () {
    if (div_principal && !div_principal.classList.contains('hidden')) {
        div_principal.remove();
    }

    contador_respuesta_user++;
    contador_respuesta_ia++; 

    var mensaje_user_dibujo = `<div id='user_response${contador_respuesta_user}' class='flex flex-col justify-end items-end w-11/12 md:w-3/5'><p id='mensajeIA_${contador_respuesta_user}' class='bg-[rgb(48,48,48)] rounded-lg px-3 py-4 text-white'></p></div>`;
    var mensaje_ia_dibujo = `
    <div id='ia_response${contador_respuesta_ia}' class='flex flex-col justify-end items-start w-11/12 md:w-3/5 my-3'>
    <p id='respuestaIA_${contador_respuesta_ia}' class='rounded-lg px-3 py-4 text-white'>
        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 200 200' class='h-10' id='loading_chat'>
            <circle fill='#000000' stroke='#000000' stroke-width='15' r='15' cx='40' cy='100'>
                <animate attributeName='opacity' calcMode='spline' dur='2' values='1;0;1;' keySplines='.5 0 .5 1;.5 0 .5 1' repeatCount='indefinite' begin='-.4'></animate>
            </circle>
            <circle fill='#000000' stroke='#000000' stroke-width='15' r='15' cx='100' cy='100'>
                <animate attributeName='opacity' calcMode='spline' dur='2' values='1;0;1;' keySplines='.5 0 .5 1;.5 0 .5 1' repeatCount='indefinite' begin='-.2'></animate>
            </circle>
            <circle fill='#000000' stroke='#000000' stroke-width='15' r='15' cx='160' cy='100'>
                <animate attributeName='opacity' calcMode='spline' dur='2' values='1;0;1;' keySplines='.5 0 .5 1;.5 0 .5 1' repeatCount='indefinite' begin='0'></animate>
            </circle>
        </svg>
    </p>
    </div>
    `;

    document.getElementById("input_mensajes").insertAdjacentHTML('beforebegin', mensaje_user_dibujo);
    document.getElementById(`mensajeIA_${contador_respuesta_user}`).innerHTML = input_ia.value;
    document.getElementById("input_mensajes").insertAdjacentHTML('beforebegin', mensaje_ia_dibujo);

    input_ia.value = '';
    document.getElementById('input_ia').style.height = 'auto';
});








function prueba_ia() {
    var mensaje2 = document.getElementById('input_ia').value; // Obtener el mensaje del textarea
    // ESTO ES PARA CONVERTIR EL TEXTO EN UN HTML CON SUS CARACTERISTICAS.
    var converter = new showdown.Converter();
    var url2 = 'http://localhost:3000/modelo_ia/bot_ia.php'; // Ajusta la URL si es necesario

    document.getElementById("enviar_mensaje").disabled = true;
    document.getElementById("enviar_mensaje").classList.replace("bg-white", "bg-gray-500");
    document.getElementById("enviar_mensaje").classList.replace("hover:bg-gray-200", "hover:bg-gray-500");

    $.ajax({
        type: 'GET', // Cambié de POST a GET, ya que estás pasando los datos en la URL
        url: url2,
        data: {mensaje: mensaje2, id_user: usuario_para_ia, id_chat : id_chat_us,}, 
        dataType: 'json',
        success: function (data) {
            if (data.mensaje) {
                var mensaje_converter = converter.makeHtml(data.mensaje);
                document.getElementById("enviar_mensaje").disabled = false;
                document.getElementById("enviar_mensaje").classList.replace("bg-gray-500", "bg-white");
                document.getElementById("enviar_mensaje").classList.replace("hover:bg-gray-500", "hover:bg-gray-200");
                document.getElementById("loading_chat").remove();
                document.getElementById(`respuestaIA_${contador_respuesta_ia}`).innerHTML = mensaje_converter;
            } else if (data.error) {
                document.getElementById(`respuestaIA_${contador_respuesta_ia}`).innerText = data.error;  // Cambié data.mensaje por data.error aquí
            }
        },
        error: function (xhr, status, error) {
            document.getElementById("enviar_mensaje").disabled = false;
            document.getElementById("loading_chat").remove();
            document.getElementById("enviar_mensaje").classList.replace("bg-gray-500", "bg-white");
            document.getElementById("enviar_mensaje").classList.replace("hover:bg-gray-500", "hover:bg-gray-200");
            document.getElementById(`respuestaIA_${contador_respuesta_ia}`).innerText = "Error en el servidor de IA, intente de nuevo en unos minutos...";
            console.error('Error en AJAX:', xhr.status, error);
        }
    });
    return true; // Evita la recarga de la página
}



function activar_menu() {
    var button = document.getElementById("button_menu_mobile");

    button.addEventListener("click", function() {
        var menu_chats = document.getElementById("menu_chats");

        if (menu_chats.classList.contains("hidden")) {
            menu_chats.classList.replace("hidden", "flex")
            document.getElementById("menu_chats")
            button.classList.replace("absolute", "relative");
        } else {
            menu_chats.classList.replace("flex", "hidden");
            button.classList.replace("relative", "absolute");
        }


    })
}