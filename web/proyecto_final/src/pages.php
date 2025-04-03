<?php
include_once "tools_escential.php";

function login() {
    global $conexion_db;
    $content = "";

    /*
    $sql_accion = "INSERT 
    INTO 
        ticajes (id_empleado, tipo_ticaje, fecha_ticaje)
    VALUES 
        (:id_empleado, :tipo_ticaje, :fecha_ticaje);
    ";
    $sql_accion = $conexion_db->prepare($sql_accion);
    $sql_accion->bindParam(":id_empleado", $empleado, PDO::PARAM_INT);
    $sql_accion->bindParam(":fecha_ticaje", $fecha, PDO::PARAM_STR);
    */
    if ($_POST) {
        if (isset($_POST["usuario"]) && isset($_POST["password"])) {

        }
        $password_hash = hash("sha256", $_POST["password"]);
        $sql_user ="
        SELECT
            *
        FROM
            usuarios
        WHERE 
            usuario = :usuario AND password = :password
        ";

        $sql_user = $conexion_db->prepare($sql_user);
        $sql_user->bindParam(":usuario", $_POST["usuario"], PDO::PARAM_STR);
        $sql_user->bindParam(":password", $password_hash, PDO::PARAM_STR);
        $sql_user->execute();
        $usuario_comprobacion = "";

        while ($usuario = $sql_user->fetch(PDO::FETCH_ASSOC)) {
            $usuario_comprobacion = $usuario;
        }

        if ($usuario_comprobacion) {
            $_SESSION["password"] = $usuario_comprobacion["password"];
            $_SESSION["usuario"] = $usuario_comprobacion["usuario"];
            $_SESSION["id_usuario"] = $usuario_comprobacion["id"];
            $sql_permisos = "
            SELECT
                roles_usuarios.nombre_rol
            FROM 
                empleados
                LEFT JOIN roles_usuarios on empleados.id = roles_usuarios.id_usuario
            WHERE 
                empleados.id = :id_empleado
            ";

            $sql_permisos = $conexion_db->prepare($sql_permisos);
            $sql_permisos->bindParam(":id_empleado", $usuario_comprobacion["id_empleado"], PDO::PARAM_INT);
            $sql_permisos->execute();
            $roles = [];
            while($rol = $sql_permisos->fetch(PDO::FETCH_ASSOC)) {
                $roles[] = $rol["nombre_rol"];
            }
            $_SESSION["roles_usuario"] = $roles;
            header("Location: inicio");
        } else {  
            $content .= "
            <form method='POST'>
                <div class='flex flex-col items-center justify-center min-h-screen bg-gradient-to-bl from-30% from-[#050A24] to-[#152975] w-full lg:flex-row lg:h-screen'>
                  <div class='flex flex-col h-1/2 w-4/5 gap-2 px-0 pb-6 lg:h-full lg:w-full lg:items-start lg:justify-end lg:px-20 lg:pb-20'>
                      <h1 class='font-extrabold text-3xl text-[#e1e1e4] text-center lg:text-left lg:text-7xl'>LuminaTech <span class='block lg:inline'>Intranet</span></h1>
                      <p class='text-[#f7f7f8] font-light hidden lg:flex lg:text-left lg:font-extralight lg:text-2xl'>Este sistema está destinado exclusivamente para el uso del personal autorizado. Por favor, ingrese con sus credenciales correspondientes.</p>
                  </div>
                    <div class='h-1/2 w-4/5 bg-[#f7f7f8f3] rounded-lg flex flex-col py-5 lg:h-full lg:w-3/5 lg:rounded-none lg:justify-center lg:items-center lg:px-10'>
                        <img src='img/icon.png'/>
                        <div class='w-full px-4 flex flex-col gap-4 mt-10'>
                            <label for='usuario' class='text-xl lg:font-thin'>Usuario</label>
                            <input id='usuario' name='usuario' type='text' placeholder='Ingrese su usuario' class='py-3 px-2 border-2 border-solid border-red-600 rounded-lg'/>
                            <label for='password' class='text-xl lg:font-thin'>Contraseña</label>
                            <input id='password' name='password' type='password'  placeholder='***********'/ class='py-3 px-2 border-2 border-solid border-red-600 rounded-lg'>
                            <p class='text-red-600'>Datos incorrectos, intente de nuevo.</p>
                            <input type='submit' placeholder='enviar' class='bg-[#1570ef] mt-5 text-white text-xl p-2 rounded-lg hover:bg-[#154fef] transition-all cursor-pointer'/>
                        </div>
                    </div>
                </div>
            </form>
            ";    
        }
        
    } else {
        $content .= "
        <form method='POST'>
            <div class='flex flex-col items-center justify-center min-h-screen bg-gradient-to-bl from-30% from-[#050A24] to-[#152975] w-full lg:flex-row lg:h-screen'>
                <div class='flex flex-col h-1/2 w-4/5 gap-2 px-0 pb-6 lg:h-full lg:w-full lg:items-start lg:justify-end lg:px-20 lg:pb-20'>
                    <h1 class='font-extrabold text-3xl text-[#e1e1e4] text-center lg:text-left lg:text-7xl'>LuminaTech <span class='block lg:inline'>Intranet</span></h1>
                    <p class='text-[#f7f7f8] font-light  hidden lg:flex lg:text-left lg:font-extralight lg:text-2xl'>Este sistema está destinado exclusivamente para el uso del personal autorizado. Por favor, ingrese con sus credenciales correspondientes.</p>
                </div>
                <div class='h-1/2 w-4/5 bg-[#f7f7f8f3] rounded-lg flex flex-col py-5 lg:h-full lg:w-3/5 lg:rounded-none lg:justify-center lg:items-center lg:px-10'>
                    <img src='img/icon.png'/>
                    <div class='w-full px-4 flex flex-col gap-4 mt-10'>
                        <label for='usuario' class='text-xl lg:font-thin'>Usuario</label>
                        <input id='usuario' name='usuario' type='text' placeholder='Ingrese su usuario' class='py-3 px-2 border border-black rounded-lg'/>
                        <label for='password' class='text-xl lg:font-thin'>Contraseña</label>
                        <input id='password' name='password' type='password'  placeholder='***********'/ class='py-3 px-2 border border-black rounded-lg'>
                        <input type='submit' placeholder='enviar' class='bg-[#1570ef] mt-5 text-white text-xl p-2 rounded-lg hover:bg-[#154fef] transition-all cursor-pointer'/>
                    </div>
                </div>
            </div>
        </form>
        ";    
    }

    return $content;
}



