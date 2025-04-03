<?php


function conexion_db($tipo, $nombreDB, $user, $password) {
    try {
        $conexion = new PDO("mysql:host=$tipo;dbname=$nombreDB", $user, $password); 
        return $conexion;

    } catch(PDOException $e) {
        echo "ERROR: " . $e;
        return null; 
    }
}

function encontrar_pagina($page) {
    global $conexion_db;
    global $user;
    try {
            $sql = "SELECT * FROM pages WHERE page_title = '$page'";
            
            $stm = $conexion_db->prepare($sql);
            $resultado = $stm->execute();
            $resultado = $stm->fetchAll(PDO::FETCH_ASSOC);
            return $resultado;
            
    } catch (PDOException $e) {
        return false;
    }

}

function permisos_page($id_page) {
    global $conexion_db;


    $sql_permisos = $conexion_db->query("
    SELECT
        *
    FROM
        pages
        LEFT JOIN roles_pages on pages.id = roles_pages.id_page
    WHERE
        pages.id = $id_page
    ");

    while($permiso = $sql_permisos->fetch(PDO::FETCH_ASSOC)) {
        $permisos_page = $permiso["nombre_rol"];
    }

    return $permisos_page;
}


function navbar() {
    global $usuario;
    global $conexion_db;
    $content = "
    <nav class='flex flex-row items-center h-1/5 py-4 px-6 bg-[#020202]'>
        <div class='w-1/5'>
            <a href='inicio'><img src='img\icon.png' width='300'/></a>
        </div>
        <ul class='flex flex-row justify-end w-4/5 gap-10'>
            <li class='text-white hover:text-blue-400 text-lg'><a href='inicio'>Inicio</a></li>
            <li class='text-white hover:text-blue-400 text-lg'><a href='ticaje'>Ticaje</a></li>

    ";  

    if (!empty($usuario)) {
        $numero_roles = implode(",", array_fill(0, count($usuario["roles_usuario"]), "?")) ?? [];

        if (!empty($numero_roles)) {
            $select_paginas_rol = "
            SELECT
                * 
            FROM
                roles_pages
            LEFT JOIN
                pages on roles_pages.id_page = pages.id
            WHERE 
                nombre_rol in ($numero_roles)";

            $select_paginas_rol = $conexion_db->prepare($select_paginas_rol);
            $select_paginas_rol->execute($usuario["roles_usuario"]);
            
            while ($pagina = $select_paginas_rol->fetch(PDO::FETCH_ASSOC)) {
                $paginas_con_rol[] = $pagina;
            }

            if (!empty($paginas_con_rol)) {
                foreach ($paginas_con_rol as $key_bs => $datos_page) {
                    $content .= "
                        <li class='text-white hover:text-blue-400 text-lg'><a href='{$datos_page['page_title']}'>{$datos_page['page_name']}</a></li>
                    ";
                }
            }

        }
    }
    if (!empty($usuario)) {
        $content .= "
            <li class='text-white hover:text-blue-400 text-lg'><a href='logout'>Cerrar sesión</a></li>
        ";
    } else {
        $content .= "
            <li class='text-white hover:text-blue-400 text-lg'><a href='login'>Iniciar sesión</a></li>
        ";
    }

    $content .= "
        </ul>
    </nav>
    ";

    return $content;
}


function demArray($array) {
    $debug = debug_backtrace();

    $fichero = isset($debug[0]['file']) ? pathinfo($debug[0]['file']) : ['basename' => 'Desconocido'];
    $basename = $fichero['basename'];


    $functionName = isset($debug[1]['function']) ? $debug[1]['function'] : 'Desconocida';

    print_r("
        <div class=''>
    ");
    print_r("
        <pre class='bg-red-300 p-4 rounded-lg m-5'>
            <span>{$basename} ({$debug[0]['line']}) {$functionName}</span>
    ");
    print_r('<pre>');
    print_r(htmlspecialchars(print_r($array, true))); 
    print_r("</pre>");
    print_r("
        </div>
    ");
}

function demArrayUsers($array) {
    print_r("
        <div class='fade-out'>
    ");
    print_r("
        <pre class='bg-red-300 p-4 rounded-lg m-5'>
    ");
    print_r($array);
    print_r("</pre>");
    print_r("
        </div>
    ");
}


function avisos_div_body($id_objeto, $texto, $fade_out = false, $color_fondo = false, $checkbox = true, $numero_objeto = false) {
    $fade_out = $fade_out ? "fade-out" : "";
    $color_fondo = $color_fondo ?: "bg-green-300";

    $mensaje = "
        <div id='$id_objeto' class='$fade_out px-13 pt-8 bg-gray-300'>
            <div class='p-4 rounded-lg $color_fondo'>
                <button onclick=\"cambiarClase('fade-out', '', '$id_objeto')\">
    ";
    if ($checkbox) {
        $mensaje .= "
                    <input id='yellow-checkbox' type='checkbox' value='' class='w-4 h-4 text-yellow-300 bg-gray-100 border-gray-300 rounded focus:ring-red-500'>
        ";
    }

    $mensaje .= "
                </button>
                $texto
            </div>
        </div>
    ";

    return $mensaje;
}

function dibujar_form($tipo_def, $tabla_t, $id_buscar, $valores_buttons, $sql_whit_especial = null, $valor_whit_especial = null) {
    global $pasos;
    global $conexion_db;

    try {
        $sql_form = "SELECT * FROM definiciones WHERE tipo = '$tipo_def' ORDER BY order_form ASC";
        $sql_form = $conexion_db->prepare($sql_form);
        $sql_form->execute();
        $sql_form = $sql_form->fetchAll(PDO::FETCH_ASSOC);
    
        $sql_id = "SELECT * FROM $tabla_t WHERE id = $id_buscar";
        $sql_id = $conexion_db->prepare($sql_id);
        $sql_id->execute();
        $sql_id = $sql_id->fetchAll(PDO::FETCH_ASSOC);
    
        $body = "
        <form method='POST' action='' enctype='multipart/form-data'>
        ";

        $body .= botones_enviar($valores_buttons["name_button_volver"], $valores_buttons["value_button_volver"], $valores_buttons["name_button_enviar"],  $valores_buttons["value_button_enviar"]);

        $contiene_img = false;
        $img = "";
        foreach ($sql_form as $key => $value) {
            if ($value["tipo_datos"] == "img") {
                $contiene_img = true;
                $img = $sql_id[0][$value["title_campo"]];
                break;
            }
        }

        if ($contiene_img) {
            $body .= "
            <div class='flex flex-col md:flex-row bg-gray-200 rounded-md p-4 mt-5 mx-5 mb-5'>
                <div class='flex-shrink-0 mb-4 md:mb-0 md:mr-4'>
                    <img class='h-96 w-80 rounded-md' src='$img' alt='Empleado'>
                </div>
                <div class='flex-grow grid grid-cols-1 md:grid-cols-2 gap-4'>
            ";
        } else {
            $body .= "
            <div class='flex flex-col md:flex-row bg-gray-200 rounded-md p-4 mt-2 mx-5 mb-5'>
                <div class='flex-grow grid grid-cols-1 md:grid-cols-2 gap-4'>
            ";
        }
    
        foreach ($sql_form as $key => $input_datos) {
            $title_etiqueta = $input_datos["etiqueta_nombre"];
            $title_input = $input_datos["title_campo"];
            $tipo_input = $input_datos["tipo_datos"];
            $valor_por_defecto = $sql_id[0][$title_input];
        
            switch ($tipo_input) {
                case 'text':
                    $body .= "
                        <div>
                            <label for='$title_input' class='block text-sm font-medium text-gray-700'>$title_etiqueta</label>
                            <input maxlength='30' class='p-4 mt-1 block w-full bg-white border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500' id='$title_input' type='text' name='$title_input' value='$valor_por_defecto'>
                        </div>
                    ";
                    break;
                case 'select':
                    $valores_select = explode(",", $input_datos["valores"]);
                    $valor_por_defecto = $sql_id[0][$title_input];
                
                    $body .= "
                        <div>
                            <label for='$title_input' class='block text-sm font-medium text-gray-700'>$title_etiqueta</label>
                            <select id='$title_input' name='$title_input' class='p-4 mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'>
                    ";
                
                    foreach ($valores_select as $value) {
                        $selected = ($valor_por_defecto == $value) ? 'selected' : '';
                        $body .= "
                                <option value='$value' $selected>$value</option>
                        ";
                    }
                
                    $body .= "
                            </select>
                        </div>
                    ";
                    break;
                case 'number':
                    if ($title_input != "id") {
                        $body .= "
                        <div>
                            <label for='$title_input' class='block text-sm font-medium text-gray-700'>$title_etiqueta</label>
                            <input class='p-4 mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500' id='$title_input' type='number' name='$title_input' value='$valor_por_defecto'>
                        </div>
                        ";
                        break;
                    } else {
                        $body .= "
                        <div>
                            <label for='$title_input' class='block text-sm font-medium text-gray-700'>$title_etiqueta</label>
                            <input disabled class='p-4 mt-1 block w-full bg-gray-300 border-transparent rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500' id='$title_input' type='number' value='$valor_por_defecto'>
                        </div>
                        ";
                    }
                    break;
                case 'select_whit_sql':
                    $body .= "
                        <div>
                            <label for='$title_input' class='block text-sm font-medium text-gray-700'>$title_etiqueta</label>
                            <select id='$title_input' name='$title_input' class='p-5 mt-1 block w-full bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm'>
                    ";
                    

                    foreach ($sql_whit_especial as $datos) {
                        $selected = ($valor_whit_especial == $datos["id"]) ? 'selected' : '';
                        $body .= "
                                <option value='{$datos['id']}' $selected>{$datos['nombre']}</option>
                        ";
                    }
                
                    $body .= "
                            </select>
                        </div>
                    ";
                    break;
                default:
                    break;
            }
        }

        $body .= "
            </div>
        </div>
        ";
        
        $body .= "
        </form>
        ";
        
        return $body;
    } catch (Exception $e) {
        demArray($e);
    }

}

function formatearTexto($texto) {
    $texto = trim($texto);

    $texto = preg_replace('/\s+/', ' ', $texto);

    $texto = htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');

    $texto = nl2br($texto);

    $texto = preg_replace('/\*(.*?)\*/', '<strong>$1</strong>', $texto);

    return $texto;
}


function botones_enviar($name_button_volver, $value_button_volver, $name_button = null, $value_button = null) {
    if (!$value_button && !$name_button) {
        $body = "
            <div class='flex flex-col md:flex-row bg-gray-200 rounded-md p-2 mt-5 mx-5 mb-5 gap-4'>
                <input type='submit' class='w-1/2 rounded-md cursor-pointer p-4 bg-green-500 hover:bg-green-600 transition-all' value='Enviar'/>
                <button class='w-1/2 rounded-md cursor-pointer p-4 bg-red-500 hover:bg-red-600 transition-all'>Volver</button>
            </div>
        ";
    } else {
        $body = "
            <div class='flex flex-col md:flex-row bg-gray-200 rounded-md p-4 mt-5 mx-5 mb-5 gap-4'>
                <button name='$name_button' value='$value_button' class='w-1/2 rounded-md cursor-pointer p-4 bg-green-500 hover:bg-green-600 transition-all'>Enviar</button>
                <button class='w-1/2 rounded-md cursor-pointer p-4 bg-red-500 hover:bg-red-600 transition-all'>Volver</button>
            </div>
        ";
    }



    return $body;
}


?>