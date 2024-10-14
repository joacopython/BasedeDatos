<?php

$formato_intitucional = '/^.+@lamejor\.cl$/';
//preg_match($formato_intitucional, $email_institucional)

function es_fecha_valida($fecha) {
    $formato = 'd/m/y';
    $fecha_obj = DateTime::createFromFormat($formato, $fecha);
    return $fecha_obj && $fecha_obj->format($formato) === $fecha;
}

function typeConvert($valor){
    if (is_numeric($valor)){
        if (strpos($valor, ".") !== false){
            return (float)$valor;
        }
        return (int)$valor;
    }
    
    $valor_minusc = strtolower($valor);

    if ($valor_minusc === "true") {
        return true;
    }
    elseif ($valor_minusc === "false") {
        return false;
    }

    return $valor;
}

function array_to_csv($array, $file_name) {
    // Abrir el archivo para escritura
    $file = fopen($file_name, 'w');

    if ($file === false) {
        return false; // Error al abrir el archivo
    }

    // Obtener los encabezados desde la primera fila del array
    $headers = array_map('trim', array_keys($array[0]));

    // Escribir los encabezados en el archivo
    fputcsv($file, $headers, ";");

    // Escribir cada fila de datos en el archivo
    foreach ($array as $row) {
        fputcsv($file, $row, ";");
    }

    // Cerrar el archivo
    fclose($file);
    
    return true; // Retornar éxito
}


function tabla_handler($nombre_archivo, $data) {
    if ($nombre_archivo == 'estudiantes') {
        $data = limpiar_estudiantes($data);
    } 
    elseif ($nombre_archivo == 'asignaturas') {
        $data = limpiar_asignaturas($data);
    } 
    elseif ($nombre_archivo == 'docentes planificados') {
        $data = limpiar_docentes_planificados($data);
    } 
    elseif ($nombre_archivo == 'notas') {
        $data = limpiar_notas($data);
    } 
    elseif ($nombre_archivo == 'planeacion') {
        $data = limpiar_planeacion($data);
    } 
    elseif ($nombre_archivo == 'planes') {
        $data = limpiar_planes($data);
    } 
    elseif ($nombre_archivo == 'prerrequisitos') {
        $data = limpiar_prerrequisitos($data);
    } else {
        echo "No se encontró la función de limpieza para $nombre_archivo.";
    }
    return $data;
}


