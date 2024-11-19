<?php

//preg_match($formato_intitucional, $email_institucional)

function remove_bom($string) {
    if ($string === null){
        return $string;
    }
    if (substr($string, 0, 3) === "\xef\xbb\xbf") {
        $string = substr($string, 3);
    }
    return $string;
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
        $data = limpiar_prerequisitos($data);
    }
    elseif ($nombre_archivo == 'usuarios') {
        $data = limpiar_usuarios($data);
    } else {
        echo "No se encontró la función de limpieza para $nombre_archivo.";
    }
    return $data;
}

function limpiar_usuarios($data) {
    $tablas = [
        'usuarios' => []
    ];

    foreach ($data as $key => $valor) {
        if ($key === "password") {
            // Hash the password
            $tablas['usuarios']['password'] = password_hash($valor, PASSWORD_DEFAULT);
        } else {
            // Add other fields as they are
            $tablas['usuarios'][$key] = $valor;
        }
    }
    
    return $tablas;
}
function limpiar_estudiantes($data){
    $tablas = [
        'Persona' => [],
        'Estudiante' => [],
        'Bloqueo' => [],
        'UltimoLogro' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'PlanEstudio' => []
    ];

    $datos_malos = [
        'Persona' => [],
        'Estudiante' => [],
        'Bloqueo' => [],
        'UltimoLogro' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'InscripcionAlumno' => [],
        'PlanEstudio' => []
    ];
    
    foreach ($data as $key => &$valor) {
        if (remove_bom($key) === "Código Plan"){
            if (!is_string($valor)){
                //$datos_malos['PlanEstudio']['codigo_plan'] = $valor;
                //$datos_malos['InscripcionAlumno']['codigo_plan'] = $valor;
                $valor = 'NULL';
            }
            $tablas['PlanEstudio']['codigo_plan'] = $valor;
            $tablas['InscripcionAlumno']['codigo_plan'] = $valor;
        }
        elseif ($key === "Carrera"){
            if (!is_string($valor)){
                //$datos_malos['PlanEstudio']['nombre_carrera'] = $valor;
                $valor = null;
            }
            $tablas['PlanEstudio']['nombre_carrera'] = $valor;
        }
        elseif ($key === "Cohorte"){
            if (!is_string($valor)){
                //$datos_malos['Estudiante']['cohorte'] = $valor;
            }
            $tablas['Estudiante']['cohorte'] = $valor;
        }

        elseif ($key === "Bloqueo"){
            if (trim(strtoupper($valor)) === "N"){
                $valor = 'FALSE';
            }
            else if (trim(strtoupper($valor)) === "S"){
                $valor = 'TRUE';
            }
            else{
                //$datos_malos['Bloqueo']['bloqueo'] = $valor;
                $valor = 'FALSE';
            }
            $tablas['Bloqueo']['bloqueo'] = $valor;
        }
        else if ($key === "Causal Bloqueo"){
            if (!is_string($valor)){
                //$datos_malos['Bloqueo']['causal_bloqueo'] = $valor;
                $valor = null;
            }

            $tablas['Bloqueo']['causal_bloqueo'] = $valor;
        }

        else if ($key === "RUN"){
            if (!is_numeric($valor)){
                $valor = -1;
            }
            elseif (is_numeric($valor)){
                $valor = (int)$valor;
            }
            $tablas['Persona']['run'] = $valor;
            $tablas['Estudiante']['run'] = $valor;
            $tablas['EmailPersonal']['run'] = $valor;
            $tablas['Telefono']['run'] = $valor;
        }
        else if ($key === "Número de alumno"){
            if ($valor === NULL){
                $tablas['Estudiante']['numero_estudiante'] = -1;
                $tablas['Bloqueo']['numero_estudiante'] = -1;
                $tablas['UltimoLogro']['numero_estudiante'] = -1;
                $tablas['InscripcionAlumno']['numero_estudiante'] = -1;
            }else{
                if (is_numeric($valor)){
                    $valor = (int)$valor;
                    $tablas['Estudiante']['numero_estudiante'] = $valor;
                    $tablas['Bloqueo']['numero_estudiante'] = $valor;
                    $tablas['UltimoLogro']['numero_estudiante'] = $valor;
                    $tablas['InscripcionAlumno']['numero_estudiante'] = $valor;
                }
                else{
                    //$datos_malos['Estudiante']['numero_estudiante'] = $valor;
                    //$datos_malos['Bloqueo']['numero_estudiante'] = $valor;
                    //$datos_malos['UltimoLogro']['numero_estudiante'] = $valor;
                    //$datos_malos['InscripcionAlumno']['numero_estudiante'] = $valor;
                    $tablas['Estudiante']['numero_estudiante'] = -1;
                    $tablas['Bloqueo']['numero_estudiante'] = -1;
                    $tablas['UltimoLogro']['numero_estudiante'] = -1;
                    $tablas['InscripcionAlumno']['numero_estudiante'] = -1;
                }
            }

        }
        else if ($key === "DV"){
            if ((is_numeric($valor) || strtoupper($valor) === 'K' ) && (strlen($valor) === 1)) {
                $tablas['Persona']['dv'] = $valor;
            }
            else{
                //$datos_malos['Persona']['dv'] = $valor;
            }
        }

        else if ($key === "Nombres"){ //que awonao sigue haciendo la manera perkin
            if (is_string($valor) && is_string($data[''])){//solo entra si es string
                $valor = $valor . " " . $data['']; 
             }
            else {
                //$datos_malos['Persona']['nombres'] = $valor;
                $valor = null;
            }
            $tablas['Persona']['nombres'] = $valor;
        }

        else if ($key === "Primer Apellido"){
            if (!is_string($valor)){
                //$datos_malos['Persona']['apellido_paterno'] = $valor;
                $valor = null;
            }
            $tablas['Persona']['apellido_paterno'] = $valor;
        }
        else if ($key === "Segundo Apellido"){
            
            if (!is_string($valor)){
                //$datos_malos['Persona']['apellido_materno'] = $valor;
                $valor = null;
            }
            $tablas['Persona']['apellido_materno'] = $valor;
        }

        else if ($key === "Logro"){
            if (!is_string($valor)){
                //$datos_malos['UltimoLogro']['ultimo_logro'] = $valor;
                $valor = null;
            }
            $tablas['UltimoLogro']['ultimo_logro'] = $valor;
        }
        else if ($key === "Fecha Logro"){
            if (!is_string($valor)){
                //$datos_malos['Estudiante']['fecha_logro'] = $valor;
                $valor = null;
            }
            $tablas['Estudiante']['fecha_logro'] = $valor;
        }
        else if ($key === "Última Carga"){
            if (!is_string($valor)){
                //$datos_malos['Estudiante']['ultima_carga'] = $valor;
                $valor = null;
            }
            $tablas['Estudiante']['ultima_carga'] = $valor;
        }
    }
    return $tablas;
}

