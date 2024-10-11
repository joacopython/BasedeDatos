<?php

function crear_matriz_persona(){
    $file = fopen('archivo1_filtrado.csv', 'r');

    // Array para almacenar la información procesada
    $personas = [];

    // Leer la primera línea (encabezados)
    $headers = fgetcsv($file, 0, ';');

    // Procesar cada línea del CSV
    while (($data = fgetcsv($file, 0, ';')) !== FALSE) {
        // Extraer la información relevante
        $run = $data[4];
        $dv = $data[5];
        $nombres = $data[6];
        $apellido_paterno = $data[7];
        $apellido_materno = $data[8];
        $nombre_completo = $data[9];
        $mail_personal = $data[11];
        $mail_institucional = $data[12];

        // Crear un array con los datos de la persona
        $persona = [
            'RUN' => $run,
            'DV' => $dv,
            'Nombres' => $nombres,
            'Apellido Paterno' => $apellido_paterno,
            'Apellido Materno' => $apellido_materno,
            'Nombre Completo' => $nombre_completo,
            'Mail Personal' => $mail_personal,
            'Mail Institucional' => $mail_institucional
        ];

        // Verificar si la persona ya está en el array
        $persona_existente = false;
        foreach ($personas as $p) {
            if ($p['RUN'] == $run && $p['DV'] == $dv) {
                $persona_existente = true;
                break;
            }
        }

        // Si la persona no existe, añadirla al array
        if (!$persona_existente) {
            $personas[] = $persona;
        }
    }

    // Cerrar el archivo CSV
    fclose($file);

    $file_2 = fopen('archivo2_filtrado.csv', 'r');

    // Leer la primera línea (encabezados)
    $headers = fgetcsv($file_2, 0, ';');

    // Procesar cada línea del CSV
    while (($data = fgetcsv($file_2, 0, ';')) !== FALSE) {
        // Extraer la información relevante
        $run = $data[0];
        $dv = $data[1];
        $nombres = $data[4];
        $apellido_paterno = $data[2];
        $apellido_materno = $data[3];
        $nombre_completo = $nombres . $apellido_paterno . $apellido_materno;
        $mail_personal = $data[5];
        $mail_institucional = $data[6];

        // Crear un array con los datos de la persona
        $persona = [
            'RUN' => $run,
            'DV' => $dv,
            'Nombres' => $nombres,
            'Apellido Paterno' => $apellido_paterno,
            'Apellido Materno' => $apellido_materno,
            'Nombre Completo' => $nombre_completo,
            'Mail Personal' => $mail_personal,
            'Mail Institucional' => $mail_institucional
        ];

        // Verificar si la persona ya está en el array
        $persona_existente = false;
        foreach ($personas as $p) {
            if ($p['RUN'] == $run && $p['DV'] == $dv) {
                $persona_existente = true;
                break;
            }
        }

        // Si la persona no existe, añadirla al array
        if (!$persona_existente) {
            $personas[] = $persona;
        }
    }

    // Cerrar el archivo CSV
    fclose($file_2);
    return $personas;
    // Mostrar el resultado (puedes comentarlo o eliminarlo si no es necesario)
}

function crear_matriz_estudiante(){
    $file = fopen('archivo1_filtrado.csv', 'r');

    // Array para almacenar la información procesada
    $estudiantes = [];

    // Leer la primera línea (encabezados)
    $headers = fgetcsv($file, 0, ';');

    // Procesar cada línea del CSV
    while (($data = fgetcsv($file, 0, ';')) !== FALSE) {
        // Extraer la información relevante
        $run = $data[4];
        $dv = $data[5];
        $cohorte = $data[0];
        $codigo_plan = $data[1];
        $numero_estudiante = $data[10];
        $ultimo_logro = $data[20];
        $fecha_logro = $data[21];
        $ultima_toma_de_ramo = $data[22];

        // Crear un array con los datos de la persona
        $estudiante = [
            'RUN' => $run,
            'DV' => $dv,
            'Número Estudiante' => $numero_estudiante,
            'Cohorte' => $cohorte,
            'Código Plan' => $codigo_plan,
            'ùltimo Logro' => $ultimo_logro,
            'Fecha Logro' => $fecha_logro,
            'Ultima Carga' => $ultima_toma_de_ramo,
        ];

        // Verificar si la persona ya está en el array
        $persona_existente = false;
        foreach ($estudiantes as $p) {
            if (($p['RUN'] == $run && $p['DV'] == $dv)||($p["Número Estudiante"] == $numero_estudiante)) {
                $persona_existente = true;
                break;
            }
        }

        // Si la persona no existe, añadirla al array
        if (!$persona_existente) {
            $estudiantes[] = $estudiante;
        }
    }

    // Cerrar el archivo CSV
    fclose($file);
    return $estudiantes;
}

