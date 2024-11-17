<?php
include('../config/conexion.php');

try {
    $db->beginTransaction();

    $file = '../data/notas adivinacion I.csv';
    if (!file_exists($file) || !is_readable($file)) {
        throw new Exception("No se puede leer el archivo CSV.\n");
    }

    $data = array_map('str_getcsv', file($file));
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
        INSERT INTO acta (numero_estudiante, sigla_curso, periodo, seccion, nota)
        VALUES (:numero_estudiante, :sigla_curso, :periodo, :seccion, :calificacion, :nota)
    ");

    foreach ($data as $index => $row) {
        [$numero_estudiante, $run, $asignatura, $seccion, $periodo, $nota_dic, $nota_mar] = $row;
        
        $calificacion;
        if($nota_dic == "P"){
          $nota = null;
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
                    $nota = null;
                    $calificacion = 'R';
                }
            }
        }else{
            $nota = null;
            $calificacion = 'NP';
        }
        
        if (!is_numeric($nota) || $nota < 0 || $nota > 7) {
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
    echo "Datos insertados correctamente en la tabla temporal.";

        
    $crearVistaActaNotas = "
    CREATE OR REPLACE VIEW ActaNotas AS
    SELECT 
        a.numero_estudiante,
        e.nombre AS nombre_estudiante,
        a.sigla_curso,
        c.nombre AS nombre_curso,
        a.periodo,
        p.nombre AS nombre_profesor,
        ROUND(a.nota, 2) AS nota_final
    FROM 
        acta a
    JOIN Estudiante e ON a.numero_estudiante = e.numero_estudiante
    JOIN Curso c ON a.sigla_curso = c.sigla_curso
    JOIN Profesor p ON c.run_profesor = p.run;
";
$db->exec($crearVistaActaNotas);
echo "Vista 'ActaNotas' creada exitosamente.\n";

// Confirmar la transacción
$db->commit();

// Consultar y mostrar la vista
$consultaVista = "SELECT * FROM ActaNotas;";
$resultadoVista = $db->query($consultaVista);

echo "<h1>Acta de Notas</h1>";
echo "<table border='1'>";
echo "<tr><th>Número Estudiante</th><th>Nombre Estudiante</th><th>Curso</th><th>Nombre Curso</th><th>Periodo</th><th>Nombre Profesor</th><th>Nota Final</th></tr>";

foreach ($resultadoVista as $fila) {
    echo "<tr>";
    echo "<td>" . $fila['numero_estudiante'] . "</td>";
    echo "<td>" . $fila['nombre_estudiante'] . "</td>";
    echo "<td>" . $fila['sigla_curso'] . "</td>";
    echo "<td>" . $fila['nombre_curso'] . "</td>";
    echo "<td>" . $fila['periodo'] . "</td>";
    echo "<td>" . $fila['nombre_profesor'] . "</td>";
    echo "<td>" . $fila['nota_final'] . "</td>";
    echo "</tr>";
}
echo "</table>";
} catch (Exception $e) {
    if ($db->inTransaction()) {
        $db->rollBack();
    }
    echo "Error: " . $e->getMessage();
}
?>
