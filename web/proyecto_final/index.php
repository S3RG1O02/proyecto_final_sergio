<?php
include_once "./src/tools_escential.php";
include_once "./src/pages.php";

session_start();

if (isset($_SESSION["usuario"])) {
    $usuario = [
        "usuario" => $_SESSION["usuario"],
        "password" => $_SESSION["password"],
        "id_usuario" => $_SESSION["id_usuario"],
        "roles_usuario" => $_SESSION["roles_usuario"]
    ];
} else {
    $usuario = [];
}

$pagina_actual = "";
$page_permisos = "";
$body = "
<div class=''>
";

$conexion_db = conexion_db("tramway.proxy.rlwy.net:50883", "railway", "root", "uweKmqiyvpjkhesEdHTfZZVCFroguppa");
// $conexion_db = conexion_db("db5017438101.hosting-data.io", "dbs13986048", "dbu251465", "my_sql_sergio_ionos");
// SERVIDOR DE MISMO HOSTING MÁS RAPIDO PERO NO PERMITE CONEXIÓN DESDE EXTERIORES.
if($_REQUEST && $_REQUEST["page"]) {
    $pagina_actual = encontrar_pagina($_GET["page"]);
    if ($pagina_actual) {
        $page_permisos = permisos_page($pagina_actual[0]["id"]);

        if ($page_permisos && !empty($page_permisos)) {
            if (!empty($usuario) && !empty($usuario["roles_usuario"]) && in_array($page_permisos, $usuario["roles_usuario"])) {
                $body .= eval("return " . $pagina_actual[0]["page_function"]);
            } else {
                $body .= "
                <div class='flex items-center justify-center min-h-screen'>
                    <h1 class='font-circular font-normal text-center text-3xl md:text-5xl lg:text-5xl text-red-600'>NO TIENE PERMISOS PARA ACCEDER A ESTA PÁGINA.</h1>
                </div>
                ";
            }
            /// CONTINUAR CUANDO PÁGINA NECESITE PERMISO FALTA COMPLETAR
        } else {
            /// ESTO SIGNIFICA QUE LA PÁGINA NO NECESITA PERMISOS 
            $body .= eval("return " . $pagina_actual[0]["page_function"]);
        }

    } else {
        $pagina_actual[0] = [
            "id" => false,
            "page_title" => "no_encontrada",
            "page_name" => "No encontrada",
            "page_function" => false,
        ];
        $body .= "
        <div class='flex items-center justify-center min-h-screen'>
            <h1 class='font-circular font-normal text-center text-3xl md:text-5xl lg:text-5xl text-red-600'>ERROR PÁGINA NO ENCONTRADA.</h1>
        </div>
        ";
    }


} elseif(!isset($_REQUEST["page"]) && !empty($usuario)) {
    header("Location: inicio");
} else {
    header("Location: login");
}

$body .= "
</div>
";

include "./src/header.php";
if ($pagina_actual[0]["id"] != 1) {
    echo navbar();
}
echo $body;
include "./src/footer.php";