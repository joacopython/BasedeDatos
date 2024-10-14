<?php

$formato_intitucional = '/^.+@lamejor\.cl$/';
//preg_match($formato_intitucional, $email_institucional)

function es_fecha_valida($fecha) {
    $formato = 'd/m/y';
    $fecha_obj = DateTime::createFromFormat($formato, $fecha);
    return $fecha_obj && $fecha_obj->format($formato) === $fecha;
}

function es_hora_valida($hora) {
    $formato = 'H:i';  // Formato de 24 horas (ejemplo: 12:41)
    $hora_obj = DateTime::createFromFormat($formato, $hora);
    return $hora_obj && $hora_obj->format($formato) === $hora;
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
<<<<<<< HEAD
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
            if (trim($valor) === 'diurno'){
                $tablas['Jornada']['jornada_diurna'] = TRUE;
            }
            else{
                $datos_malos['Jornada']['jornada_diurna'] = trim($valor);
            }
        }elseif ($key === 'VESPERTINO'){
            if (trim($valor) === 'vespertino'){
                $tablas['Jornada']['jornada_vespertina'] = TRUE;
            }
            else{
                $datos_malos['Jornada']['jornada_vespertina'] = trim($valor);
            }
        }elseif ($key === 'SEDE'){
            if (trim($valor) === 'vespertino'){
                $tablas['Jornada']['jornada_vespertina'] = trim($valor);
            }
            else{
                $datos_malos['Jornada']['jornada_vespertina'] = trim($valor);
            }
        }elseif ($key === 'SEDE'){
            if (trim($valor) === 'vespertino'){
                $tablas['Jornada']['jornada_vespertina'] = trim($valor);
            }
            else{
                $datos_malos['Jornada']['jornada_vespertina'] = trim($valor);
            }
        }elseif ($key === 'VESPERTINO'){
            if (trim($valor) === 'vespertino'){
                $tablas['Jornada']['jornada_vespertina'] = trim($valor);
            }
            else{
                $datos_malos['Jornada']['jornada_vespertina'] = trim($valor);
=======
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
>>>>>>> 3eff3bbf5d9431f4877ca6820c395bffc3469209
            }
        }
    }
}

