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
    // Definir el path de la carpeta
    $directory = 'data_mala/';

    // Asegurarse de que la carpeta existe, si no, crearla
    if (!is_dir($directory)) {
        mkdir($directory, 0777, true); // Crea la carpeta si no existe
    }

    // Crear el path completo del archivo
    $file_path = $directory . $file_name;

    // Abrir el archivo para escritura
    $file = fopen($file_path, 'w');

    if ($file === false) {
        return false; // Error al abrir el archivo
    }

    // Obtener los encabezados desde la primera fila del array
    $headers = array_map('trim', array_keys($array));

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
        'InscripcionAlumno' => [],
        'PlanEstudio' => []
    ];
    
    foreach ($data as $key => &$valor) {
        if ($key === "Código Plan"){
            if (!is_string($valor)){
                $datos_malos['PlanEstudio']['codigo_plan'] = $valor;
                $datos_malos['InscripcionAlumno']['codigo_plan'] = $valor;
                $valor = null;
            }
            $tablas['PlanEstudio']['codigo_plan'] = $valor;
        }
        elseif ($key === "Carrera"){
            if (!is_string($valor)){
                $datos_malos['PlanEstudio']['nombre_carrera'] = $valor;
                $valor = null;
            }
            $tablas['PlanEstudio']['nombre_carrera'] = $valor;
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
                $tablas['InscripcionAlumno']['numero_estudiante'] = $valor;
            }
            else{
                $datos_malos['Estudiante']['numero_estudiante'] = $valor;
                $datos_malos['Bloqueo']['numero_estudiante'] = $valor;
                $datos_malos['UltimoLogro']['numero_estudiante'] = $valor;
                $datos_malos['InscripcionAlumno']['numero_estudiante'] = $valor;
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
    #array_to_csv($datos_malos, "Estudiantes_malos");
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
            if (!is_string($valor) || strlen($valor) > 30){
                $datos_malos['IncluyeCurso']['codigo_plan'] = $valor;
                $valor = null;
            }
            $tablas['IncluyeCurso']['codigo_plan'] = null;
        }
        else if ($key === "Asignatura id"){
            if (!is_string($valor) || strlen($valor) > 10){
                $datos_malos['Curso']['sigla_curso'] = $valor;
                $valor = null;
            }
            $tablas['Curso']['sigla_curso'] = $valor;
        }
        else if ($key === "Asignatura"){
            if (!is_str($valor) || strlen($valor) > 100){
                $datos_malos['Curso']['nombre_curso'] = $valor;
                $valor = null;
            }
            $tablas['Curso']['nombre_curso'] = $valor;
        }
        else if ($key === "Nivel"){
            if (!is_numeric($valor)){
                $datos_malos['Curso']['nivel'] = $valor;
                $valor = null;
            }else{
                $valor = (int)$valor;
            }
            $tablas['Curso']['nivel'] = $valor;
        }
        else if ($key === "Prerequisito"){
            if (gettype($valor) !== CHAR(1)){
                $datos_malos['Curso']['prerequisito'] = $valor;
                $valor = null;
            }
            $tablas['Curso']['prerequisito'] = $valor;
        }
    }
    array_to_csv($datos_malos, "Asigantura_malas");
    return $tablas;
}