function limpiar_estudiantes($data){
    $tablas = [
        'Estudiante' => [],
        'Persona' => [],
        'Bloqueo' => [],
        'UltimoLogro' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'Carrera' => [],
        'InscripcionCarrera' => [],
        'PlanEstudio' => []
    ];

    $datos_malos = [
        'Estudiante' => [],
        'Persona' => [],
        'Bloqueo' => [],
        'UltimoLogro' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'Carrera' => [],
        'InscripcionCarrera' => [],
        'PlanEstudio' => []
    ];
    
    foreach ($data as $key => &$valor) {
        if ($key === "Código Plan"){
            if (!is_string($valor)){
                $datos_malos['PlanEstudio']['codigo_plan'] = $valor;
                $valor = null;
            }
            $tablas['PlanEstudio']['codigo_plan'] = $valor;
        }
        elseif ($key === "Carrera"){
            if (!is_string($valor)){
                $datos_malos['PlanEstudio']['nombre_carrera'] = $valor;
                $datos_malos['Carrera']['nombre_carrera'] = $valor;
                $datos_malos['InscripcionCarrera']['nombre_carrera'] = $valor;
                $valor = null;
            }
            $tablas['PlanEstudio']['nombre_carrera'] = $valor;
            $tablas['Carrera']['nombre_carrera'] = $valor;
            $tablas['InscripcionCarrera']['nombre_carrera'] = $valor;
        }
        elseif ($key === "Cohorte"){
            if (!is_string($valor)){
                $datos_malos['Estudiante']['cohorte'] = $valor;
            }
            $tablas['Estudiante']['cohorte'] = $valor;
        }


        elseif ($key === "Bloqueo"){
            if ($valor === "N"){
                $valor = false;
            }
            else if ($valor === "S"){
                $valor = true;
            }
            else{
                $datos_malos['Bloqueo']['bloqueo'] = $valor;
                $valor = null;
            }
            $tablas['Bloqueo']['bloqueo'] = $valor;
        }
        else if ($key === "Causal Bloqueo"){
            if (!is_string($valor)){
                $datos_malos['Bloqueo']['causal_bloqueo'] = $valor;
                $valor = null;
            }
            $tablas['Bloqueo']['causal_bloqueo'] = $valor;
        }
        else if ($key === "RUN"){
            if (!is_numeric($valor)){
                //pasar a datos malos
                $datos_malos['Estudiante']['run'] = $valor;
                $datos_malos['Persona']['run'] = $valor;
                $datos_malos['EmailPersonal']['run'] = $valor;
                $datos_malos['Telefono']['run'] = $valor;
            }
            elseif (is_numeric($valor)){
                $valor = (int)$valor;
                $tablas['Estudiante']['run'] = $valor;
                $tablas['Persona']['run'] = $valor;
                $tablas['EmailPersonal']['run'] = $valor;
                $tablas['Telefono']['run'] = $valor;
            }
        }
        else if ($key === "Numero de Alumno"){
            if (is_numeric($valor)){
                $valor = (int)$valor;
                $tablas['Estudiante']['numero_estudiante'] = $valor;
                $tablas['Bloqueo']['numero_estudiante'] = $valor;
                $tablas['UltimoLogro']['numero_estudiante'] = $valor;
                $tablas['InscripcionCarrera']['numero_estudiante'] = $valor;
            }
            else{
                $datos_malos['Estudiante']['numero_estudiante'] = $valor;
                $datos_malos['Bloqueo']['numero_estudiante'] = $valor;
                $datos_malos['UltimoLogro']['numero_estudiante'] = $valor;
                $datos_malos['InscripcionCarrera']['numero_estudiante'] = $valor;
            }
        }
        else if ($key === "DV"){
            if ((is_numeric($valor) || strtoupper($valor) === 'K' ) && (gettype($valor) === CHAR(1))) {
                $tablas['Estudiante']['dv'] = $valor;
                $tablas['Persona']['dv'] = $valor;
                $tablas['EmailPersonal']['dv'] = $valor;
                $tablas['Telefono']['dv'] = $valor;
            }
            else{
                $datos_malos['Estudiante']['dv'] = $valor;
                $datos_malos['Persona']['dv'] = $valor;
                $datos_malos['EmailPersonal']['dv'] = $valor;
                $datos_malos['Telefono']['dv'] = $valor;
            }
        }

        else if ($key === "Nombres"){
            if (is_string($valor) && is_string($data[''])){
                $valor = $valor + " " + $data[''];
            }
            else {
                $datos_malos['Estudiante']['nombres'] = $valor;
                $datos_malos['Persona']['nombres'] = $valor;
                $valor = null;
            }
            $tablas['Estudiante']['nombres'] = $valor;
            $tablas['Persona']['nombres'] = $valor;
        }

        else if ($key === "Primer Apellido"){
            if (!is_string($valor)){
                $datos_malos['Estudiante']['apellido_materno'] = $valor;
                $datos_malos['Persona']['apellido_materno'] = $valor;
                $valor = null;
            }
            $tablas['Estudiante']['apellido_paterno'] = $valor;
            $tablas['Persona']['apellido_paterno'] = $valor;
        }
        else if ($key === "Segundo Apellido"){
            
            if (!is_string($valor)){
                $datos_malos['Estudiante']['apellido_materno'] = $valor;
                $datos_malos['Persona']['apellido_materno'] = $valor;
                $valor = null;
            }
            $tablas['Estudiante']['apellido_materno'] = $valor;
            $tablas['Persona']['apellido_materno'] = $valor;
        }

        else if ($key === "Logro"){
            if (!is_string($valor)){
                $datos_malos['UltimoLogro']['ultimo_logro'] = $valor;
                $valor = null;
            }
            $tablas['UltimoLogro']['ultimo_logro'] = $valor;
        }
        else if ($key === "Fecha Logro"){
            if (!es_fecha_valida($valor)){
                $datos_malos['Estudiante']['fecha_logro'] = $valor;
                $valor = null;
            }
            $tablas['Estudiante']['fecha_logro'] = $valor;
        }
        else if ($key === "Última Carga"){
            if (!is_string($valor)){
                $datos_malos['Estudiante']['ultima_carga'] = $valor;
                $valor = null;
            }
            $tablas['Estudiante']['ultima_carga'] = $valor;
        }
    }
    return $tablas;
}