function home() {
    global $conexion_db;
    
    $sql_consejos = $conexion_db->query("SELECT * FROM consejos_home");

    while ($consejo = $sql_consejos->fetch(PDO::FETCH_ASSOC)) {
        $consejos[] = $consejo;
    }
    $consejo_alt = $consejos[ rand(0, count($consejos) - 1)];


    $content = "
    <div class='flex items-center justify-center min-h-screen h py-10 px-5 lg:p-10 lg:px-16 lg:h-[calc(100vh-5rem)] bg-gradient-to-bl from-15% from-[#c9c8c8] to-[#c9c7c7]'>
        <div class=' bg-white rounded-2xl shadow-2xl p-5 lg:p-20 lg:-mt-20 lg:w-4/5'>
            <h1 class='text-center font-bold text-4xl mb-5'>{$consejo_alt['titulo_consejo']}</h1>
            <p class='font-light text-xl'>{$consejo_alt['contenido']}</p>
        </div>
    </div>
    ";


    return $content;
}

function ticaje() {
    global $conexion_db;
    $id_empleado = $_POST["id_empleado"] ?? 0;
    $id_sede = $_POST["id_sede"] ?? 0;

    if ($id_sede == 0) {
        $paso = "sede";
    } elseif ($id_empleado == 0) {
        $paso = "empleado";
    } else {
        $paso = "ticaje";
    }

    $content ="
    <form method='POST'>
    ";

    switch ($paso) {
        case 'sede': 
            $content .= "
            <div class='flex justify-center items-center h-screen bg-[#2D2D2D] lg:h-[calc(100vh-5rem)]'>
                <div class='w-5/6 lg:w-2/6 h-auto bg-[#1E1E1E] rounded-2xl p-10 shadow-2xl shadow-[#1E1E1E] flex flex-col justify-evenly items-center'>
                    <img src='img/icon.png' class='mb-5'/>
                    <div class='flex flex-col w-4/5 my-5 text-white'>
                        <select id='sedes' name='id_sede' class='p-5 bg-[#2D2D2D] rounded-lg'>
            ";
    
            $sql_sedes = $conexion_db->query("SELECT * FROM sedes");
    
            while ($sede = $sql_sedes->fetch(PDO::FETCH_ASSOC)) {
                $content .="
                            <option value='{$sede['id']}'>{$sede['nombre_sede']}</option>
                ";
            }
    
    
            $content .= "
                        </select>
                        <input value='Ver empleados' class='text-md mt-7 p-5 bg-[#0C8DFF] hover:bg-[#0c45ff] transition-all cursor-pointer rounded-lg' type='submit'/>
                    </div>
                </div>
            </div>
            ";
            break;
        case "empleado":
            $id_sede = $_POST["id_sede"] ?? 0;
            $sql_empleados = "
            SELECT
                empleados.id, empleados.imagen_empleado
            FROM
                empleados
                LEFT JOIN sedes on empleados.id_sede = sedes.id
            WHERE
                sedes.id = :id_sede
            ";

            $sql_empleados = $conexion_db->prepare($sql_empleados);
            $sql_empleados->bindParam(":id_sede", $id_sede, PDO::PARAM_INT);
            $sql_empleados->execute();
            $empleados_sede = [];

            while ($empleado = $sql_empleados->fetch(PDO::FETCH_ASSOC)) {
                $empleados_sede[] = $empleado;
            }
            if (empty($empleados_sede)) {
                $content .= "
                <div class='bg-[#363636] lg:h-[calc(100vh-5rem)] flex justify-center items-center'>
                    <div class='w-2/6 h-auto bg-[#1E1E1E] rounded-2xl p-10 shadow-2xl shadow-[#1E1E1E] flex flex-col justify-center items-center m-10'>
                        <h2 class='text-2xl text-red-600 text-center mb-5'>No hay empleados activos en esta sede.</h2>
                        <a href='ticaje' class='p-5 bg-[#0C8DFF] w-full text-white text-center rounded-lg hover:bg-[#0c45ff] transition-all'>Volver</a>
                    </div>
                </div>
                ";
            } else {
                $content .= "
                <div class='bg-[#2D2D2D] lg:h-[calc(100vh-5rem)] flex flex-col justify-center items-center p-10'>
                    <div class='w-4/5 bg-[#1E1E1E] h-auto p-10 flex flex-wrap justify-center items-center gap-10 rounded-lg shadow-2xl'>
                ";
                foreach ($empleados_sede as $key_registro => $datos_empleado) {
                    $img = $datos_empleado["imagen_empleado"];
                    $content .= "
                        <button name='id_empleado' value='{$datos_empleado['id']}' class='h-auto w-auto cursor-pointer hover:opacity-80 transition-all bg-[#363636]'><img src='$img' class='w-50 h-40'/></button>
                    ";
                }

                $content .="  
                        <input type='hidden' name='paso' value='empleado'/>
                    </div>
                    <a href='ticaje' class='cursor-pointer h-auto w-4/5 md:w-1/7 lg:-w-1/7 mt-10 bg-red-600 rounded-lg py-3 text-[#e9e9e9] hover:bg-red-800 transition-all text-center'>Volver</a>
                    <input type='hidden' name='id_sede' value='$id_sede'/>
                </div>
                ";
            }
            break;
        case "ticaje": 

            $content .="
            <input type='hidden' name='id_sede' value='$id_sede'/>
            <input type='hidden' name='id_empleado' value='{$id_empleado}'/>
            ";

            $sql_empleado = "
            SELECT
                empleados.id, empleados.documento_empleado, empleados.nombre, empleados.imagen_empleado, ticajes.id as id_ticaje, ticajes.tipo_ticaje, ticajes.fecha_ticaje
            FROM
                empleados
                LEFT JOIN ticajes ON empleados.id = ticajes.id_empleado
            WHERE 
                empleados.id = :id_empleado;
            ";


            $sql_empleado = $conexion_db->prepare($sql_empleado);
            $sql_empleado->bindParam(":id_empleado", $id_empleado, PDO::PARAM_INT);
            $sql_empleado->execute();

            $ticaje_empleado = [];

            while ($ticaje_empleado = $sql_empleado->fetch(PDO::FETCH_ASSOC)) {
                $ticajes_empleado[] = $ticaje_empleado; 
            }

            $ultimo_ticaje = end($ticajes_empleado);


            if (isset($_POST["accion"])) {
                            
                $fecha = date("Y-m-d H:i:s");
                $fecha_format = date("H:i:s");
                
                $sql_accion = "INSERT INTO ticajes(id_empleado ,tipo_ticaje, fecha_ticaje) VALUES(:id_empleado, :tipo_ticaje, :fecha_ticaje)";
                $sql_accion = $conexion_db->prepare($sql_accion);
                $sql_accion->bindParam(":id_empleado", $id_empleado, PDO::PARAM_STR);
                $sql_accion->bindParam(":tipo_ticaje", $_POST["accion"], PDO::PARAM_STR);
                $sql_accion->bindParam(":fecha_ticaje", $fecha, PDO::PARAM_STR);
                switch ($_POST["accion"]) {
                    case 'ENTRADA':
                        $sql_accion->execute();
                        $content .= "
                        <div class='bg-[#363636] min-h-screen lg:h-[calc(100vh-5rem)] flex justify-center items-center'>
                            <div class='bg-[#b4b4b4] h-auto w-4/5 -mt-20 flex flex-col justify-center items-center rounded-lg shadow-md shadow-[#000000] p-10 m-10 lg:-mt-10 lg:w-1/5'>
                                <div class='bg-[#1E1E1E] w-full h-62 mb-5 rounded-lg'>
                                    <img src='{$ultimo_ticaje['imagen_empleado']}' class='bg-[#1E1E1E] h-full w-full rounded-lg'/>
                                </div>
                                <p class='font-medium text-lg mb-2'>Último ticaje</p>
                                <p class='font-thin mb-2 text-center'>Te has ticado a las: $fecha_format</p>
                                <div class='w-full flex flex-row mt-2 gap-3'>
                                    <button class='text-white p-3 bg-[#363636] w-full rounded-lg hover:bg-[#696969] transition-all cursor-pointer' name='id_empleado' value='0'>Volver</button>
                                </div>
                            </div>
                        </div>
                        ";
                        break;
                    case 'SALIDA':
                        $sql_accion->execute();
                        $content .= "
                        <div class='bg-[#363636] min-h-screen lg:h-[calc(100vh-5rem)] flex justify-center items-center'>
                            <div class='bg-[#b4b4b4] h-auto w-4/5 -mt-20 flex flex-col justify-center items-center rounded-lg shadow-md shadow-[#000000] p-10 m-10 lg:-mt-10 lg:w-1/5'>
                                <div class='bg-[#1E1E1E] w-full h-62 mb-5 rounded-lg'>
                                    <img src='{$ultimo_ticaje['imagen_empleado']}' class='bg-[#1E1E1E] h-full w-full rounded-lg'/>
                                </div>
                                <p class='font-medium text-lg mb-2'>Último ticaje</p>
                                <p class='font-thin mb-2 text-center'>{$ultimo_ticaje['tipo_ticaje']}: {$ultimo_ticaje['fecha_ticaje']}</p>
                                <div class='w-full flex flex-row mt-2 gap-3'>
                                    <button class='text-white p-3 bg-[#363636] w-full rounded-lg hover:bg-[#696969] transition-all cursor-pointer' name='id_empleado' value='0'>Volver</button>
                                </div>
                            </div>
                        </div>
                        ";
                        break;
                    default:
                        # code...
                        break;
                }
            } else {
                if (!empty($ultimo_ticaje["fecha_ticaje"])) {
                    $content .= "
                    <div class='bg-[#363636] min-h-screen lg:h-[calc(100vh-5rem)] flex justify-center items-center'>
                        <div class='bg-[#b4b4b4] h-auto w-4/5 -mt-20 flex flex-col justify-center items-center rounded-lg shadow-md shadow-[#000000] p-10 m-10 lg:-mt-10 lg:w-1/5'>
                            <div class='bg-[#1E1E1E] w-full h-62 mb-5 rounded-lg'>
                                <img src='{$ultimo_ticaje['imagen_empleado']}' class='bg-[#1E1E1E] h-full w-full rounded-lg'/>
                            </div>
                            <p class='font-medium text-lg mb-2'>Último ticaje</p>
                            <p class='font-thin mb-2 text-center'>{$ultimo_ticaje['tipo_ticaje']}: {$ultimo_ticaje['fecha_ticaje']}</p>
                            <div class='w-full flex flex-wrap items-center justify-center mt-2 gap-3'>
                                <button class='text-white p-4 bg-[#363636] rounded-lg hover:bg-[#696969] transition-all cursor-pointer' name='accion' value='ENTRADA'>Entrada</button>
                                <button class='text-white p-4 bg-[#363636] rounded-lg hover:bg-[#696969] transition-all cursor-pointer' name='accion' value='SALIDA'>Salida</button>
                                <button class='text-white px-5 py-4 bg-[#363636] rounded-lg hover:bg-[#696969] transition-all cursor-pointer' name='id_empleado' value='0'>Volver</button>
                            </div>
                        </div>
                    </div>
                    ";
                } else {
                    $content .= "
                    <div class='bg-[#363636] min-h-screen lg:h-[calc(100vh-5rem)] flex justify-center items-center'>
                        <div class='bg-[#b4b4b4] h-auto w-4/5 flex flex-col justify-center items-center rounded-lg shadow-md shadow-[#000000] p-10 m-10 lg:-mt-10 lg:w-1/5'>
                            <div class='bg-[#1E1E1E] w-full h-62 mb-5 rounded-lg'>
                                <img src='{$ultimo_ticaje['imagen_empleado']}' class='bg-[#1E1E1E] h-full w-full rounded-lg'/>
                            </div>
                            <p class='font-medium text-lg mb-2'>Último ticaje</p>
                            <p class='font-thin mb-2 text-center'>NO HAY TICAJES</p>
                            <div class='w-full flex flex-wrap items-center justify-center mt-2 gap-3'>
                                <button class='text-white p-4 bg-[#363636] rounded-lg hover:bg-[#696969] transition-all cursor-pointer' name='accion' value='ENTRADA'>Entrada</button>
                                <button class='text-white p-4 bg-[#363636] rounded-lg hover:bg-[#696969] transition-all cursor-pointer' name='accion' value='SALIDA'>Salida</button>
                                <button class='text-white px-5 py-4 bg-[#363636] rounded-lg hover:bg-[#696969] transition-all cursor-pointer' name='id_empleado' value='0'>Volver</button>
                            </div>
                        </div>
                    </div>
                    ";
                }

            }

            break;
        default:
            # code...
            break;
    }

    $content .= "
    </form>
    ";

    return $content;
}

