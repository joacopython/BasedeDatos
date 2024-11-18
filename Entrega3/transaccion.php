<?php
include('../config/conexion.php');

try {
    $db->beginTransaction();

    $file = '../data/notas adivinacion I.csv';
    if (!file_exists($file) || !is_readable($file)) {
        throw new Exception("No se puede leer el archivo CSV.\n");
    }

    $data = array_map(function($line) {
        return str_getcsv($line, ';');
    }, file($file));
    
    $encabezados = array_shift($data);
    $errores = [];

    $db->exec("
        CREATE TEMPORARY TABLE acta (
            numero_estudiante INT,
            sigla_curso VARCHAR(30),
            periodo VARCHAR(30),
            seccion INT,
            calificacion calificacion_enum,
            nota DECIMAL(3, 2),
            PRIMARY KEY (numero_estudiante, sigla_curso)
        );
    ");


    $stmt = $db->prepare("
        INSERT INTO acta (numero_estudiante, sigla_curso, periodo, seccion, calificacion, nota)
        VALUES (:numero_estudiante, :sigla_curso, :periodo, :seccion, :calificacion, :nota)
    ");
    foreach ($data as $index => $row) {
        $numero_estudiante = $row[0];
        $run = $row[1];
        $asignatura = $row[2];
        $seccion = $row[3];
        $periodo = $row[4];
        $nota_dic = $row[5];
        $nota_mar = $row[6];
        
        $calificacion;
        if($nota_dic == "P"){
          $nota = NULL;
          $calificacion = 'P';
        }
        
        if(!empty($nota_dic)){
            $nota = (float)str_replace(',', '.', $nota_dic);
            if ($nota < 4.0){
                if (!empty($nota_mar)){
                    $nota = (float)str_replace(',', '.', $nota_mar);
                    if ($nota < 4.0){
                        $calificacion = 'R';
                    }
                }else{
                    $nota = NULL;
                    $calificacion = 'R';
                }
            }
        }elseif(!empty($nota_mar)){
            $nota = (float)str_replace(',', '.', $nota_mar);
            if ($nota < 4.0){
                if (!empty($nota_mar)){
                    $nota = (float)str_replace(',', '.', $nota_mar);
                    if ($nota < 4.0){
                        $calificacion = 'R';
                    }
                    else{
                        $calificacion = 'A';
                    }
                }
                else
                {
                    $nota = NULL;
                    $calificacion = 'R';
                }
            }
        }
        else{
            $nota = 'null';
            $calificacion = 'NP';
        }
        
        if ((!is_numeric($nota) && !empty($nota)) || $nota < 0 || $nota > 7) {
            if (empty($numero_estudiante)){
                continue;
            }
            $errores[] = "Nota del número de alumno $numero_estudiante contiene un valor erróneo ($nota).";
            continue;
        }
        
        $stmt->execute([
            ':numero_estudiante' => $numero_estudiante,
            ':sigla_curso' => $asignatura,
            ':periodo' => $periodo,
            ':seccion' => $seccion,
            ':calificacion' => $calificacion,
            ':nota' => $nota
        ]);
    }
    if (!empty($errores)) {
        $db->rollBack();
        foreach ($errores as $error) {
            echo "$error\n";
        }
        exit("Corrija los errores y vuelva a intentar.\n");
    }
    $db->commit();
    echo "Datos insertados correctamente en la tabla temporal.\n";

    $crearVistaActaNotas = "
    CREATE OR REPLACE VIEW ActaNotas AS
    SELECT 
        a.numero_estudiante,
        per.nombres AS nombre_estudiante,
        a.sigla_curso,
        c.nombre_curso AS nombre_curso,
        a.periodo,
        profPersona.nombres AS nombre_profesor,
        ROUND(a.nota, 2) AS nota_final -- Nota redondeada a 2 decimales
    FROM 
        acta a
    JOIN Estudiante e ON a.numero_estudiante = e.numero_estudiante
    JOIN Persona per ON e.run = per.run -- Relación Estudiante -> Persona para obtener el nombre del estudiante
    JOIN OfertaAcademica oa ON a.sigla_curso = oa.sigla_curso -- Relación acta -> OfertaAcademica
    JOIN Curso c ON oa.sigla_curso = c.sigla_curso -- Relación OfertaAcademica -> Curso
    JOIN Profesor prof ON oa.run_profesor = prof.run -- Relación OfertaAcademica -> Profesor
    JOIN Persona profPersona ON prof.run = profPersona.run; -- Relación Profesor -> Persona para obtener el nombre del profesor
    ";
    
    $db->exec($crearVistaActaNotas);
    

    echo "Vista 'ActaNotas' creada exitosamente.\n";

    // Consultar y mostrar la vista
    $consultaVista = "SELECT * FROM ActaNotas;";
    $resultadoVista = $db->query($consultaVista);

    echo $resultadoVista;

    echo "<h1>Acta de Notas</h1>";
    echo "<table border='1'>";
    echo "<tr><th>Número Estudiante</th><th>Nombre Estudiante</th><th>Curso</th><th>Nombre Curso</th><th>Periodo</th><th>Nombre Profesor</th><th>Nota Final</th></tr>";

    foreach ($resultadoVista as $fila) {
        echo "Estoy acá\n";
        echo "<tr>";
        echo "<td>" . htmlspecialchars($fila['numero_estudiante']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['nombre_estudiante']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['sigla_curso']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['nombre_curso']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['periodo']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['nombre_profesor']) . "</td>";
        echo "<td>" . htmlspecialchars($fila['nota_final']) . "</td>";
        echo "</tr>";
    }
    echo "Termine acá\n";
    echo "</table>";

}catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "Error: " . $e->getMessage();
}
?>