function limpiar_asignaturas($data){
    $tablas = [
        'Curso' => [],
        'IncluyeCurso' => [],
    ];
    $datos_malos = [
        'Curso' => [],
        'IncluyeCurso' => [],
    ];
    
    foreach ($data as $key => &$valor) {
        if (remove_bom($key) === 'Plan'){
            if (!is_string($valor) || strlen($valor) > 30){
                //$datos_malos['IncluyeCurso']['codigo_plan'] = $valor;
                $valor = 'NULL';
            }
            $tablas['IncluyeCurso']['codigo_plan'] = $valor;
        }
        else if ($key === "Asignatura id"){
            if (!is_string($valor) || strlen($valor) > 10){
                //$datos_malos['Curso']['sigla_curso'] = $valor;
                $valor = '-1';
            }
            $tablas['Curso']['sigla_curso'] = $valor;
            $tablas['IncluyeCurso']['sigla_curso'] = $valor;
        }
        else if ($key === "Asignatura"){
            if (!is_string($valor) || strlen($valor) > 100){
                //$datos_malos['Curso']['nombre_curso'] = $valor;
                $valor = '-1';
            }
            $tablas['Curso']['nombre_curso'] = $valor;
        }
        else if ($key === "Nivel"){
            if (!is_numeric($valor)){
                //$datos_malos['Curso']['nivel'] = $valor;
                $valor = -1;
            }else{
                $valor = (int)$valor;
            }
            $tablas['Curso']['nivel'] = $valor;
        }
        else if ($key === "Prerequisito"){
            if (strlen($valor) !== 1){
                //$datos_malos['Curso']['prerequisito'] = $valor;
                $valor = '-1';
            }
            $tablas['Curso']['prerequisito'] = $valor;
        }
    }
    return $tablas;
}


