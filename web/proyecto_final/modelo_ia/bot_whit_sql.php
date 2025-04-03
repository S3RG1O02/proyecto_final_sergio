<?php
include_once "../src/tools_escential.php";
set_time_limit(500);
$conexion_db = conexion_db("localhost", "crud", "root", "0205");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Obtén el mensaje del formulario
    
    $mensaje = $_GET['mensaje'];
    $ia_context = "Responde estrictamente en formato JSON NO PONGAS EL JSON PON DIRECTAMENTE uno o otro. con 'sql' para consultas y 'respuesta' solo pon uno de los dos, si hay sql no hay respuesta. Tambien mencionarte el schema de mi tabla empleados, tiene los campos id, DNI, nombre, apellidos, direccion, sede, imagen_empleado";

    // URL de la API de LM Studio
    //$url = "http://192.168.119.1:1234/v1/chat/completions";

    // URL DE OLLAMA 
    $url = "http://127.0.0.1:11434/api/generate";

    $url_local = "http://127.0.0.1:1234/v1/chat/completions";

    $data = [
        "model" => "deepseek-r1-distill-qwen-14b",
        /* FORMA DE LM_STUDIO
        "messages" => [
            ["role" => "system", "content" => $ia_context],
            ["role" => "user", "content" => $mensaje]
        ],
        */
        "prompt" => "$ia_context\n\nUsuario: $mensaje\n\nIA:",
        "temperature" => 0.7,
        "max_tokens" => 4000,
        // "stream" => true
    ];
    // Convierte el array a formato JSON
    $payload = json_encode($data);

    // Inicializa cURL
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

    // Ejecuta la petición
    $response = curl_exec($ch);
    curl_close($ch);

    // Decodifica la respuesta JSON
    $responseData = json_decode($response, true);

    // Extrae el contenido de la respuesta
    $respuestaIA = $responseData['choices'][0]['message']['content'];
    $pos = strpos($respuestaIA, "</think>");


    // Si la etiqueta se encuentra, extraer la parte después de </think>
    if ($pos !== false) {
    // Sumar la longitud de la etiqueta "</think>" para empezar después de ella
    $result = substr($respuestaIA, $pos + strlen("</think>"));
    }

    $result_decode = json_decode($result, true);
    if (isset($result_decode["sql"]) && !empty($result_decode["sql"])) {
        $query = $conexion_db->query($result_decode["sql"]);

        $datos = [];
        while ($dato = $query->fetch(PDO::FETCH_ASSOC)) {
            $datos[] = $dato; 
        }

        $datos = json_encode($datos);

        $prompt_nuevo = "El usuario pregunto '$mensaje', aqui estan los datos obtenidos '$datos', redacta la respuesta por favor en base a esto";

        $data_new_sql = [
            "model" => "deepseek-r1-distill-qwen-14b",
            "messages" => [["role" => "user", "content" => $prompt_nuevo]],
            "temperature" => 0.5,
            "max_tokens" => 4000,
            // "stream" => true
        ];

        $data_new_sql = json_encode($data_new_sql);

        $ch_sql = curl_init($url);
        curl_setopt($ch_sql, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_sql, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json"
        ]);
        curl_setopt($ch_sql, CURLOPT_POST, true);
        curl_setopt($ch_sql, CURLOPT_POSTFIELDS, $data_new_sql);
    
        // Ejecuta la petición
        $response = curl_exec($ch_sql);
        curl_close($ch_sql);
    
        // Decodifica la respuesta JSON
        $responseData = json_decode($response, true);
    
        // Extrae el contenido de la respuesta
        $respuestaIA = $responseData['choices'][0]['message']['content'];
        $pos = strpos($respuestaIA, "</think>");

        if ($pos !== false) {
            $resultado_con_sql = substr($respuestaIA, $pos + strlen("</think>"));
        }

        echo "</p1>$resultado_con_sql</p>";
    }

    echo json_encode(["mensaje" => $result]);
} else {
    echo json_encode(["error" => "Por favor, envía el formulario."]);
}

/*

['role' => 'assistant', 'content' => $assistant_reply]; // PARA ADICIONAR CONTENIDO
*/
?>