function crear_matriz_profesor(){
    $file = fopen('archivo2_filtrado.csv', 'r');

    // Array para almacenar la información procesada
    $profesores = [];
    $administrativos = [];

    // Leer la primera línea (encabezados)
    $headers = fgetcsv($file, 0, ';');

    // Procesar cada línea del CSV
    while (($data = fgetcsv($file, 0, ';')) !== FALSE) {
        // Extraer la información relevante
        $run = $data[0];
        $dv = $data[1];
        $contrato = $data[8];
        if ($data[10] == ""){
            $jornada = $data[9];
        }else{
            $jornada = $data[10];
        }
        $dedicacion = $data[11];
        $grado_academico = $data[12];
        $jerarquia = $data[13];
        $cargo = $data[14];

        // Crear un array con los datos de la persona
        $profesor = [
            'RUN' => $run,
            'DV' => $dv,
            'Contrato' => $contrato,
            'Jornada' => $jornada,
            'Dedicación' => $dedicacion,
            'Grado Academico' => $grado_academico,
            'Jerarquia' => $jerarquia,
            'Cargo' => $cargo,
        ];
        $administrativo = [
            'RUN' => $run,
            'DV' => $dv,
            'Cargo' => $cargo,
        ];

        // Verificar si la persona ya está en el array
        $persona_existente = false;
        if ((strtoupper(substr($jerarquia, 0, 2)) == "PR")||(strtoupper(substr($cargo, 0, 1)) == "PR")){

            foreach ($profesores as $p) {
                if (($p['RUN'] == $run && $p['DV'] == $dv)) {
                    $persona_existente = true;
                    break;
                }
            }
            // Si la persona no existe, añadirla al array
            if (!$persona_existente) {
                $profesores[] = $profesor;
            }
        }else{
            foreach ($administrativos as $p) {
                if (($p['RUN'] == $run && $p['DV'] == $dv)) {
                    $persona_existente = true;
                    break;
                }
            }
            // Si la persona no existe, añadirla al array
            if (!$persona_existente) {
                $administrativos[] = $administrativo;
            }
        }
    }
    // Cerrar el archivo CSV
    fclose($file);
    $datos = [$profesores, $administrativos];
    return $datos;
}

function crear_matriz_cursos(){
    $file = fopen('archivo1_filtrado.csv', 'r');

    // Array para almacenar la información procesada
    $cursos = [];

    // Leer la primera línea (encabezados)
    
    $headers = fgetcsv($file, 0, ';');
   
    // Procesar cada línea del CSV
    while (($data = fgetcsv($file, 0, ';')) !== FALSE) {
        // Extraer la información relevante
        $sigla = $data[14];
        $nombre = $data[15];
        $seccion = $data[16];
        $nivel = $data[17];
        $persona_existente = false;
        foreach ($cursos as &$p){
            if ($p['Sigla Curso'] == $sigla && $p['Nivel'] == $nivel) {
                $persona_existente = true;
                if (intval($p['Secciones']) < intval($seccion)){
                    $p['Secciones'] = $seccion;
                }
                break;
            }
        }
        // Si la persona no existe, añadirla al array
        if (!$persona_existente) {
            $estudiante = [
                'Sigla Curso' => $sigla,
                'Curso' => $nombre,
                'Nivel' => $nivel,
                'Secciones' => $seccion,
            ];
            $cursos[] = $estudiante;
        }
    }
    // Cerrar el archivo CSV
    fclose($file);
    return $cursos;
}

function crear_matriz_notas(){
    $file = fopen('archivo1_filtrado.csv', 'r');

    // Array para almacenar la información procesada
    $estudiantes = [];

    // Leer la primera línea (encabezados)
    $headers = fgetcsv($file, 0, ';');

    // Procesar cada línea del CSV
    while (($data = fgetcsv($file, 0, ';')) !== FALSE) {
        // Extraer la información relevante
        $numero_estudiante = $data[10];
        $sigla_curso = $data[14];
        $periodo = $data[13];
        $calificacion = $data[18];
        $nota = $data[19];

        // Crear un array con los datos de la persona
        $estudiante = [
            'Número Estudiante' => $numero_estudiante,
            'Sigla Curso' => $sigla_curso,
            'Periodo' => $periodo,
            'Calificación' => $calificacion,
            'Nota' => $nota,
        ];

        // Verificar si la persona ya está en el array
        $persona_existente = false;
        foreach ($estudiantes as $p) {
            if (($p['Periodo'] == $periodo && $p['Sigla Curso'] == $dv && $p["Número Estudiante"] == $numero_estudiante)) {
                $persona_existente = true;
                break;
            }
        }

        // Si la persona no existe, añadirla al array
        if (!$persona_existente) {
            $notas[] = $estudiante;
        }
    }

    // Cerrar el archivo CSV
    fclose($file);
    return $notas;
}
?>