function limpiar_planes($data){
    // exequiel
    $tablas = [
        'Facultad' => [],
        'PlanEstudio' => []
        ];

    $datos_malos = [
        'Facultad' => [],
        'PlanEstudio' => []
        ];

    foreach ($data as $key => &$valor) {
        if (remove_bom($key) == 'Código Plan') {
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['codigo_plan'] = $valor;
                $valor = "NULL";
            }
            $tablas['PlanEstudio']['codigo_plan'] = $valor;
        }
        elseif ($key == "Facultad"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['Facultad']['nombre_facultad'] = $valor;
                $valor = "NULL";
            }
            $tablas['Facultad']['nombre_facultad'] = $valor;
        }
        elseif ($key == "Carrera"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['nombre_carrera'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['nombre_carrera'] = $valor;
        }
        elseif ($key == "Plan"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['nombre_plan'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['nombre_plan'] = $valor;
        }
        elseif ($key == "Jornada"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['jornada'] = $valor; 
                $valor = NULL;
            }else{
                $valor = strtoupper($valor);
            }
            $tablas['PlanEstudio']['jornada'] = $valor;
        }            
        elseif ($key == "Sede"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['sede'] = $valor; 
                $valor = NULL;
            }
            $tablas['PlanEstudio']['sede'] = $valor;
        } 
        elseif ($key == "Grado"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['grado'] = $valor; 
                $valor = NULL;
            }
            $tablas['PlanEstudio']['grado'] = $valor;
        }
        elseif ($key == "Modalidad"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['modalidad'] = $valor;
                $valor = NULL;
            }
            if($valor === "OnLine"){
                $tablas['PlanEstudio']['modalidad'] = 'OnLine';
            }
        }
        elseif ($key == "Inicio Vigencia"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['inicio'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['inicio'] = $valor;
        } 
    }
    return $tablas;
}

