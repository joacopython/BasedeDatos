<?php
function leerArchivo($rutaArchivo) {
    // Verifica si el archivo existe
    if (file_exists($rutaArchivo)) {
        // Intenta abrir el archivo en modo lectura
        $archivo = fopen($rutaArchivo, "r");

        // Verifica si se pudo abrir el archivo
        if ($archivo) {
            // Lee todo el contenido del archivo
            $contenido = fread($archivo, filesize($rutaArchivo));

            // Cierra el archivo
            fclose($archivo);

            // Retorna el contenido del archivo
            return $contenido;
        } else {
            // Error al abrir el archivo
            return "Error: No se pudo abrir el archivo.";
        }
    } else {
        // Error si el archivo no existe
        return "Error: El archivo no existe.";
    }
}
?>