function limpiar_asignatura($data){
    $tablas = [
        'IncluyeCurso' => [],
        'Curso' => [],
        'CursoPrerequisito' => [],
    ];
    $datos_malos = [
        'IncluyeCurso' => [],
        'Curso' => [],
        'CursoPrerequisito' => [],
    ];
    
    foreach ($data as $key => &$valor) {
        if ($key === 'Plan'){
            if (strlen($valor) < 30){
                $tablas['IncluyeCurso']['codigo_plan'] = $valor; 
            }
            else{
                $datos_malos['IncluyeCurso']['codigo_plan'] = $valor; 
            }
        }
        else if ($key === "Asignatura id"){
            if (strlen($valor) < 10){
                $tablas['Curso']['sigla_curso'] = $valor;
            }
            else{
                $datos_malos['Curso']['sigla_curso'] = $valor;
            }
        }
        else if ($key === "Asignatura"){
            if (is_str($valor) && strlen($valor) < 100){
                $tablas['Curso']['nombre_curso'] = $valor;
            }
            else{
                $datos_malos['Curso']['nombre_curso'] = $valor;
            }
        }
        else if ($key === "Nivel"){
            if (is_numeric($valor)){
                $valor_numero = (int)$valor;
                $tablas['Curso']['nivel'] = $valor_numero;
            }else{
                $datos_malos['Curso']['nivel'] = $valor;
            }
        }
        else if ($key === "Prerequisito"){
            if (gettype($valor) === CHAR(1)){
                $tablas['Curso']['prerequisito'] = $valor;
            }else{
                $datos_malos['Curso']['prerequisito'] = $valor;
            }
        }
    }
}


