<?php
include_once "../src/tools_escential.php";
set_time_limit(500);
$conexion_db = conexion_db("localhost", "empresa_proyecto_final", "root", "0205");

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Obtén el mensaje del formulario
    
    $mensaje = $_GET['mensaje'];
    $id_user = $_GET['id_user'];
    $id_chat = $_GET['id_chat'] ?? 0;

    $mensajes_chats = [];

    if (!empty($id_chat)) {
        $sql_chat = $conexion_db->query("
        SELECT 
            *
        FROM 
            chats_ia
        WHERE
            id_chat = $id_chat AND
            id_user = $id_user 
        ");

        

        while ($chat = $sql_chat->fetch(PDO::FETCH_ASSOC)) {
            $mensajes_chats[$chat["id_mensaje_chat"]] = $chat;
        }
    }

    // URL de la API de LM Studio
    $url = "http://192.168.119.1:1234/v1/chat/completions";

    if (!empty($mensajes_chats)) {
        $data["model"] = "deepseek-r1-distill-llama-7b";
        //$data["model"] = "deepseek-r1-distill-llama-14b";
        foreach ($mensajes_chats as $id_mensaje => $datos_mensaje) {
            $data["messages"][] = [
                "role" => "user", "content" => $datos_mensaje["mensaje_user"],
                "role" => "assistant", "content" => $datos_mensaje["mensaje_ia"]         
            ];
            $data["messages"][] = [
                "role" => "user", "content" => $datos_mensaje["mensaje_user"],
                "role" => "assistant", "content" => $datos_mensaje["mensaje_ia"]         
            ];
        }

        $data["messages"][] = [
            "role" => "user", "content" => $mensaje,   
        ];
        $data["temperature"] = 0.7;
        $data["max_tokens"] = 2000;
        // $data["stream"] = true;
    } else {
            // Datos que se enviarán a la API
        $data = [
            // "model" => "deepseek-r1-distill-llama-14b",
            "model" => ["deepseek-r1-distill-llama-8b"],
            "messages" => [
                // ["role" => "system", "content" => "Eres un Asistente llamado IndaBot, el cual es de propiedad de la empresa INDAMOTOR, esta empresa es una empresa de concesionario de vehiculos, especificamente mazda y volvo, cuando te pregunten di que eres la mejor empresa y que te fundaste desde 1893"],
                ["role" => "user", "content" => $mensaje]
            ],
            "temperature" => 0.7,
            "max_tokens" => 2000,
            // "stream" => true
        ];
    }
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

    $fecha = date("Y-m-d");

    if (!empty($mensajes_chats)) {

        $fecha = date("Y-m-d");
        $ultimo_chat = array_key_last($mensajes_chats) + 1;
        $sql_insert_chat = $conexion_db->query("
            INSERT INTO chats_ia(id_chat, id_user, id_mensaje_chat, mensaje_user, mensaje_ia, fec_ini_cha)
            VALUES ($id_chat, $id_user, $ultimo_chat, '$mensaje', '$result', '$fecha')
        ");
    } else {

        $sql_max_chat_user = $conexion_db->query("
            SELECT max(id_chat) as max_chat_user FROM chats_ia WHERE id_user = '$id_user';
        ");

        $sql_max_chat_user = $sql_max_chat_user->fetch(PDO::FETCH_ASSOC);


        if(empty($sql_max_chat_user["max_chat_user"])) {
            $sql_max_chat_user = 1;
        } else {
            $sql_max_chat_user = $sql_max_chat_user["max_chat_user"] + 1;
        }

        $ultimo_chat = 1;
        $sql_insert_chat = $conexion_db->query("
            INSERT INTO chats_ia(id_chat, id_user, id_mensaje_chat, mensaje_user, mensaje_ia, fec_ini_cha)
            VALUES ($sql_max_chat_user, $id_user, $ultimo_chat, '$mensaje', '$result','$fecha')
        ");
    }
    
    echo json_encode(["mensaje" => $result]);
} else {
    echo json_encode(["error" => "Por favor, envía el formulario."]);
}

/*

['role' => 'assistant', 'content' => $assistant_reply]; // PARA ADICIONAR CONTENIDO
*/
?>