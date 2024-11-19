<?php
include('../config/conexion.php');

try {

    $create_table_sql = "CREATE TABLE Profesor (
        run INTEGER NOT NULL,
        nombre CHARACTER VARYING,
        apellido1 CHARACTER VARYING,
        apellido2 CHARACTER VARYING,
        sexo CHARACTER(1),
        jerarquizacion CHARACTER VARYING,
        telefono INTEGER,
        email_personal CHARACTER VARYING(128),
        email_institucional CHARACTER VARYING(128),
        dedicacion INTEGER,
        contrato CHARACTER VARYING,
        jornada CHARACTER VARYING,
        sede CHARACTER VARYING,
        carrera CHARACTER VARYING,
        grado_academico CHARACTER VARYING,
        detalle CHARACTER VARYING,
        PRIMARY KEY (run)
);";
    
    $db->exec($create_table_sql);

    $query_data = "SELECT * FROM profesores;";
    $result_data = pg_query($db_profes, $query_data);

    if (!$result_data) {
        die("Error al obtener los datos de la tabla: " . pg_last_error($db_profes));
    }

    while ($row = pg_fetch_assoc($result_data)) {
        $columns = implode(", ", array_keys($row));
        $values = implode(", ", array_map(function ($value) {
            return $value === null ? "NULL" : "'" . pg_escape_string($value) . "'";


        }, array_values($row)));

        $insert_query = "INSERT INTO Profesor ($columns) VALUES ($values);";
        $db->exec($insert_query);
    }

    echo "La tabla Profesores ha sido transferida exitosamente.";
} catch (PDOException $e) {
    $db->exec("DROP TABLE Profesor;");
    die("Error durante el proceso: " . $e->getMessage());
}


try {
    $transfer_query = "
        INSERT INTO Persona (run, nombres, apellido_paterno, apellido_materno, email_institucional)
        SELECT 
            run, 
            nombre AS nombres, 
            apellido1 AS apellido_paterno, 
            apellido2 AS apellido_materno, 
            email_institucional
        FROM Profesor;
    ";

    $db->exec($transfer_query);
    echo "Los datos han sido transferidos exitosamente de Profesor a Persona.";
} catch (PDOException $e) {
    die("Error durante la transferencia: " . $e->getMessage());
}
?>
