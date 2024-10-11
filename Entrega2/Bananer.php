<?php
include "cargador.php";
include "funciones bananer.php";
include "funciones_matrices.php";

echo "Cargando....\n";

#cargar_archivos();
$personas = crear_matriz_persona();



$red = "\033[31m";
$green = "\033[32m";
$yellow = "\033[33m";
$bold = "\033[1m";
$reset = "\033[0m";

echo "{$bold}{$red}==============================\n";
echo "|                            |\n";
echo "|    {$bold}{$green}Bienvenido a Bananer{$bold}{$red}    |\n";
echo "|                            |\n";
echo "==============================\n\n\n{$reset}";

$seguir = TRUE;
while($seguir){
    echo "¿Que operaciòn desea realizar?\n\n";
    echo "{$bold}{$red}[1]{$reset}   Iniciar seción\n";
    echo "{$bold}{$red}[2]{$reset}   Salir\n";
    echo "{$bold}{$green}Su opción:{$red}";
    $opcion = trim(fgets(STDIN));
    echo "{$reset}";
    if ($opcion ==="1"){
        $ingresar = True;
        $contador = 0;
        while($ingresar){ 
            echo "\nIngrese su rut:\n";
            echo "{$bold}{$red}";
            $rut = trim(fgets(STDIN));
            echo "{$reset}";
            $persona = ingresar_por_rut($rut, $personas);
            if ($persona == []){
                if ($contador == 2){
                    $ingresar = False;
                    echo "Ingreso Invalido \n\n";
                }else{
                    echo "El rut {$bold}{$red}{$rut}{$reset} no está registrado\n";
                    $contador ++;
                    $intentos = 3 - $contador;
                    echo "{$bold}{$green}Le quedan {$intentos} intentos{$reset}\n";
                }
            }else{
                echo "Bienvenid@ {$persona[0]['Nombres']}\n";
                ingresar($persona[0]);
                $ingresar = FALSE;
            }
        }
    }elseif($opcion ==="2"){
        echo "\n{$bold}{$red}---------------------------{$bold}{$green}Cerrando Bananer";
        echo"{$bold}{$red}----------------------------\n";
        $seguir = False;
    }else{
        echo "\nPorfavor ingrese una opción valida (1 o 2)\n";
    }
}

?>