function limpiar_notas($data){
    // exequiel

<<<<<<< HEAD
    $datos_malos = [
        'Estudiante' => [],
        'Persona' => [],
        'Carrera' => [],
        'InscripcionCarrera' => [],
        'PlanEstudio' => [],
        'HistorialAcademico' => [],
        'Curso' => [],
        'OfertaAcademica' => []
    ];

    foreach ($data as $key => &$valor){
        if ($key === "Código Plan"){
            if (!is_string($valor)){
                $datos_malos['PlanEstudio']['codigo_plan'] = $valor;
                $valor = NULL;
            }  // las primary key malas no deberian agregarse a $tablas vdd?
            $tablas['PlanEstudio']['codigo_plan'] = $valor;
        }

        elseif ($key === "Plan"){
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['PlanEstudio']['nombre_plan'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['nombre_plan'] = $valor;
        }

        elseif ($key === "Cohorte"){
            if (!is_string($valor)){
                $datos_malos['Estudiante']['cohorte'] = $valor;
            }
            $tablas['Estudiante']['cohorte'] = $valor;
        }

        elseif ($key == "Sede"){
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['PlanEstudio']['sede'] = $valor; 
                $valor = NULL;
            }
            $tablas['PlanEstudio']['sede'] = $valor;
        } 

        elseif ($key === "RUN"){
            if (!is_numeric($valor)){
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

        elseif ($key === "DV"){
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
         // Agregar atributos a estudiante //
        elseif ($key === "Nombres"){
            if (!is_string($valor)){
                $datos_malos['Estudiante']['nombres'] = $valor;
                $datos_malos['Persona']['nombres'] = $valor;
                $valor = NULL;
            }
            $tablas['Estudiante']['nombres'] = $valor;
            $tablas['Persona']['nombres'] = $valor;
        }

        elseif ($key === "Apellido Paterno"){
            if (!is_string($valor)){
                $datos_malos['Estudiante']['apellido_materno'] = $valor;
                $datos_malos['Persona']['apellido_materno'] = $valor;
                $valor = NULL;
            }
            $tablas['Estudiante']['apellido_paterno'] = $valor;
            $tablas['Persona']['apellido_paterno'] = $valor;
        }
        elseif ($key === "Apellido Materno"){  
            if (!is_string($valor)){
                $datos_malos['Estudiante']['apellido_materno'] = $valor;
                $datos_malos['Persona']['apellido_materno'] = $valor;
                $valor = NULL;
            }
            $tablas['Estudiante']['apellido_materno'] = $valor;
            $tablas['Persona']['apellido_materno'] = $valor;
        }
        // Agregar atributos a estudiane //
        else if ($key === "Número de Alumno"){
            if (is_numeric($valor)){
                $valor = (int)$valor;
                $tablas['Estudiante']['numero_estudiante'] = $valor;
                $tablas['HistorialAcademico']['numero_estudiante'] = $valor;
            }
            else{
                $tablas['Estudiante']['numero_estudiante'] = $valor;
                $tablas['HistorialAcademico']['numero_estudiante'] = $valor;
            }
        }

        elseif ($key === "Periodo Asignatura"){  
            if (!is_string($valor)){
                $datos_malos['HistorialAcademico']['periodo'] = $valor;
                $datos_malos['Curso']['periodo'] = $valor;
                $valor = NULL;
            }
            $tablas['HistorialAcademico']['periodo'] = $valor;
            $tablas['Curso']['periodo'] = $valor;
        }

        elseif ($key === "Código Asignatura"){  
            if (!is_string($valor)){
                $datos_malos['HistorialAcademico']['sigla_curso'] = $valor;
                $datos_malos['OfertaAcademica']['sigla_curso'] = $valor;
                $datos_malos['Curso']['sigla_curso'] = $valor;
                $valor = NULL;
            }
            $tablas['HistorialAcademico']['sigla_curso'] = $valor;
            $tablas['OfertaAcademica']['sigla_curso'] = $valor;
            $tablas['Curso']['sigla_curso'] = $valor;
        }

        elseif ($key === "Convocatoria"){  
            if (!is_string($valor)){
                $datos_malos['HistorialAcademico']['convocatoria'] = $valor;
                $valor = NULL;
            }
            $tablas['HistorialAcademico']['convocatoria'] = $valor;
        }

        elseif ($key === "Calificación"){  
            if (!is_string($valor)){
                $datos_malos['HistorialAcademico']['calificacion'] = $valor;
                $valor = NULL;
            }
            $tablas['HistorialAcademico']['calificacion'] = $valor;
        }

        elseif ($key === "Nota"){  
            if (!is_numeric($valor)){
                $datos_malos['HistorialAcademico']['nota'] = $valor;
                $valor = NULL;
            }
            $valor = (float) $valor;
            $tablas['HistorialAcademico']['nota'] = $valor;
        }        
    }

function limpiar_planeacion($data){
        // exequiel
        $tablas = [
            'Estudiante' => [],
            'Persona' => [],
            'Bloqueo' => [],
            'UltimoLogro' => [],
            'EmailPersonal' => [],
            'Telefono' => [],
            'Carrera' => [],
            'InscripcionCarrera' => [],
            'PlanEstudio' => [],
            'HistorialAcademico' => [],
            'Curso' => [],
            'OfertaAcademica' => [],
            'Departamento' => [],
            'Salas' => []
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
            'PlanEstudio' => [],
            'HistorialAcademico' => [],
            'Curso' => [],
            'OfertaAcademica' => [],
            'Departamento' => [],
            'Salas' => []
        ];
    
        foreach ($data as $key => &$valor) {
            if ($key === "Periodo"){  
                if (!is_string($valor)){
                    $datos_malos['HistorialAcademico']['periodo'] = $valor;
                    $datos_malos['Curso']['periodo'] = $valor;
                    $datos_malos['OfertaAcademica']['periodo'] = $valor;
                    $valor = NULL;
                }
                $tablas['HistorialAcademico']['periodo'] = $valor;
                $tablas['Curso']['periodo'] = $valor;
                $tablas['OfertaAcademica']['periodo'] = $valor;
            }

            elseif ($key == "Sede"){
                if (!is_string($valor) || empty($valor)) {
                    $datos_malos['PlanEstudio']['sede'] = $valor; 
                    $valor = NULL;
                }
                $tablas['PlanEstudio']['sede'] = $valor;
            } 

            elseif ($key === "Facultad"){
                if (!is_string($valor) || empty($valor)) {
                    $datos_malos['PlanEstudio']['nombre_facultad'] = $valor;
                    $valor = NULL;
                }
                $tablas['PlanEstudio']['nombre_facultad'] = $valor;
            }
    
            elseif ($key === "Código Depto"){
                if (!is_numeric($valor)){
                    $datos_malos['Departamento']['codigo_departamento'] = $valor;
                    $valor = NULL;
                }
                $valor = (int) $valor;
                $tablas['Departamento']['codigo_departamento'] = $valor;
            }
            
            elseif ($key === "Departamento"){
                if (!is_string($valor)){
                    $datos_malos['Departamento']['nombre'] = $valor;
                    $valor = NULL;
                }
                $tablas['Departamento']['nombre'] = $valor;
            }

            elseif ($key === "Id Asignatura"){
                if (!is_string($valor)){
                    $datos_malos['Curso']['sigla_curso'] = $valor;
                    $datos_malos['HistorialAcademico']['sigla_curso'] = $valor;
                    $datos_malos['OfertaAcademica']['sigla_curso'] = $valor;
                    $valor = NULL;
                }
                $tablas['Curso']['sigla_curso'] = $valor;
                $tablas['HistorialAcademico']['sigla_curso'] = $valor;
                $tablas['OfertaAcademica']['sigla_curso'] = $valor;
            }

            elseif ($key === "Asignatura"){
                if (!is_string($valor)){
                    $datos_malos['Curso']['nombre_curso'] = $valor;
                    $valor = NULL;
                }
                $tablas['Curso']['nombre_curso'] = $valor;
            }
    
            elseif ($key == "Seccion"){ // CONVERSAR DISCORD
                if (!is_numeric($valor)){
                    $datos_malos['HistorialAcademico']['seccion'] = $valor; 
                    $valor = NULL;
                }
                $valor = (int) $valor;
                $tablas['HistorialAcademico']['seccion'] = $valor;
            } 

            elseif ($key === "Duración"){
                if (!is_string($valor)){
                    $datos_malos['PlanEstudio']['duracion'] = $valor;
                    $datos_malos['OfertaAcademica']['duracion'] = $valor;
                    $valor = NULL;
                }
                $tablas['PlanEstudio']['duracion'] = $valor;
                $tablas['OfertaAcademica']['duracion'] = $valor;
            }

            elseif ($key === "Jornada"){
                if (is_string($valor)) {
                    if (strtoupper($valor) == 'DIURNO'){
                        $tablas['Jornada']['jornada_diurna'] = true;
                    }
                    elseif (strtoupper($valor) == 'VESPERTINO'){
                        $tablas['Jornada']['jornada_vespertina'] = true;
                    } else{
                        $datos_malos['Jornada']['jornada_diurna'] = $valor;
                        $datos_malos['Jornada']['jornada_vespertina'] = $valor; 
                        $valor = false;
                    }
                } else{
                    $datos_malos['Jornada']['jornada_diurna'] = $valor; 
                    $datos_malos['Jornada']['jornada_vespertina'] = $valor; 
                    $valor = false;
                }
            }
            
            elseif ($key === "Cupo"){ // VACANTES SOLO ESTÁ EN SALAS 
                if (!is_numeric($valor)){
                    $datos_malos['Salas']['vacantes'] = $valor;
                }
                $tablas = (int) $tablas;
                $tablas['Salas']['vacantes'] = $valor;
            }

            elseif ($key === "Inscrito"){  
                if (!is_numeric($valor)){
                    $datos_malos['OfertaAcademica']['inscritos'] = $valor;                
                }
                $tablas = (int) $tablas;
                $tablas['OfertaAcademica']['inscritos'] = $valor;
            }

            elseif ($key === "Día"){
                if (!is_string($valor)){
                    $datos_malos['OfertaAcademica']['dia'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['dia'] = $valor;
            }

            elseif ($key == "Hora Inicio"){
                if (es_hora_valida($valor) == false || empty($valor)) {
                    $datos_malos['OfertaAcademica']['hora_inicio'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['hora_inicio'] = $valor;
            } 

            elseif ($key == "Hora Fin"){
                if (es_hora_valida($valor) == false || empty($valor)) {
                    $datos_malos['OfertaAcademica']['hora_fin'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['horario_fin'] = $valor;
            } 

            elseif ($key == "Fecha Inicio"){
                if (es_fecha_valida($valor) == false || empty($valor)) {
                    $datos_malos['OfertaAcademica']['fecha_inicio'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['fecha_inicio'] = $valor;
            } 

            elseif ($key == "Fecha Fin"){
                if (es_fecha_valida($valor) == false || empty($valor)) {
                    $datos_malos['OfertaAcademica']['fecha_fin'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['fecha_fin'] = $valor;
            } 

            elseif ($key === "Lugar"){
                if (!is_string($valor)){
                    $datos_malos['Salas']['sala'] = $valor;
                    $valor = NULL;
                }
                $tablas['Salas']['sala'] = $valor;
            }

            elseif ($key === "Edificio"){
                if (!is_string($valor)){
                    $datos_malos['Salas']['edificio'] = $valor;
                    $valor = NULL;
                }
                $tablas['Salas']['edificio'] = $valor;
            }

            elseif ($key === "Profesor Principal"){
                if (!is_string($valor) || empty($valor)){
                    $datos_malos['Profesor']['duracion'] = $valor;
                    $datos_malos['OfertaAcademica']['duracion'] = $valor;
                    $valor = NULL;
                }
                $tablas['PlanEstudio']['duracion'] = $valor;
                $tablas['OfertaAcademica']['duracion'] = $valor;
            }



    }
=======
>>>>>>> 3eff3bbf5d9431f4877ca6820c395bffc3469209
}
?>