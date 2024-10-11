<?php

function ingresar_por_rut($RUT, $personas){
    $persona = [];
    list($RUN, $DV) = explode('-', $RUT);
    foreach ($personas as $p) {
        if ($p['RUN'] == $RUN && $p['DV'] == $DV) {
            $persona[] = $p;
        }
    }
    return $persona;
}

function ingresar($persona){
    $red = "\033[31m";
    $green = "\033[32m";
    $yellow = "\033[33m";
    $bold = "\033[1m";
    $reset = "\033[0m";
    $seguir = True;
    while ($seguir = True){
        echo "{$bold}{$red}[1]{$reset}   Carga Academica Acumulada\n";
        echo "{$bold}{$red}[2]{$reset}   Lista de Curso\n";
        echo "{$bold}{$red}[3]{$reset}   Cerrar sección\n";
        echo "{$bold}{$green}Su opción:{$red}";
        $elecciòn = trim(fgets(STDIN));
        echo $reset;
        if ($elecciòn == "1"){
            carga_academica($persona);
            cerrar_secion();
        }elseif($elecciòn == "2"){
            lista_cursos() ;
            cerrar_secion();
        }elseif($elecciòn == "3"){
            echo "\nHasta luego {$persona['Nombres']}\n";
            return null;
        }else{
            echo "\nPorfavor ingrese una opción valida (1, 2 o 3)\n";
        }
    }
}

function lista_cursos(){
    $red = "\033[31m";
    $green = "\033[32m";
    $yellow = "\033[33m";
    $bold = "\033[1m";
    $reset = "\033[0m";
    while(True){
        echo "\nIngrese la sigla del curso que desea consultar:{$bold}{$red} ";
        $sigla_curso = trim(fgets(STDIN));
        echo $reset;
        $lista_curso = buscar_personas_curso($sigla_curso);
        if ($lista_curso == []){
            echo "\n La Sigla {$bold}{$red}{$sigla_curso}{$reset} es invalida\n";
        }else{
            generar_lista_curso($lista_curso);
            return null;
        }
    }
}

function generar_lista_curso($lista_curso){
    $red = "\033[31m";
    $green = "\033[32m";
    $yellow = "\033[33m";
    $bold = "\033[1m";
    $reset = "\033[0m";
    $estudiantes = crear_matriz_estudiante();
    $personas = crear_matriz_persona();
    echo "\n{$bold}{$red}--------------------------";
    echo "{$bold}{$green}LISTA DE CURSO{$bold}{$red}";
    echo "--------------------------------{$reset}\n";
    foreach ($lista_curso as $periodo){
        echo "\n\n{$bold}{$green}Periodo: {$periodo[0]['Periodo']}{$reset}";
        foreach($periodo as $estudiante){
            $info = encontrar_info_estudiante($estudiante['Número Estudiante'], $personas, $estudiantes);
            echo "\n Cohorte: {$info['Cohorte']} | Nombre Completo: {$info['Nombre Completo']} | Run: {$info['RUN']} | Número estudiante: {$info['Número Estudiante']}";
        }
    }
    echo "\n{$bold}{$red}--------------------------------------------------------";
    echo "------------------------{$reset}\n";
}

function encontrar_info_estudiante($numero_estudiante, $personas, $estudiantes){
    foreach ($estudiantes as $estudiante){
        if ($estudiante['Número Estudiante'] == $numero_estudiante){
            $run = $estudiante['RUN'];
            $dv = $estudiante["DV"];
            $cohorte = $estudiante['Cohorte'];
            foreach ($personas as $persona){
                if ($persona['RUN'] == $run && $persona['DV'] == $dv){
                    $info = [
                        'Cohorte' => $cohorte,
                        'Nombre Completo' => $persona['Nombre Completo'],
                        'RUN' => $run,
                        'Número Estudiante' => $numero_estudiante,
                    ];
                    return $info;
                }
            }
        }
    }
}

function buscar_personas_curso($sigla_curso){

    $cursos_separados = [];
    $cursos = crear_matriz_notas();
    
    foreach ($cursos as $estudiante) {
        $periodo = $estudiante['Periodo'];
        if ($estudiante['Sigla Curso'] == $sigla_curso){
            if (!isset($cursos_separados[$periodo])) {
                $cursos_separados[$periodo] = [];
            }
            if (verificar_repetido($cursos_separados[$periodo], $estudiante['Número Estudiante'])){
                $cursos_separados[$periodo][] = [
                    'Número Estudiante' => $estudiante['Número Estudiante'],
                    'Periodo' => $periodo,
                ];
            }
        }
    }
    ksort($cursos_separados);
    return $cursos_separados;
}

function verificar_repetido($notas_periodo, $numero_estudiante){
    foreach ($notas_periodo as $n){
        if ($n['Número Estudiante'] == $numero_estudiante){
            return False;
        }
    }
    return True;
}

function carga_academica($persona){
    $red = "\033[31m";
    $green = "\033[32m";
    $yellow = "\033[33m";
    $bold = "\033[1m";
    $reset = "\033[0m";
    $estudiante = encontrar_estudiante($persona);
    if ($estudiante == "NO"){
        echo "\nVaya... Parece que no estas registrado como un estudiante\n";
    }else{
        $numero_estudiante = $estudiante['Número Estudiante'];
        $notas = encontrar_cursos($numero_estudiante);
        $cursos_por_periodo = separar_por_periodo($notas);
        generar_carga_academica($cursos_por_periodo);
    }
}