function limpiar_docentes_planificados($data){
    $tablas = [
        'Persona' => [],
        'Profesor' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'Administrativo' => [],
        'Jornada' => [],
    ];
    $datos_malos = [
        'Persona' => [],
        'Profesor' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'Administrativo' => [],
        'Jornada' => [],
    ];
    
    foreach ($data as $key => &$valor){
        if (remove_bom($key) === 'RUN') {
            if (!is_numeric($valor) || $valor ==="" || $valor === null){
                //$datos_malos['Persona']['run'] = $valor;
                //$datos_malos['EmailPersonal']['run'] = $valor;
                //$datos_malos['Telefono']['run'] = $valor;
                //$datos_malos['Jornada']['run'] = $valor;
                //$datos_malos['Telefono']['run'] = $valor;
                //$datos_malos['Administrativo']['run'] = $valor;
                $valor = -1;
            }else{
                $valor = (int)$valor;
                $valor = trim($valor);
            }
            $tablas['Persona']['run'] = $valor;
            $tablas['Profesor']['run'] = $valor;
            $tablas['Jornada']['run'] = $valor;
            $tablas['EmailPersonal']['run'] = $valor;
            $tablas['Telefono']['run'] = $valor;
            $tablas['Administrativo']['run'] = $valor;
        }
        
        elseif ($key === 'Nombre'){
            if (!is_string($valor)){
                //$datos_malos['Persona']['nombres'] = $valor;
                $valor = null;
            }
            $tablas['Persona']['nombres'] = $valor;
        }elseif ($key === 'telefono'){
            if (!is_numeric($valor) || strlen($valor) > 30){
                //$datos_malos['Telefono']['telefono'] = $valor;
                $valor = null;
            }
            else{
                $valor = (int)$valor;
                $valor = trim($valor);
            }
            $tablas['Telefono']['telefono'] = $valor;
        }elseif ($key === 'email personal'){
            if (!is_string($valor)){
                //$datos_malos['EmailPersonal']['email_personal'] = $valor;
                $valor = null;
            }
            $tablas['EmailPersonal']['email_personal'] = $valor;
        }elseif ($key === 'email  institucional'){
            $formato_institucional = '/^.+@lamejor\.cl$/';
            if (!is_string($valor) || !preg_match($formato_institucional, $valor)){
                //$datos_malos['Persona']['email_institucional'] = $valor;
                $valor = null;
            }
            $tablas['Persona']['email_institucional'] = $valor;
        }elseif ($key === 'DEDICACIÓN'){
            if (!is_numeric($valor) || (int)$valor >=40){
                //$datos_malos['Profesor']['dedicacion'] = (int)$valor;
                //$datos_malos['Administrativo']['dedicacion'] = (int)$valor;
                $valor = null;
            }else{
                $valor = (int)$valor;
            }
            $tablas['Profesor']['dedicacion'] = $valor;
            $tablas['Administrativo']['dedicacion'] = $valor;
        }elseif ($key === 'CONTRATO'){
            if (!is_string($valor)){
                //$datos_malos['Administrativo']['contrato'] = $valor;
                //$datos_malos['Profesor']['contrato'] = $valor;
                $valor = 'null';
            }
            $tablas['Profesor']['contrato'] = strtoupper($valor);
            $tablas['Administrativo']['contrato'] = strtoupper($valor);
        }elseif ($key === 'DIURNO'){
            if (trim($valor) === 'diurno'){
                $tablas['Jornada']['jornada_diurna'] = "TRUE";
            }elseif(trim($valor) === 'VESPERTINO'){
                $tablas['Jornada']['jornada_vespertina'] = "TRUE";
            }
            else{
                $tablas['Jornada']['jornada_diurna'] = "FALSE";
                //$datos_malos['Jornada']['jornada_diurna'] = trim($valor);
            }
        }elseif ($key === 'VESPERTINO'){
            if (trim($valor) === 'vespertino'){
                $tablas['Jornada']['jornada_vespertina'] = "TRUE";
            }elseif(trim($valor) === 'diurno'){
                $tablas['Jornada']['jornada_diurna'] = "TRUE";
            }
            else{
                $tablas['Jornada']['jornada_vespertina'] = "FALSE";
                $tablas['Jornada']['jornada_diurna'] = "FALSE";
                //$datos_malos['Jornada']['jornada_vespertina'] = $valor;
            }
        }elseif ($key === 'SEDE'){
            if (!is_string($valor) || strlen($valor) > 30){
                //$datos_malos['Profesor']['sede'] = $valor;
                $valor = null;
            }
            $tablas['Profesor']['sede'] = $valor;
        }elseif ($key === 'CARRERA'){
            if (!is_string($valor) || strlen($valor) > 100){
                //$datos_malos['Profesor']['nombre_carrera'] = $valor;
            }
            $tablas['Profesor']['nombre_carrera'] = $valor;
        }elseif ($key === 'GRADO ACADÉMICO'){
            if (!is_string($valor) || is_numeric($valor)){
                //$datos_malos['Profesor']['grado_academico'] = $valor;
                //$datos_malos['Administrativo']['grado_academico'] = $valor;
                $valor = null;
            }
            $tablas['Profesor']['grado_academico'] = $valor;
            $tablas['Administrativo']['grado_academico'] = $valor;
        }
        elseif ($key === 'JERARQUÍA ') {
            if (!is_string($valor)){
                //$datos_malos['Profesor']['jerarquia_academica'] = $valor;
                $valor = null;
            }
            $tablas['Profesor']['jerarquia_academica'] = $valor;
        }elseif ($key === 'CARGO'){
            if (!is_string($valor)){
                //$datos_malos['Profesor']['cargo'] = $valor;
                $valor = null;
            }
            $tablas['Profesor']['cargo'] = $valor;
        }elseif ($key === 'ESTAMENTO'){
            if (is_string($valor) || strlen($valor) > 30){
                //$datos_malos['Persona']['estamento'] = $valor;
                $valor = null;
            }
            $tablas['Persona']['estamento'] = $valor;
        }
    }
    return $tablas;
}