function empleados() {
    global $conexion_db;
    $content = "
    <form method='POST' class=''>
    ";

    if ($_POST) {
        $paso = unserialize($_POST["paso"] ?? null);
        
        if (isset($paso["accion"]) && isset($paso["id"])) {
            
            switch ($paso["accion"]) {
                case 'editar':
                    if (isset($paso["accion_form"]) && isset($paso["id"]) && isset($paso["accion_form"]) == "grabar") {
                        try {    
                            $datos_update = $_POST;
                            unset($datos_update["paso"]);
                            $cols_db_consulta = $conexion_db->query("SELECT * FROM empleados");
                            $cols_db = [];
                            for ($i=0; $i < $cols_db_consulta->ColumnCount(); $i++) {
                                $col_actual = $cols_db_consulta->getColumnMeta($i);
                                if ($col_actual["name"] != "imagen_empleado" && $col_actual["name"] != "id") {
                                    $cols_db[] = $col_actual["name"];
                                }
                            }
                            foreach ($datos_update as $key => $dato_update) {
                                if (!in_array($key, $cols_db)) {
                                    throw new Exception($content .= avisos_div_body("error_1", "Error al actualizar, falta de campos necesarios, intente más tarde", true, "bg-red-400"));
                                }
                            }

                            $select_con_id = $conexion_db->prepare("
                            SELECT
                                *
                            FROM
                                empleados
                            where
                                id = :id
                            ");
                            $select_con_id->execute([
                                ":id" => $paso["id"],
                            ]);
                            $datos_registro = $select_con_id->fetchAll(PDO::FETCH_ASSOC);  

                            $sql_sede = $conexion_db->prepare("
                            select 
                                id, nombre_sede
                            from
                                sedes
                            where 
                                nombre_sede = :nombre_sede
                            ");
                            $sql_sede->execute([
                                ":nombre_sede" => $datos_update["id_sede"],
                            ]);
                            $sede = $sql_sede->fetch(PDO::FETCH_ASSOC);
                            if (!empty($datos_registro) && !empty($sede)) {
                                                            
                                $datos_update["id_sede"] = $sede["id"];

                                $update_registro = $conexion_db->prepare("
                                UPDATE 
                                    empleados
                                SET 
                                    documento_empleado = :documento_empleado, 
                                    nombre = :nombre, 
                                    id_sede = :id_sede, 
                                    numero_telefono = :numero_telefono, 
                                    direccion = :direccion
                                WHERE 
                                    id = :id
                                ");

                                $update_registro->execute([
                                    ":documento_empleado" => $datos_update["documento_empleado"],
                                    ":nombre" => $datos_update["nombre"],
                                    ":id_sede" => $datos_update["id_sede"],
                                    ":numero_telefono" => $datos_update["numero_telefono"],
                                    ":direccion" => $datos_update["direccion"],
                                    ":id" => $paso["id"],
                                ]);
                                $content .= avisos_div_body("mensaje_1", "Actualización correcta.", true, "bg-green-400");

                            } else {
                                $content .= avisos_div_body("error_1", "Error al actualizar, sede no existente o id se encuentra, intente más tarde..", true, "bg-red-400");
                            }                            
                        } catch (PDOException $th) {
                            demArray($th);
                            $content .= avisos_div_body("error_1", "Error al actualizar, intente más tarde..", true, "bg-red-400");
                        }
                    } else {
                        $paso_grabar = serialize([
                            "accion" => "editar", 
                            "accion_form" => "grabar",
                            "id" => $paso["id"],
                        ]);
                        $valores_buttons = [
                            "name_button_volver" => "paso",
                            "value_button_volver" => "",
                            "name_button_enviar" => "paso",
                            "value_button_enviar" => $paso_grabar,
                        ];
                        $content .= dibujar_form("empleados", "empleados", $paso["id"], $valores_buttons);
                        return $content;
                    }

                    break;
                case 'eliminar':
                    try {
                        $sql_delete = $conexion_db->prepare("
                        DELETE FROM
                            empleados
                        WHERE 
                            id = :id_empleado
                        ");
                        $sql_delete->bindParam(":id_empleado", $paso["id"], PDO::PARAM_INT);
                        $sql_delete->execute();
                        $content .= avisos_div_body("mensaje_delete", "Empleado eliminado correctamente", true, "bg-green-400");
                    } catch (PDOException $th) {
                        $content .= avisos_div_body("mensaje_delete", "Error al eliminar", true, "bg-red-500");
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }


    }

    $select_empleados = $conexion_db->query("SELECT * FROM empleados");

    $primera = true;
    while ($empleado = $select_empleados->fetch(PDO::FETCH_ASSOC)) {
        if ($primera) {
            foreach ($empleado as $key => $value) {
                $campos_db[] = $key;
            }
            $primera = false;
        } 
        $empleados[] = $empleado;
    }


    $content .= "
    <div class='flex justify-center py-10 bg-gray-300'>
        <table class='w-11/12 relative overflow-x-auto shadow-2xl sm:rounded-lg text-md'>
            <thead class='bg-gray-400'>
                <tr>
                    <th class='p-4'>Id</th>
                    <th class='p-4'>Documento empleado</th>
                    <th class='p-4'>Nombre</th>
                    <th class='p-4'>Sede</th>
                    <th class='p-4'>Número de teléfono</th>
                    <th class='p-4'>Dirección</th>
                    <th class='p-4'>Acción</th>
                </tr>
            </thead>
            <tbody class='text-center'>
    ";

    foreach ($empleados as $key => $datos_empleado) {
        $paso_editar = serialize([
            "accion" => "editar", 
            "id" => $datos_empleado["id"],
        ]);
        $paso_eliminar = serialize([
            "accion" => "eliminar", 
            "id" => $datos_empleado["id"],
        ]);
        $content .= "
                <tr class='odd:bg-white even:bg-gray-300 p-5'>
                    <td class='p-3 font-bold'>{$datos_empleado['id']}</td>
                    <td class='p-2'>{$datos_empleado['documento_empleado']}</td>
                    <td class='p-2'>{$datos_empleado['nombre']}</td>
                    <td class='p-2'>{$datos_empleado['id_sede']}</td>
                    <td class='p-2'>{$datos_empleado['numero_telefono']}</td>
                    <td class='p-2'>{$datos_empleado['direccion']}</td>
                    <td class='p-2'><button name='paso' value='$paso_editar' class='mr-4 p-2 bg-green-300 rounded-lg hover:bg-green-400 transition-all cursor-pointer'>Editar</button><button class='p-2 bg-red-500 rounded-lg text-gray-100 hover:bg-red-600 transition-all cursor-pointer' onclick=\"show({$datos_empleado['id']}, '{$datos_empleado['nombre']}');\" id='button_eliminar_{$datos_empleado['id']}' name='paso' value='$paso_eliminar'>Eliminar</button></td>
                </tr>
        ";
    }


    $content .= "
            </tbody>
        </table>
    </div>
    <script src='src/js/eliminar_empleado.js'></script>
    ";

    $content .= "
    </form>
    ";


    return $content;
}

function sedes() {
    global $conexion_db;
    $content = "
    <form method='POST' class=''>
    ";


    if ($_POST) {
        $paso = unserialize($_POST["paso"] ?? null);
        
        if (isset($paso["accion"]) && isset($paso["id"])) {
            switch ($paso["accion"]) {
                case 'editar':
                    if (isset($paso["accion_form"]) && isset($paso["id"]) && isset($paso["accion_form"]) == "grabar") {
                        try {    
                            $datos_update = $_POST;
                            unset($datos_update["paso"]);
                            $cols_db_consulta = $conexion_db->query("SELECT * FROM sedes");
                            $cols_db = [];
                            for ($i=0; $i < $cols_db_consulta->ColumnCount(); $i++) {
                                $col_actual = $cols_db_consulta->getColumnMeta($i);
                                if ($col_actual["name"] != "id") {
                                    $cols_db[] = $col_actual["name"];
                                }
                            }
                            $cols_correctas = true;
                            foreach ($datos_update as $key => $dato_update) {
                                if (!in_array($key, $cols_db)) {
                                    $cols_correctas = false;
                                } else {
                                    $cols_correctas = true;
                                }
                            }

                            if (!$cols_correctas) {
                                $content .= avisos_div_body("error_1", "Error al actualizar, falta de campos necesarios, intente más tarde", true, "bg-red-400");
                            } else {
                                $select_con_id = $conexion_db->prepare("
                                SELECT
                                    *
                                FROM
                                    sedes
                                where
                                    id = :id
                                ");
                                $select_con_id->execute([
                                    ":id" => $paso["id"],
                                ]);
                                $datos_registro = $select_con_id->fetchAll(PDO::FETCH_ASSOC);  

                                $sql_empleado_supervisor = $conexion_db->prepare("
                                select 
                                    *
                                from
                                    empleados
                                where 
                                    id = :id
                                ");
                                $sql_empleado_supervisor->execute([
                                    ":id" => $datos_update["id_empleado_supervisor"],
                                ]);
                                $empleado_supervisor = $sql_empleado_supervisor->fetch(PDO::FETCH_ASSOC);
                                
                                if (!empty($datos_registro) && !empty($empleado_supervisor)) {
                                                                
                                    $datos_update["id_empleado_supervisor"] = $empleado_supervisor["id"];

                                    $update_registro = $conexion_db->prepare("
                                    UPDATE 
                                        sedes
                                    SET 
                                        nombre_sede = :nombre,  
                                        direccion_fisica = :direccion,
                                        id_empleado_supervisor = :id_empleado_supervisor
                                    WHERE 
                                        id = :id
                                    ");

                                    $update_registro->execute([
                                        ":nombre" => $datos_update["nombre_sede"],
                                        ":direccion" => $datos_update["direccion_fisica"],
                                        ":id_empleado_supervisor" => $datos_update["id_empleado_supervisor"],
                                        ":id" => $paso["id"],
                                    ]);
                                    $content .= avisos_div_body("mensaje_1", "Actualización correcta.", true, "bg-green-400");

                                } else {
                                    $content .= avisos_div_body("error_1", "Error al actualizar, id se encuentra, intente más tarde..", true, "bg-red-400");
                                }             
                            }
  
                        } catch (PDOException $th) {
                            demArray($th);
                            $content .= avisos_div_body("error_1", "Error al actualizar, intente más tarde..", true, "bg-red-400");
                        }
                    } else {
                        $paso_grabar = serialize([
                            "accion" => "editar", 
                            "accion_form" => "grabar",
                            "id" => $paso["id"],
                        ]);
                        $valores_buttons = [
                            "name_button_volver" => "paso",
                            "value_button_volver" => "",
                            "name_button_enviar" => "paso",
                            "value_button_enviar" => $paso_grabar,
                        ];
                        $empleados_especificos = $conexion_db->query("
                        select
                            *
                        from
                            empleados
                        ");
                        $empleados_especificos = $empleados_especificos->fetchAll(PDO::FETCH_ASSOC);
                        $content .= dibujar_form("sedes", "sedes", $paso["id"], $valores_buttons, $empleados_especificos, $paso["id_empleado_supvervisor"]);
                        return $content;
                    }

                    break;
                case 'eliminar':
                    try {
                        $sql_delete = $conexion_db->prepare("
                        DELETE FROM
                            sedes
                        WHERE 
                            id = :id_sede
                        ");
                        $sql_delete->bindParam(":id_sede", $paso["id"], PDO::PARAM_INT);
                        $sql_delete->execute();
                        $content .= avisos_div_body("mensaje_delete", "Empleado eliminado correctamente", true, "bg-green-400");
                    } catch (PDOException $th) {
                        $content .= avisos_div_body("mensaje_delete", "Error al eliminar", true, "bg-red-500");
                    }
                    break;
                default:
                    # code...
                    break;
            }
        }


    }



    $select_sedes = $conexion_db->query("SELECT * FROM sedes");

    $primera = true;
    while ($sede = $select_sedes->fetch(PDO::FETCH_ASSOC)) {
        if ($primera) {
            foreach ($sede as $key => $value) {
                
                $campos_db[] = $key;
            }
            $primera = false;
        } 
        $sedes[] = $sede;
    }

    $content .= "
    <div class='flex justify-center py-10 bg-gray-300'>
        <table class='w-11/12 relative overflow-x-auto shadow-2xl sm:rounded-lg text-md'>
            <thead class='bg-gray-400'>
                <tr>
                    <th class='p-4'>Id</th>
                    <th class='p-4'>Sede</th>
                    <th class='p-4'>Dirección</th>
                    <th class='p-4'>Empleado supervisor</th>
                    <th class='p-4'>Acción</th>
                </tr>
            </thead>
            <tbody class='text-center'>
    ";

    foreach ($sedes as $key => $datos_sede) {
        $paso_editar = serialize([
            "accion" => "editar", 
            "id" => $datos_sede["id"],
            "id_empleado_supvervisor" => $datos_sede['id_empleado_supervisor'],
        ]);
        $paso_eliminar = serialize([
            "accion" => "eliminar", 
            "id" => $datos_sede["id"],
        ]);

        $empleado_sup = $conexion_db->query("
            select
                nombre
            from 
                empleados
            where 
                id = {$datos_sede['id_empleado_supervisor']}
        ");
        $empleado_sup = $empleado_sup->fetch(PDO::FETCH_ASSOC);
        if (empty($empleado_sup)) {
            $empleado_sup = [
                "nombre" => "Empleado no existente o dado de baja.",
            ];
        }

        $content .= "
                <tr class='odd:bg-white even:bg-gray-300 p-5'>
                    <td class='p-3 font-bold'>{$datos_sede['id']}</td>
                    <td class='p-2'>{$datos_sede['nombre_sede']}</td>
                    <td class='p-2'>{$datos_sede['direccion_fisica']}</td>
                    <td class='p-2'>{$empleado_sup['nombre']}</td>
                    <td class='p-2'><button name='paso' value='$paso_editar' class='mr-4 p-2 bg-green-300 rounded-lg hover:bg-green-400 transition-all cursor-pointer'>Editar</button><button class='p-2 bg-red-500 rounded-lg text-gray-100 hover:bg-red-600 transition-all cursor-pointer' onclick=\"show({$datos_sede['id']}, '{$datos_sede['nombre_sede']}');\" id='button_eliminar_{$datos_sede['id']}' name='paso' value='$paso_eliminar'>Eliminar</button></td>
                </tr>
        ";
    }


    $content .= "
            </tbody>
        </table>
    </div>
    <script src='src/js/eliminar_empleado.js'></script>
    ";

    $content .= "
    </form>
    ";


    return $content;
}

function chatBot() {
    $body = "";
    global $usuario;
    $id_user = $usuario["id_usuario"];
    global $conexion_db;
    $select_chats = $conexion_db->query("
    SELECT 
        *
    FROM
        chats_ia
    WHERE 
        id_user = $id_user
    ORDER BY
        id_chat DESC
    ");

    $chats_user = [];

    while ($chat = $select_chats->fetch(PDO::FETCH_ASSOC)) {
        $chats_user[$chat["id_chat"]][] = $chat;
    }
    $body .= "
<div class='flex flex-col md:grid md:grid-cols-5 md:grid-rows-1 bg-[rgb(33,33,33)] w-full'>
    <button id='button_menu_mobile' class='absolute p-3 md:hidden' onclick='activar_menu()'>
        <svg xmlns='http://www.w3.org/2000/svg' height='40px' viewBox='0 -960 960 960' width='40px' fill='#FFFFFF'>
        <path d='M120-240v-80h720v80H120Zm0-200v-80h720v80H120Zm0-200v-80h720v80H120Z'/>
        </svg>
    </button>
    <div id='menu_chats' class='hidden md:flex overflow-y-auto md:row-span-1 custom-scrollbar md:h-[calc(100vh-5rem)] bg-[rgb(48,48,48)] transition-all p-4'>
        <form method='POST' action='' class='w-full'>
    ";
    if (!empty($chats_user)) {
        $body .= "
                <button class='bg-[rgb(33,33,33)] w-full rounded-lg py-2 px-3 group hover:bg-gray-600 transition-all border-none mb-4'>
                    <p class='text-white text-center font-circular'>Nuevo chat</p>
                </button>
        ";
        foreach ($chats_user as $id_chat) {
                $first_chat = array_key_first($id_chat);
                $body .= "
                    <button title='chat del dia: {$id_chat[$first_chat]['fec_ini_cha']}' class='bg-[rgb(33,33,33)] w-full rounded-lg py-2 px-3 group hover:bg-gray-600 transition-all border-none mb-4' name='id_chat' value='{$id_chat[$first_chat]['id_chat']}'>
                        <p class='text-white text-left font-light font-circular truncate'>{$id_chat[$first_chat]['mensaje_user']}</p>
                    </button>
                ";
        }

        $body .= "
        </form>
    </div>
        ";
    }


    if ($_POST) {

        $body .= "
        <form id='app_container' onsubmit='return false;' class='bg-[rgb(33,33,33)] flex flex-col p-10 h-[calc(100vh-5rem)] col-span-4 overflow-y-auto items-center custom-scrollbar w-full'>
        ";

        $sql_mensajes_chat = $conexion_db->query("
        SELECT 
            *
        FROM
            chats_ia
        WHERE
            id_chat = {$_POST['id_chat']}
        ");
        $mensajes_chat = [];

        while ($mensaje_chat = $sql_mensajes_chat->fetch(PDO::FETCH_ASSOC)) {
            $mensajes_chat[$mensaje_chat["id_mensaje_chat"]] = $mensaje_chat;
        }

        foreach ($mensajes_chat as $id_mensaje_chat => $datos_mensaje) {

            $mensaje_user = formatearTexto($datos_mensaje['mensaje_user']);
            $mensaje_ia = formatearTexto($datos_mensaje['mensaje_ia']);
            
            $body .= "
            <div id='user_response{$datos_mensaje['id_mensaje_chat']}' class='flex flex-col justify-end items-end w-11/12 md:w-3/5 py-1'>
                <p id='mensajeIA_{$datos_mensaje['id_mensaje_chat']}' class='bg-[rgb(48,48,48)] rounded-lg px-3 py-4 text-white'>
                    {$mensaje_user}
                </p>
            </div>
        
            <div id='ia_response{$datos_mensaje['id_mensaje_chat']}' class='flex flex-col justify-end items-start w-11/12 md:w-3/5 py-1'>
                <p id='respuestaIA_{$datos_mensaje['id_mensaje_chat']}' class='rounded-lg px-3 py-4 text-white'>
                    {$mensaje_ia}
                </p>
            </div>
            ";
        }

        $body .= "
            <div id='input_mensajes' class='w-11/12 md:w-3/5 bg-[rgb(48,48,48)] rounded-xl relative p-2'>
                <div class='h-auto'>
                    <textarea name='mensaje' id='input_ia' rows='1' placeholder='Envia un mensaje a LumBot' class='w-full rounded-lg text-white focus:ring-0 focus:border-none placeholder-gray-400 bg-transparent border-none resize-none overflow-auto'></textarea>
                </div>
                <div class='h-14'>
                    <button id='enviar_mensaje' type='submit' onclick='prueba_ia(); return false;' class='rounded-xl bg-white w-8 h-8 p-1 hover:bg-gray-200 absolute right-2 bottom-2'>
                        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' class='size-6'>
                            <path d='M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z' />
                        </svg>
                    </button>
                </div>
            </div>
        </form>
        ";

        $contador_mensajes = array_key_last($mensajes_chat); 
    } else {
        $body .= "
        <form id='app_container' onsubmit='return false;' class='bg-[rgb(33,33,33)] flex items-center justify-center flex-col p-10 col-span-4 overflow-y-auto custom-scrollbar h-[calc(100vh-5rem)]'>
            <div class='mt-10 mb-6' id='text_inicial'><p class='px-3 text-2xl text-center font-circular font-medium text-white'>¿En que te puedo ayudar?</p></div>
           
            <div id='input_mensajes' class='w-11/12 md:w-3/5 bg-[rgb(48,48,48)] rounded-xl relative p-2'>
                <div class='h-auto'>
                    <textarea name='mensaje' id='input_ia' rows='1' placeholder='Envia un mensaje a LumBot' class='w-full rounded-lg text-white focus:ring-0 focus:border-none placeholder-gray-400 bg-transparent border-none resize-none overflow-auto'></textarea>
                </div>
                <div class='h-14'>
                    <button id='enviar_mensaje' type='submit' onclick='prueba_ia(); return false;' class='rounded-xl bg-white w-8 h-8 p-1 hover:bg-gray-200 absolute right-2 bottom-2'>
                        <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='currentColor' class='size-6'>
                            <path d='M3.478 2.404a.75.75 0 0 0-.926.941l2.432 7.905H13.5a.75.75 0 0 1 0 1.5H4.984l-2.432 7.905a.75.75 0 0 0 .926.94 60.519 60.519 0 0 0 18.445-8.986.75.75 0 0 0 0-1.218A60.517 60.517 0 0 0 3.478 2.404Z' />
                        </svg>
                    </button>
                </div>
            </div>
        </form>
        ";
        $contador_mensajes = 0;
    }

    $id_chat = $_POST["id_chat"] ?? 0;

    $body .= "
</div>
    <script src='src/js/function_ia.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/showdown/2.0.3/showdown.min.js'></script>
    <script src='https://code.jquery.com/jquery-3.6.0.min.js'></script>
    <script>
        var usuario_para_ia = $id_user;
        var id_chat_us = $id_chat;
    </script>
    <script>
        var contador_respuesta_user = $contador_mensajes;
        var contador_respuesta_ia = $contador_mensajes;
    </script>
    ";

    return $body;
}

function logout() {
    session_destroy();
    header("Location: login");
}