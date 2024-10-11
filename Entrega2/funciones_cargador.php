<?php
function csv_to_array($file_name) {
    $file = fopen($file_name, "r");

    // Leer la primera línea para obtener los encabezados
    $headers = fgetcsv($file, 1000, ";");
    $headers[0] = remove_bom($headers[0]);

    $data = [];

    // Leer cada línea del archivo
    while (($row = fgetcsv($file, 1000, ";")) !== FALSE) {
        // Crear un array asociativo para cada fila
        $row_data = [];
        foreach ($headers as $index => $header) {
            $row_data[$header] = $row[$index];
        }
        // Agregar la fila al array de datos
        $data[] = $row_data;
    }

    fclose($file);
    return $data;
}

function remove_bom($string) {
    if (substr($string, 0, 3) === "\xef\xbb\xbf") {
        $string = substr($string, 3);
    }
    return $string;
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


#función extraída de la Ayudantia 1:

function remove_repeats($repeated_array){
    $clean_array = [];
    foreach ($repeated_array as $line){
        if (in_array($line, $clean_array)){
        } else {
            $clean_array[] = $line;
        }
    }
    return $clean_array;
}


function file_open($file_name){
    $file = fopen($file_name, "r");
    $content_array = [];
    while (!feof($file)){
        $line = fgets($file);
        $content_array[] = explode(",", $line);
    }
    fclose($file);
    return $content_array;
}


function save_data($file_name, $array){
    $new_file = fopen($file_name, "w");
    if($new_file){
        foreach ($array as $data){
            $line = implode(",", $data);
            fwrite($new_file, $line);
        }
        fclose($new_file);
    }
    return null;
}


function filter_1(&$mega_array){
    foreach($mega_array as &$array){

        # verificar no nulo
        if ($array["Cohorte"] === ""){
            $array["Cohorte"] = "X";
        }
        if ($array["Código Plan"] ===""){
            $array["Código Plan"] = "X";
        }
        if ($array["Plan"] ===""){
            $array["Plan"] = "X";
        }
        if ($array["Bloqueo"] ===""){
            $array["Bloqueo"] = "X";
        }
        if ($array["RUN"] ==="" || ctype_digit($array["RUN"]) == FALSE){
            $array["RUN"] = "X";
        }
        if ($array["DV"] ===""){
            $array["DV"] = "X";
        }
        if ($array["Nombres"] ===""){
            $array["Nombres"] = "X";
        }
        if ($array["Apellido Paterno"] ===""){
            $array["Apellido Paterno"] = "X";
        }
        if ($array["Nombre Completo"] ===""){
            $array["Nombre Completo"] = "X";
        }
        if ($array["Número estudiante"] ===""){
            $array["Número estudiante"] = "X";
        }
        if ($array["Periodo curso"] ===""){
            $array["Periodo curso"] = "X";
        }
        if ($array["Sigla curso"] ===""){
            $array["Sigla curso"] = "X";
        }
        if ($array["Asignatura"] ===""){
            $array["Asignatura"] = "X";
        }
        if ($array["Sección"] ===""){
            $array["Sección"] = "X";
        }
        if ($array["Calificación"] ===""){
            $array["Calificación"] = "X";
        }
        if ($array["Último Logro"] ===""){
            $array["Último Logro"] = "X";
        }
        if ($array["Fecha Logro"] ===""){
            $array["Fecha Logro"] = "X";
        }
        if ($array["Última Carga"] ===""){
            $array["Última Carga"] = "X";
        }


        #Email
        $email_personal = trim($array['Mail Personal']);
        $email_institucional = trim($array['Mail Institucional']);
        $formato_intitucional = '/^.+@lamejor\.cl$/';
        if (preg_match($formato_intitucional, $email_institucional)){
            $array['Mail Institucional'] = $email_institucional;
        }else{
            $array['Mail Institucional'] = "";
        }
        $array['Mail Personal'] = $email_personal;

        
        #Nota
        $num = $array["Nota"];
        if ($num === '' || (floatval($num) >= 1 && floatval($num) <= 7)) {
        }else{
            $array["Nota"] = "";
        }
    }
    return $mega_array;
}


function filter_2(&$mega_array){
    foreach($mega_array as &$array){
        #no nulos
        if ($array["RUN"] ==="" || ctype_digit($array["RUN"]) == FALSE){
            $array["RUN"] = "X";
        }
        if ($array["DV"] ===""){
            $array["DV"] = "X";
        }

        #contrato
        $c = strtoupper(trim($array['CONTRATO']));
        if ( $c =="FULL TIME" || $c == "PART TIME" || $c == "HONORARIO"){
        }else{
            #Reemplazar por nulo
            $array["CONTRATO"] = "";
            if ($c == "DIURNO"){
                $array["JORNADA DIURNO"] = strtolower($c);
            }
        }

        #Telefono
        $num = $array["TELÉFONO"];
        if ($num === '' || ctype_digit($num) && strlen($num) === 9 ) {
        }else{
            $array["TELÉFONO"] = "XXXXXXXXX";
        }


        #Email
        $email_personal = trim($array['MAIL PERSONAL']);
        $email_institucional = trim($array['MAIL INSTITUCIONAL']);
        $formato_intitucional = '/^.+@lamejor\.cl$/';
        if (preg_match($formato_intitucional, $email_institucional)){
            $array['MAIL INSTITUCIONAL'] = $email_institucional;
        }else{
            $array['MAIL INSTITUCIONAL'] = "";
        }
        $array['MAIL PERSONAL'] = $email_personal;
    }
    return $mega_array;
}

?>