function limpiar_notas($data){
    $calificacion_enum = ['SO', 'MB', 'B', 'SU', 'I', 'M', 'MM', 'P', 'NP', 'EX', 'A', 'R', 'CV', 'SD', 'SC', 'ES', 'HO', 'DP'];
    // exequiel
    $tablas = [
        'Persona' => [],
        'Estudiante' => [],
        'Curso' => [],
        'PlanEstudio' => [],
        'HistorialAcademico' => [],
        'Telefono' => [],
        'EmailPersonal' => [],
        ];

    $datos_malos = [
        'PlanEstudio' => [],
        'Estudiante' => [],
        'Persona' => [],
        'EmailPersonal' => [],
        'Telefono' => [],
        'HistorialAcademico' => [],
        'Curso' => [],
        ];

    foreach ($data as $key => &$valor){
        if (remove_bom($key) === "Código Plan"){
            if (!is_string($valor)){
                //$datos_malos['PlanEstudio']['codigo_plan'] = $valor;
                $valor = 'NULL';
            }  // las primary key malas no deberian agregarse a $tablas vdd?
            $tablas['PlanEstudio']['codigo_plan'] = $valor;
        }

        elseif ($key === "Plan"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['nombre_plan'] = $valor;
                $valor = NULL;
            }
            $tablas['PlanEstudio']['nombre_plan'] = $valor;
        }

        elseif ($key === "Cohorte"){
            if (!is_string($valor)){
                //$datos_malos['Estudiante']['cohorte'] = $valor;
            }
            $tablas['Estudiante']['cohorte'] = $valor;
        }

        elseif ($key == "Sede"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['sede'] = $valor; 
                $valor = NULL;
            }
            $tablas['PlanEstudio']['sede'] = $valor;
        } 

        elseif ($key === "RUN"){
            if (!is_numeric($valor)){
                //$datos_malos['Estudiante']['run'] = $valor;
                //$datos_malos['Persona']['run'] = $valor;
                //$datos_malos['EmailPersonal']['run'] = $valor;
                //$datos_malos['Telefono']['run'] = $valor;
            }
            elseif (is_numeric($valor)){
                $valor = (int)$valor;
                $tablas['Persona']['run'] = $valor;
                $tablas['Estudiante']['run'] = $valor;
                $tablas['EmailPersonal']['run'] = $valor;
                $tablas['Telefono']['run'] = $valor;
            }
        }

        elseif ($key === "DV") {
            if ((is_numeric($valor) || strtoupper($valor) === 'K') && strlen($valor) === 1) {
                $tablas['Persona']['dv'] = $valor;
            } else {
                //$datos_malos['Persona']['dv'] = $valor;
            }
        }
         
        elseif ($key === "Nombres"){
            if (!is_string($valor)){
                //$datos_malos['Persona']['nombres'] = $valor;
                $valor = NULL;
            }
            $tablas['Persona']['nombres'] = $valor;
        }

        elseif ($key === "Apellido Paterno"){
            if (!is_string($valor)){
                //$datos_malos['Persona']['apellido_paterno'] = $valor;
                $valor = NULL;
            }
            $tablas['Persona']['apellido_paterno'] = $valor;
        }
        elseif ($key === "Apellido Materno"){  
            if (!is_string($valor)){
                //$datos_malos['Persona']['apellido_materno'] = $valor;
                $valor = NULL;
            }
            $tablas['Persona']['apellido_materno'] = $valor;
        }
        
        else if ($key === "Número de alumno"){
            if (is_numeric($valor)){
                $valor = (int)$valor;
                $tablas['Estudiante']['numero_estudiante'] = $valor;
                $tablas['HistorialAcademico']['numero_estudiante'] = $valor;
            }
            else{
                $valor = -1;
                $tablas['Estudiante']['numero_estudiante'] = $valor;
                $tablas['HistorialAcademico']['numero_estudiante'] = $valor;
            }
        }

        elseif ($key === "Periodo Asignatura"){  
            if (!is_string($valor)){
                //$datos_malos['HistorialAcademico']['periodo'] = $valor;
                $valor = NULL;
            }
            $tablas['HistorialAcademico']['periodo'] = $valor;
        }

        elseif ($key === "Código Asignatura"){  
            if (!is_string($valor)){
                //$datos_malos['HistorialAcademico']['sigla_curso'] = $valor;
                //$datos_malos['Curso']['sigla_curso'] = $valor;
                $valor = '-1';
            }
            $tablas['HistorialAcademico']['sigla_curso'] = $valor;
            $tablas['Curso']['sigla_curso'] = $valor;
        }

        elseif ($key === "Convocatoria"){  
            if (!is_string($valor)){
                //$datos_malos['HistorialAcademico']['convocatoria'] = $valor;
                $valor = NULL;
            }
            $tablas['HistorialAcademico']['convocatoria'] = $valor;
        }

        elseif ($key === "Calificación"){  
            if (!is_string($valor) || $valor){
                //$datos_malos['HistorialAcademico']['calificacion'] = $valor;
                $valor = NULL;
            }elseif (!in_array($valor, $calificacion_enum)){
                $tablas['HistorialAcademico']['calificacion'] = $valor;
            }
        }

        elseif ($key === "Nota"){  
            if (!is_numeric($valor)){
                //$datos_malos['HistorialAcademico']['nota'] = $valor;
                $valor = -1;
            }
            $valor = (float) $valor;
            $tablas['HistorialAcademico']['nota'] = $valor;
        }
    }
    return $tablas;
}

