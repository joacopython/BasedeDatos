<?php

function limpiarCSV($ruta, $salida) {
    $valores_no_nulos = [
        'Cohorte' => 'X',
        'Código Plan' => 'X',
        'Plan' => 'X',
        'Bloqueo' => 'false',
        'RUN' => '-1',
        'DV' => 'X',
        'Nombres' => 'X',
        'Apellido Paterno' => 'X',
        "PATERNO" => 'X',
        "MATERNO" => 'X',
        'Nombre completo' => 'X',
        'Número de estudiante' => '-1',
        'Periodo curso' => 'X',
        'sigla curso' => 'X',
        'curso' => 'X',
        'Sección' => 'X',
        'Nivel del curso' => 'X',
        'Calificación' => 'X',
        'Ultimo logro' => 'X',
        'Fecha Logro' => 'X',
        'Ultima toma de ramos' => 'X'
    ];
    $niveles = ["INGRESO", "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "LICENCIAURA"];
    $no_espacio_mid = ["Mail Personal", "Mail Institucional", "MAIL PERSONAL", "MAIL INSTITUCIONAL"];

    $archivo_datos = fopen($ruta, "r");
    $outer = fopen($salida, "w");
    $header = fgetcsv($archivo_datos, 1000,";");
    if (substr($header[0], 0, 3) === "\xEF\xBB\xBF") {
        $header[0] = substr($header[0], 3);
    }
    foreach ($header as &$valor) {
        $valor = trim($valor);
    }
    
    fputcsv($outer, $header, ";");
    
    while (($linea = fgetcsv($archivo_datos, 1000,";")) !== false){
        $diccdata = array_combine($header, $linea);
        $valido = true;
        foreach ($diccdata as $key => &$valor) {
            if ($valor === "" && array_key_exists($key, $valores_no_nulos)) {
                $valor = $valores_no_nulos[$key];
            }
            $valor = trim($valor);

            if (in_array($key, $no_espacio_mid, true)){
                $valor = str_replace(" ", "", $valor);
            }
            else{
                $valor = preg_replace("/\s+/", " ", $valor);

            }
            if ($key === "RUN"){
                if (!is_numeric($valor) || $valor === "-1"){
                    $valido = true;
                }
            }
            if ($key === "Nota"){
                if (!is_numeric($valor) || (float)$valor < 1.0 || (float)$valor > 7.0){
                    $valor = "";
                }
                else{
                    $valor = strval((float)$valor);
                }
            }
            if ($key === "Último Logro"){
                if (!in_array($valor, $niveles, true)){
                    $valor = "";
                }
            }
        }

        if ($valido){
            fputcsv($outer, $diccdata, ";");
        }
    }
    fclose($archivo_datos);
    fclose($outer);
    return;
}


//Las siguientes funciones son pensadas para datos limpios

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


function eliminar_repetidos($array_repetidos) {
    $array_limpio = [];
    foreach ($array_repetidos as $linea) {
        if (!in_array($linea, $array_limpio, true)) { 
            $array_limpio[] = $linea;
        }
    }
    return $array_limpio;
}

function change_keys($matriz, $nuevas_claves) {
    $new_matriz = [];
    foreach ($matriz as $fila) {
        $nueva_fila = [];
        foreach ($nuevas_claves as $indice => $valor) 
        {
            $origin = array_keys($fila)[$indice];
            $nueva_fila[$valor] = $fila[$origin];
        }
        $new_matriz[] = $nueva_fila;
    }
    return $new_matriz;
}
function nombre_completo($matriz) {
    $new_matriz = [];
    foreach ($matriz as $fila) {
        $completo = "";
        $new_fila = $fila;
        if ($fila["NOMBRES:"] !== "X") {
            $completo .= $fila["NOMBRES:"] . " ";
        }

        if ($fila["PATERNO"] !== "X") {
            $completo .= $fila["PATERNO"] . " ";
        }

        if ($fila["MATERNO"] !== "X") {
            $completo .= $fila["MATERNO"];
        }

        $new_fila["Nombre completo"] = trim($completo);
        $new_matriz[] = $new_fila;
    }
    return $new_matriz;
}
function jornada_Seter($matriz) {
    $new_matriz = [];
    foreach ($matriz as $fila) {
        if ($fila["JORNADA DIURNO"] === "" && $fila["JORNADA VESPERTINO"] !== "") {
            $fila["Jornada"] = "Vespertino";
        }
        elseif ($fila["JORNADA DIURNO"] === "" && $fila["JORNADA VESPERTINO"] === "") {
            $fila["Jornada"] = "";
        }
        elseif ($fila["JORNADA DIURNO"] !== "" && $fila["JORNADA VESPERTINO"] === "") {
            $fila["Jornada"] = "Diurno";
        }
        $new_matriz[] = $fila;
    }
    return $new_matriz;
}
function proyection($matriz, $columnas, $strict) {
    $proyeccion = [];
    foreach ($matriz as $fila) {
        $new_fila = [];
        foreach ($columnas as $columna) {
            if (isset($fila[$columna])) {
                $new_fila[$columna] = $fila[$columna];
            }
            else if (!$strict) {
                $new_fila[$columna] = "";
            }
        }
        $proyeccion[] = $new_fila;
    }
    return eliminar_repetidos($proyeccion);
}
function proyection_2($fila, $columnas, $strict) {
    $new_fila = [];
    foreach ($columnas as $columna) {
        if (isset($fila[$columna])) {
            $new_fila[$columna] = $fila[$columna];
        }
        else if (!$strict) {
            $new_fila[$columna] = "";
        }
    }
    return $new_fila;
}


function leer_CSV($ruta, $columnas) {
    $archivo_datos = fopen($ruta, "r");
    $header = fgetcsv($archivo_datos, 1000,";");
    $array_datos = [];
    while (($linea = fgetcsv($archivo_datos, 1000,";")) !== false){
        $diccdata = array_combine($header, $linea);
        $diccdata = proyection_2($diccdata, $columnas, false);
        foreach ($diccdata as $key => &$valor) {
            $valor = typeConvert($valor);
        }
        if (!in_array($diccdata, $array_datos)) { 
            $array_datos[] = $diccdata;
        }
    }
    fclose($archivo_datos);
    return $array_datos;
}
function lista_decurso($notas, $estudiantes, $cursos) {
    $curso_str = readline("Ingrese la sigla del curso: ");
    $periodo_str = readline("Ingrese el periodo: ");
    $notas_filtradas = array_filter($notas, function ($nota) use ($curso_str, $periodo_str) {
        return $nota["Sigla curso"] === $curso_str && $nota["Periodo curso"] === $periodo_str;
    });
    if (empty($notas_filtradas)) {
        echo "No se encontro un estudiante\n";
        return false;
    }
    $numestudiantes_curso = [];
    foreach ($notas_filtradas as $nota) {
        $numestudiantes_curso[] = $nota["Número estudiante"];
    }
    if (empty($numestudiantes_curso)) {
        echo "No se encontro un estudiante\n";
        return false;
    }
    $numestudiantes_curso = array_unique($numestudiantes_curso);
    foreach ($estudiantes as $estudiante) {
        if (in_array($estudiante["Número estudiante"], $numestudiantes_curso,true)){
            echo "Cohorte: {$estudiante["Cohorte"]} | Nombre Completo: {$estudiante["Nombre Completo"]} | RUN: {$estudiante["RUN"]}-{$estudiante["DV"]} | Número estudiante: {$estudiante["Número estudiante"]}\n";
        }
    }
    return true;
}
function carga_acdemica($notas, $estudiantes, $cursos) {
    $answer = readline("Ingrese el RUN del estudiante: ");
    if (!preg_match('/^\d+-[\dkK]$/', $answer)) {
        echo "Porfavor igreasar RUN valido ejemplo 12345678-9;\n";
        return false;
    }
    list($run, $digitov) = explode("-", $answer);
    $run = (int)$run;
    if (is_numeric($digitov)){
        $digitov = (int)$digitov;
    }
    

    $estudiante = array_filter($estudiantes, function($estudiante) use ($run, $digitov) {
        return $estudiante["RUN"] == $run && $estudiante["DV"] == $digitov;
    });

    if (empty($estudiante)) {
        echo "No se encontro un estudiante\n";
        return false;
    }
    
    $numero_estudiante = reset($estudiante)["Número estudiante"];

    $notas_estudiante = array_filter($notas, function ($nota) use ($numero_estudiante) {
        return $nota["Número estudiante"] === $numero_estudiante;
    });

    if (empty($notas_estudiante)) {
        echo "No se encontro notas para el estudiante\n";
        return false;
    }
    $total = 0;
    $cantcursos = 0;
    $periodos = [];
    foreach ($notas_estudiante as $nota) {
        $info_curso = array_filter($cursos, function ($curso) use ($nota) {
            return $curso["Sigla curso"] === $nota["Sigla curso"];
        });
        if (!empty($info_curso)) {
            $info_curso = reset($info_curso)["curso"];
            echo "Periodo: {$nota["Periodo curso"]} | Sigla: {$nota["Sigla curso"]} | Curso: {$info_curso} | Nota: {$nota["Nota"]} | Calificación: {$nota["Calificación"]}\n";
        }
        if (is_numeric($nota["Nota"])) {
            $periodos[$nota["Periodo curso"]][] = $nota["Nota"];
            $total += $nota["Nota"];
            $cantcursos++;
        }
    }
    foreach ($periodos as $perioso => $notas_periodo) {
        $promedio_sem = (array_sum($notas_periodo) / count($notas_periodo));
        echo "PPS del periodo {$perioso}: {$promedio_sem}\n";
    }
    if ($cantcursos !== 0) {
        $promedio_a = $total / $cantcursos;
        echo "PPA: {$promedio_a}\n";
    }
    else {
        echo "No se encontraron cursos\n";
    }
    return true;
}
?>