function generar_carga_academica($cursos_por_periodo){
    $red = "\033[31m";
    $green = "\033[32m";
    $yellow = "\033[33m";
    $bold = "\033[1m";
    $reset = "\033[0m";
    $cursos = crear_matriz_cursos();
    echo "\n{$bold}{$red}--------------------------";
    echo "{$bold}{$green}CARGA ACADEMICA ACUMULADA{$bold}{$red}";
    echo "----------------------------{$reset}\n";
    $suma_promedios = 0.0;
    $contador_promedios = 0;
    foreach($cursos_por_periodo as $c){
        $suma_notas = 0.0;
        $contador = 0;
        echo "\n{$bold}{$green}Periodo: {$c[0]['Periodo']}{$reset}";
        foreach($c as $notas){
            $nota = $notas['Nota'];
            $nota_valor = (float)str_replace(',', '.', $notas['Nota']);
            foreach($cursos as $cu){
                if ($notas['Sigla Curso'] == $cu['Sigla Curso']){
                    $nombre_curso = $cu["Curso"]; 
                }
            }
            $nombre_curso_alineado = str_pad($nombre_curso, 60);
            echo "\n {$nombre_curso_alineado}: {$nota}";
            if ($notas['Nota'] != ""){
                $suma_notas = $suma_notas + $nota_valor;
                $contador ++;
            }
        }
        if ($contador != 0){
            $promedio = $suma_notas / $contador;
            $suma_promedios = $suma_promedios + $promedio;
            $contador_promedios ++;
            $promedio_aprox = number_format($promedio, 1);
            $nombre_curso_alineado = str_pad("PROMEDIO PERIODO", 60);
            echo "\n {$nombre_curso_alineado}: {$promedio_aprox}";
        }else{
            $nombre_curso_alineado = str_pad("PROMEDIO PERIODO", 60);
            echo "\n {$nombre_curso_alineado}: NO DISPO";
        }
    }
    if ($contador_promedios !=0){
        $nombre_curso_alineado = str_pad("PROMEDIO FINAL", 61);
        $promedio_final = round($suma_promedios/$contador_promedios,1);
        echo "\n\n{$bold}{$green}{$nombre_curso_alineado}: {$promedio_final}{$reset}\n";
        echo "\n{$bold}{$red}--------------------------------------------------------";
        echo "------------------------{$reset}\n";
    }else{
        $nombre_curso_alineado = str_pad("PROMEDIO FINAL", 61);
        echo "\n\n{$bold}{$green}{$nombre_curso_alineado}: No Dispo{$reset}\n";
        echo "\n{$bold}{$red}--------------------------------------------------------";
        echo "------------------------{$reset}\n";
    }
}

function separar_por_periodo($notas){

    $cursos_separados = [];
    
    foreach ($notas as $estudiante) {
        $periodo = $estudiante['Periodo'];
        if (!isset($cursos_separados[$periodo])) {
            $cursos_separados[$periodo] = [];
        }
        $cursos_separados[$periodo][] = [
            'Número Estudiante' => $estudiante['Número Estudiante'],
            'Sigla Curso' => $estudiante['Sigla Curso'],
            'Calificación' => $estudiante['Calificación'],
            'Nota' => $estudiante['Nota'],
            'Periodo' => $periodo,
        ];
    }
    ksort($cursos_separados);
    return $cursos_separados;
}

function encontrar_cursos($numero_estudiante){
    $matriz_cursos = crear_matriz_notas();
    $notas_estudiante = [];
    foreach($matriz_cursos as $n){
        if ($n['Número Estudiante'] == $numero_estudiante){
            $notas_estudiante[] = $n;
        }
    }
    return $notas_estudiante;
}

function encontrar_estudiante($persona){
    $estudiantes = crear_matriz_estudiante();
    foreach($estudiantes as $p){
        if ($p['RUN'] == $persona['RUN'] && $p['DV'] == $persona['DV']){
            return $p;
        }
    }
    return "NO";
}
function cerrar_secion(){
    $red = "\033[31m";
    $green = "\033[32m";
    $yellow = "\033[33m";
    $bold = "\033[1m";
    $reset = "\033[0m";
    $seguir = True;
    while ($seguir){
        echo "\n ¿Desea realizar otra operación?\n";
        echo "{$bold}{$red}[1]{$reset}   Si\n";
        echo "{$bold}{$red}[2]{$reset}   No\n";
        echo "{$bold}{$green}Su opción:{$red}";
        $elecciòn = trim(fgets(STDIN));
        echo $reset;
        if ($elecciòn == "1"){
            return null;
        }elseif($elecciòn == "2"){
            echo "\n{$bold}{$red}---------------------------{$bold}{$green}Cerrando Bananer";
            echo"{$bold}{$red}----------------------------\n";
            exit();
        }else{
            echo "\nPorfavor ingrese una opción valida (1 o 2)\n";
        }
    }
}
?>