function limpiar_planeacion($data){
        // exequiel
        $tablas = [
            'Persona' => [],
            'Curso' => [],
            'Departamento' => [],
            'Salas' => [],
            'Facultad' =>[],
            'OfertaAcademica' => []
        ];
        
        $datos_malos = [
            'Persona' => [],
            'Curso' => [],
            'Departamento' => [],
            'Salas' => [],
            'Facultad' =>[],
            'OfertaAcademica' => []
        ];
        
        
        foreach ($data as $key => &$valor) {
            if (remove_bom($key) === "Periodo"){  
                if (!is_string($valor)){
                    //$datos_malos['Curso']['periodo'] = $valor;
                    //$datos_malos['OfertaAcademica']['periodo'] = $valor;
                    //$datos_malos['Salas']['periodo'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['periodo'] = $valor;
            }
            
            elseif ($key == "Sede"){
                if (!is_string($valor) || empty($valor)) {
                    $valor = "-1";
                }
                $tablas['OfertaAcademica']['sede'] = $valor;
            } 
            
            elseif ($key == "Facultad  "){
                if (!is_string($valor) || empty($valor)) {
                    //$datos_malos['OfertaAcademica']['nombre_facultad'] = $valor;
                    $valor = 'NULL';
                }
                $tablas['Facultad']['nombre_facultad'] = $valor;
                $tablas['OfertaAcademica']['nombre_facultad'] = $valor;
            }
            
            elseif ($key === "Código Depto"){
                if (!is_numeric($valor)){
                    //$datos_malos['Departamento']['codigo_departamento'] = $valor;
                    $valor = -1;
                }
                $valor = (int) $valor;
                $tablas['Facultad']['codigo_departamento'] = $valor;
                $tablas['Departamento']['codigo_departamento'] = $valor;
            }
            
            elseif ($key === "Departamento"){
                if (!is_string($valor)){
                    //$datos_malos['Departamento']['nombre'] = $valor;
                    $valor = NULL;
                }
                $tablas['Departamento']['nombre'] = $valor;
            }
            
            elseif ($key === "Id Asignatura"){
                if (!is_string($valor) || empty($valor)){
                    //$datos_malos['Curso']['sigla_curso'] = $valor;
                    //$datos_malos['OfertaAcademica']['sigla_curso'] = $valor;
                    $valor = "-1";
                }
                $tablas['Curso']['sigla_curso'] = $valor;
                $tablas['OfertaAcademica']['sigla_curso'] = $valor;
            }
            
            elseif ($key === "Asignatura"){
                if (!is_string($valor)){
                    //$datos_malos['Curso']['nombre_curso'] = $valor;
                    $valor = NULL;
                }
                $tablas['Curso']['nombre_curso'] = $valor;
            }
            
            elseif ($key == "Sección"){ // CONVERSAR DISCORD
                if (!is_numeric($valor)){
                    //$datos_malos['OfertaAcademica']['seccion_curso'] = $valor; 
                    $valor = NULL;
                }
                $valor = (int) $valor;
                $tablas['OfertaAcademica']['seccion_curso'] = $valor;
            } 
            
            elseif ($key === "Duración"){
                if (!is_string($valor)){
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['duracion'] = $valor;
            }
            
            elseif ($key === "Jornada"){
                if (is_string($valor)) {
                    $tablas['OfertaAcademica']['jornada'] = $valor;
                }
            }
            elseif ($key === "Cupo"){ // VACANTES SOLO ESTÁ EN SALAS 
                if (!is_numeric($valor)){
                    //$datos_malos['Salas']['vacantes'] = $valor;
                }
                $valor = (int) $valor;
                $tablas['Salas']['vacantes'] = $valor;
            }
            
            elseif ($key === "Inscrito"){  
                if (!is_numeric($valor)){
                    //$datos_malos['OfertaAcademica']['inscritos'] = $valor;                
                }
                $valor = (int) $valor;
                $tablas['OfertaAcademica']['inscritos'] = $valor;
            }
            
            elseif ($key === "Día"){
                if (!is_string($valor)){
                    //$datos_malos['OfertaAcademica']['dia'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['dia'] = $valor;
            }
            
            elseif ($key == "Hora Inicio"){
                if (es_hora_valida($valor) == false || empty($valor)) {
                    //$datos_malos['OfertaAcademica']['hora_inicio'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['hora_inicio'] = $valor;
            } 
            
            elseif ($key == "Hora Fin"){
                if (es_hora_valida($valor) == false || empty($valor)) {
                    //$datos_malos['OfertaAcademica']['hora_fin'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['horario_fin'] = $valor;
            } 
            
            elseif ($key == "Fecha Inicio"){
                if (empty($valor)) {
                    //$datos_malos['OfertaAcademica']['fecha_inicio'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['fecha_inicio'] = $valor;
            } 

            elseif ($key == "Fecha Fin"){
                if (empty($valor)) {
                    //$datos_malos['OfertaAcademica']['fecha_fin'] = $valor;
                    $valor = NULL;
                }
                $tablas['OfertaAcademica']['fecha_fin'] = $valor;
            } 
            
            elseif ($key === "Lugar"){
                if (!is_string($valor) || empty($valor)) {
                    //$datos_malos['Salas']['sala'] = $valor;
                    $valor = "POR DEFINIR";
                }
                $tablas['Salas']['sala'] = $valor;
            }
            
            elseif ($key === "Edificio"){
                if (!is_string($valor)){
                    //$datos_malos['Salas']['edificio'] = $valor;
                    $valor = NULL;
                }
                $tablas['Salas']['edificio'] = $valor;
            }
            
            elseif ($key === "Profesor Principal"){
                if (strtoupper($valor) == 'S'){
                    $tablas['OfertaAcademica']['profesor_principal'] = "TRUE";
                }else{
                    $tablas['OfertaAcademica']['profesor_principal'] = "FALSE";
                }
            }  
            elseif ($key === "RUN"){
                $codigos_departamento = array_column($tablas['Departamento'], 'codigo_departamento');
                if (in_array($valor, $tablas['Departamento'])) {
                    $valor = -1;
                }
                if (!is_numeric($valor)){
                    $valor = -1;
                }
                $valor = (int)$valor;
                $tablas['Persona']['run'] = $valor;
                $tablas['Profesor']['run'] = $valor;
                $tablas['EmailPersonal']['run'] = $valor;
                $tablas['Telefono']['run'] = $valor;
                $tablas['Administrativo']['run'] = $valor;
            }
            /*
            elseif ($key === "Nombre Docente"){
                if (!is_string($valor)){
                    //$datos_malos['Persona']['nombres'] = $valor;
                    $valor = NULL;
                }
                $tablas['Persona']['nombres'] = $valor;
            }
    
            elseif ($key === "1er Apellido Docente"){
                if (!is_string($valor)){
                    //$datos_malos['Persona']['apellido_paterno'] = $valor;
                    $valor = NULL;
                }
                $tablas['Persona']['apellido_paterno'] = $valor;
            }
            
            elseif ($key === "2so Apellido Docente"){  
                if (!is_string($valor)){
                    //$datos_malos['Persona']['apellido_materno'] = $valor;
                    $valor = NULL;
                }
                $tablas['Persona']['apellido_materno'] = $valor;
            }

            elseif ($key === "Jerarquización"){  
                if (!is_string($valor)){
                    //$datos_malos['Profesor']['jerarquia_academica'] = $valor;
                    $valor = NULL;
                }
                $tablas['Profesor']['jerarquia_academica'] = $valor;
            }
            */
    }
    return $tablas;
}

function limpiar_prerequisitos($data){
    // exequiel
    $tablas = [
        'CursoPrerequisito' => []
        ];

    $datos_malos = [
        'CursoPrerequisito' => []
        ];
    $contador = 0;
    foreach ($data as $key => &$valor) {
        if (remove_bom($key) == 'Plan') {
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['codigo_plan'] = $valor;
                $valor = "NULL";
            }
            $tablas['CursoPrerequisito']['codigo_plan'] = $valor;
        }
        elseif ($key == "Asignatura id"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['PlanEstudio']['sigla_curso'] = $valor;
                //$datos_malos['CursoPrerequisito']['sigla_curso'] = $valor;
                //$datos_malos['Curso']['sigla_curso'] = $valor;
                $valor = "-1";
            }
            $tablas['CursoPrerequisito']['sigla_curso'] = $valor;
        }
        elseif ($key == "Asignatura"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['Curso']['nombre_curso'] = $valor;
                $valor = "NULL";
            }
            $tablas['CursoPrerequisito']['nombre_curso'] = $valor;
        }
        elseif ($key == "Nivel"){
            if (!is_string($valor) || empty($valor)) {
                //$datos_malos['Curso']['nivel'] = $valor;
                $valor = NULL;
            }
            $tablas['CursoPrerequisito']['nivel'] = (int)$valor;
        }

        elseif ($key == "Prerequisitos") {
            if ($contador == 0) { 
                if (!is_numeric($valor) || empty($valor)) {
                    //$datos_malos['CursoPrerequisito']['prerequisito_1'] = $valor; 
                    $valor = "-1";
                }
                $tablas['CursoPrerequisito']['prerequisito_1'] = $valor;
                $contador ++;
            } else {
                if (!is_numeric($valor) || empty($valor)) {
                    //$datos_malos['CursoPrerequisito']['prerequisito_2'] = $valor; 
                    $valor = "-1";
                }
                $tablas['CursoPrerequisito']['prerequisito_2'] = $valor;
                $contador = 0;
            }   
        }   
    }
    return $tablas;
}
?>