function limpiar_planes($data){
    // exequiel
    $tablas = [
        'Facultad' => [],
        'PlanEstudio' => [],
        'Jornada' => [],
        'InscripcionAlumno' => []
        ];

    $datos_malos = [
        'Facultad' => [],
        'PlanEstudio' => [],
        'Jornada' => [],
        'InscripcionAlumno' => []
        ];

    foreach ($data as $key => &$valor) {
        if ($key == 'Código Plan') {
            if (!is_string($valor) || empty($valor)) {
                $datos_malos['PlanEstudio']['codigo_plan'] = $valor;
                $datos_malos['InscripcionAlumno']['codigo_plan'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['codigo_plan'] = $valor;
            $tablas['InscripcionAlumno']['codigo_plan'] = $valor;
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
                $datos_malos['PlanEstudio']['nombre_carrera'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['nombre_carrera'] = $valor;
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
    array_to_csv($datos_malos, "Planes_malos");
    return $tablas;
}

function limpiar_docentes_planificados($data){
    $tablas = [
        'Profesor' => [],
        'Persona' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'Administrativo' => [],
        'Jornada' => [],
        'PlanEstudio' => []
    ];
    $datos_malos = [
        'Profesor' => [],
        'Persona' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'Administrativo' => [],
        'Jornada' => [],
        'PlanEstudio' => []
    ];
    
    foreach ($data as $key => &$valor){
        if ($key === 'RUN') {
            if (!is_numeric($valor)){
                $datos_malos['Persona']['run'] = $valor;
                $datos_malos['EmailPersonal']['run'] = $valor;
                $datos_malos['Telefono']['run'] = $valor;
                $datos_malos['Jornada']['run'] = $valor;
                $valor = null;
            }else{
                $valor = (int)$valor;
                $valor = trim($valor);
            }
            $tablas['Jornada']['run'] = $valor;
            $tablas['Persona']['run'] = $valor;
            $tablas['EmailPersonal']['run'] = $valor;
            $tablas['Telefono']['run'] = $valor;
        }elseif ($key === 'Nombre'){
            if (!is_string($valor)){
                $datos_malos['Persona']['nombres'] = $valor;
                $valor = null;
            }
            $tablas['Persona']['nombres'] = $valor;
        }elseif ($key === 'telefono'){
            if (!is_numeric($valor) || strlen($valor) > 30){
                $datos_malos['Persona']['telefono'] = $valor;
                $valor = null;
            }
            else{
                $valor = (int)$valor;
                $valor = trim($valor);
            }
            $tablas['Telefono']['telefono'] = $valor;
        }elseif ($key === 'email personal'){
            if (!is_string($valor)){
                $datos_malos['EmailPersonal']['email_personal'] = $valor;
                $valor = null;
            }
            $tablas['EmailPersonal']['email_personal'] = $valor;
        }elseif ($key === 'email  institucional'){
            if (!is_string($valor) || !preg_match($formato_intitucional, $valor)){
                $datos_malos['Persona']['email_institucional'] = $valor;
                $valor = null;
            }
            $tablas['Persona']['email_institucional'] = $valor;
        }elseif ($key === 'DEDICACIÓN'){
            if (!is_numeric($valor) || INT($valor) >=40){
                $datos_malos['Profesor']['email_personal'] = INT($valor);
                $datos_malos['Adiministrativo']['email_personal'] = INT($valor);
                $valor = null;
            }else{
                $valor = INT($valor);
            }
            $tablas['Profesor']['email_personal'] = $valor;
            $tablas['Adiministrativo']['email_personal'] = $valor;
        }elseif ($key === 'CONTRATO'){
            if (!is_string($valor)){
                $datos_malos['Adiministrativo']['contrato'] = $valor;
                $datos_malos['Profesor']['contrato'] = $valor;
                $valor = null;
            }
            $tablas['Profesor']['contrato'] = $valor;
            $tablas['Adiministrativo']['contrato'] = $valor;
        }elseif ($key === 'DIURNO'){
            if (trim($valor) === 'diurno'){
                $tablas['Jornada']['jornada_diurna'] = TRUE;
            }elseif(trim($valor) === 'VESPERTINO'){
                $tablas['Jornada']['jornada_vespertina'] = TRUE;
            }
            else{
                $tablas['Jornada']['jornada_diurna'] = FALSE;
                $datos_malos['Jornada']['jornada_diurna'] = trim($valor);
            }
        }elseif ($key === 'VESPERTINO'){
            if (trim($valor) === 'vespertino'){
                $tablas['Jornada']['jornada_vespertina'] = TRUE;
            }elseif(trim($valor) === 'diurno'){
                $tablas['Jornada']['jornada_diurna'] = TRUE;
            }
            else{
                $tablas['Jornada']['jornada_vespertina'] = FALSE;
                $tablas['Jornada']['jornada_diurna'] = FALSE;
                $datos_malos['Jornada']['jornada_vespertina'] = $valor;
            }
        }elseif ($key === 'SEDE'){
            if (!is_string($valor) || strlen($valor) > 30){
                $datos_malos['PlanEstudio']['sede'] = $valor;
                $valor = null;
            }
            $tablas['PlanEstudio']['sede'] = $valor;
        }elseif ($key === 'CARRERA'){
            if (!is_string($valor) || strlen($valor) > 100){
                $datos_malos['PlanEstudio']['nombre_carrera'] = $valor;
            }
            $tablas['PlanEstudio']['nombre_carrera'] = $valor;
        }elseif ($key === 'GRADO ACADÉMICO'){
            if (!is_string($valor)){
                $datos_malos['Profesor']['grado_academico'] = $valor;
                $datos_malos['Administrativo']['grado_academico'] = $valor;
                $valor = TRUE;
            }
            $tablas['Profesor']['grado_academico'] = $valor;
            $tablas['Administrativo']['grado_academico'] = $valor;
        }
        elseif ($key === 'JERARQUÍA ') {
            if (!is_string($valor)){
                $datos_malos['Profesor']['jerarquia_academica'] = $valor;
                $valor = null;
            }
            $tablas['Profesor']['jerarquia_academica'] = $valor;
        }elseif ($key === 'CARGO'){
            if (!is_string($valor)){
                $datos_malos['Profesor']['cargo'] = $valor;
                $valor = null;
            }
            $tablas['Profesor']['cargo'] = $valor;
        }elseif ($key === 'ESTAMENTO'){
            if (is_string($valor) || strlen($valor) > 30){
                $datos_malos['Persona']['estamento'] = $valor;
                $valor = null;
            }
            $tablas['Persona']['telefono'] = $valor;
        }
    }
    array_to_csv($datos_malos, "Docentes_planificados_malos");
    return $tablas;
}


function limpiar_notas($data){
    // exequiel
    $tablas = [
        'PlanEstudio' => [],
        'Estudiante' => [],
        'Persona' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'HistorialAcademico' => [],
        'Curso' => [],
        'OfertaAcademica' => []
        ];

    $datos_malos = [
        'PlanEstudio' => [],
        'Estudiante' => [],
        'Persona' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
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
         
        elseif ($key === "Nombres"){
            if (!is_string($valor)){
                $datos_malos['Persona']['nombres'] = $valor;
                $valor = NULL;
            }
            $tablas['Persona']['nombres'] = $valor;
        }

        elseif ($key === "Apellido Paterno"){
            if (!is_string($valor)){
                $datos_malos['Persona']['apellido_paterno'] = $valor;
                $valor = NULL;
            }
            $tablas['Persona']['apellido_paterno'] = $valor;
        }
        elseif ($key === "Apellido Materno"){  
            if (!is_string($valor)){
                $datos_malos['Persona']['apellido_materno'] = $valor;
                $valor = NULL;
            }
            $tablas['Persona']['apellido_materno'] = $valor;
        }
        
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
    array_to_csv($datos_malos, "Notas_malas");
    return $tablas;
}

function limpiar_planeacion($data){
        // exequiel
        $tablas = [
            'Persona' => [],
            'InscripcionCarrera' => [],
            'PlanEstudio' => [],
            'HistorialAcademico' => [],
            'Curso' => [],
            'OfertaAcademica' => [],
            'Departamento' => [],
            'Salas' => []
        ];
        
        $datos_malos = [
            'Persona' => [],
            'InscribeAlumno' => [],
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
                    $datos_malos['Salas']['periodo'] = $valor;
                    $valor = NULL;
                }
                $tablas['HistorialAcademico']['periodo'] = $valor;
                $tablas['Curso']['periodo'] = $valor;
                $tablas['OfertaAcademica']['periodo'] = $valor;
                $tablas['Salas']['periodo'] = $valor;
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
                    $datos_malos['IncluyeCurso']['sigla_curso'] = $valor;
                    $datos_malos['HistorialAcademico']['sigla_curso'] = $valor;
                    $datos_malos['OfertaAcademica']['sigla_curso'] = $valor;
                    $valor = NULL;
                }
                $tablas['Curso']['sigla_curso'] = $valor;
                $tablas['IncluyeCurso']['sigla_curso'] = $valor;
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
                    $datos_malos['OfertaAcademica']['seccion'] = $valor; 
                    $valor = NULL;
                }
                $valor = (int) $valor;
                $tablas['HistorialAcademico']['seccion'] = $valor;
                $tablas['OfertaAcademica']['seccion'] = $valor;
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
                    $datos_malos['OfertaAcademica']['profesor_principal'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['profesor_principal'] = $valor;
            }
            
            elseif ($key === "RUN"){
                $codigos_departamento = array_column($tablas['Departamento'], 'codigo_departamento');
                if (in_array($valor, $tablas['Departamento'])) {
                    // Si es un código de departamento, lo ignoras
                    break;
                }
                elseif (!is_numeric($valor)){
                    $datos_malos['Profesor']['run'] = $valor;
                    $datos_malos['Estudiante']['run'] = $valor;
                    $datos_malos['Persona']['run'] = $valor;
                    $datos_malos['EmailPersonal']['run'] = $valor;
                    $datos_malos['Telefono']['run'] = $valor;
                    $datos_malos['Jornada']['run'] = $valor;
                    $datos_malos['Administrativo']['run'] = $valor;
                } else{
                $valor = (int)$valor;
                $tablas['Profesor']['run'] = $valor;
                $tablas['Estudiante']['run'] = $valor;
                $tablas['Persona']['run'] = $valor;
                $tablas['EmailPersonal']['run'] = $valor;
                $tablas['Telefono']['run'] = $valor;
                $tablas['Jornada']['run'] = $valor;
                $tablas['Administrativo']['run'] = $valor;
                }
            }
            
            elseif ($key === "Nombre Docente"){
                if (!is_string($valor)){
                    $datos_malos['Persona']['nombres'] = $valor;
                    $valor = NULL;
                }
                $tablas['Persona']['nombres'] = $valor;
            }
    
            elseif ($key === "1er Apellido Docente"){
                if (!is_string($valor)){
                    $datos_malos['Persona']['apellido_paterno'] = $valor;
                    $valor = NULL;
                }
                $tablas['Persona']['apellido_paterno'] = $valor;
            }
            
            elseif ($key === "2so Apellido Docente"){  
                if (!is_string($valor)){
                    $datos_malos['Persona']['apellido_materno'] = $valor;
                    $valor = NULL;
                }
                $tablas['Persona']['apellido_materno'] = $valor;
            }

            elseif ($key === "Jerarquización"){  
                if (!is_string($valor)){
                    $datos_malos['Profesor']['jerarquia_academica'] = $valor;
                    $valor = NULL;
                }
                $tablas['Profesor']['jerarquia_academica'] = $valor;
            }
    }
    array_to_csv($datos_malos, "planeacion_mala");
    return $tablas;
}
?>