function limpiar_planes($data){
    // exequiel
    $tablas = [
        'Facultad' => [],
        'Carrera' => [],
        'PlanEstudio' => [],
        'Jornada' => [],
        ];

    $datos_malos = [
        'Facultad' => [],
        'Carrera' => [],
        'PlanEstudio' => [],
        'Jornada' => [],
        ];

    foreach ($data as $key => &$valor) {
        if ($key == 'Código Plan') {
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['PlanEstudio']['codigo_plan'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['codigo_plan'] = $valor;
        }
        elseif ($key == "Facultad"){
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['Facultad']['nombre'] = $valor;
                $valor = NULL;
            }
            $tablas['Facultad']['nombre'] = $valor;
        }
        elseif ($key == "Carrera"){
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['Carrera']['nombre'] = $valor;
                $valor = NULL;
            }
            $tablas['Carrera']['nombre'] = $valor;
        }
        elseif ($key == "Plan"){
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['PlanEstudio']['nombre'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['nombre'] = $valor;
        }
        elseif ($key == "Jornada"){
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['Jornada']['jornada_diurna'] = $valor; 
                $valor = NULL;
            }
            $tablas['PlanEstudio']['codigo_plan'] = $valor;
        }            
        elseif ($key == "Sede"){
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['PlanEstudio']['sede'] = $valor; 
                $valor = NULL;
            }
            $tablas['PlanEstudio']['sede'] = $valor;
        } 
        elseif ($key == "Grado"){
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['PlanEstudio']['grado'] = $valor; 
                $valor = NULL;
            }
            $tablas['PlanEstudio']['grado'] = $valor;
        }
        elseif ($key == "Modalidad"){
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['PlanEstudio']['modalidad'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['modalidad'] = $valor;
        }
        elseif ($key == "Inicio Vigencia"){
            if (es_fecha_valida($valor) == false || empty($valor)) {
                $datos_malos['PlanEstudio']['inicio'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['inicio'] = $valor;
        } 
    }
}

function limpiar_docentes_planificados($data){
    $tablas = [
        'Profesor' => [],
        'Persona' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'Administrativo' => [],
        'Jornada' => []
    ];
    $datos_malos = [
        'Profesor' => [],
        'Persona' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'Administrativo' => [],
        'Jornada' => []
    ];
    
    foreach ($data as $key => &$valor) {
        if ($valor !== NULL){
            if ($key === 'RUN') {
                if (is_numeric($valor)){
                    $valor = (int)$valor;
                    $valor = trim($valor);
                    $tablas['Persona']['run'] = $valor;
                    $tablas['EmailPersonal']['run'] = $valor;
                    $tablas['Telefono']['run'] = $valor;
                    $tablas['Jornada']['run'] = $valor;
                } 
                else{
                    $datos_malos['Persona']['run'] = $valor;
                    $datos_malos['EmailPersonal']['run'] = $valor;
                    $datos_malos['Telefono']['run'] = $valor;
                    $datos_malos['Jornada']['run'] = $valor;
                }
            }elseif ($key === 'Nombre'){
                if (is_string($valor)){
                    $tablas['Persona']['nombres'] = $valor;
                }
                else{
                    $datos_malos['Persona']['nombres'] = $valor;
                }
            }elseif ($key === 'telefono'){
                if (is_numeric($valor) && strlen($valor) < 30){
                    $valor = (int)$valor;
                    $valor = trim($valor);
                    $tablas['Telefono']['telefono'] = $valor;
                }
                else{
                    $datos_malos['Persona']['telefono'] = $valor;
                }
            }elseif ($key === 'email personal'){
                if (is_string($valor)){
                    $tablas['EmailPersonal']['email_personal'] = $valor;
                }
                else{
                    $datos_malos['EmailPersonal']['email_personal'] = $valor;
                }
            }elseif ($key === 'email  institucional'){
                if (is_string($valor) && preg_match($formato_intitucional, $valor)){
                    $tablas['Persona']['email_institucional'] = $valor;
                }
                else{
                    $datos_malos['Persona']['email_institucional'] = $valor;
                }
            }elseif ($key === 'DEDICACIÓN'){
                if (is_numeric($valor)){
                    if(INT($valor) <= 40){
                        $tablas['Profesor']['email_personal'] = INT($valor);
                        $tablas['Adiministrativo']['email_personal'] = INT($valor);
                    }
                }
                else{
                    $datos_malos['Profesor']['email_personal'] = INT($valor);
                    $datos_malos['Adiministrativo']['email_personal'] = INT($valor);
                }
            }elseif ($key === 'CONTRATO'){
                if (is_string($valor)){
                    $tablas['Profesor']['contrato'] = $valor;
                    $tablas['Adiministrativo']['contrato'] = $valor;
                }
                else{
                    $datos_malos['Profesor']['contrato'] = $valor;
                    $datos_malos['Adiministrativo']['contrato'] = $valor;
                }
            }elseif ($key === 'DIURNO'){
                if ($valor === 'diurno'){
                    $tablas['Jornada']['jornada_diurna'] = $valor;
                }
                else{
                    $datos_malos['Jornada']['jornada_diurna'] = $valor;
                }
            }elseif ($key === 'VESPERTINO'){
                if (is_string($valor)){
                    $tablas['Profesor']['contrato'] = $valor;
                    $tablas['Adiministrativo']['contrato'] = $valor;
                }
                else{
                    $datos_malos['Profesor']['contrato'] = $valor;
                    $datos_malos['Adiministrativo']['contrato'] = $valor;
                }
            }
        }
    }
}

function limpiar_notas($data){
    // exequiel

}
?>