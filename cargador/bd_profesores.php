<?php

try {
    $query_table_structure = "
        SELECT pg_get_tabledef(pg_class.oid) AS create_table_sql
        FROM pg_class
        JOIN pg_namespace ON pg_class.relnamespace = pg_namespace.oid
        WHERE pg_class.relname = 'profesores' AND pg_namespace.nspname = 'public';";

    $result_table_structure = pg_query($db_profes, $query_table_structure);

    if (!$result_table_structure) {
        die("Error al obtener la estructura de la tabla: " . pg_last_error($db_profes));
    }

    $row_table_structure = pg_fetch_assoc($result_table_structure);
    $create_table_sql = $row_table_structure['create_table_sql'];


    $db->exec($create_table_sql);

    $query_data = "SELECT * FROM Profesores;";
    $result_data = pg_query($db_profes, $query_data);

    if (!$result_data) {
        die("Error al obtener los datos de la tabla: " . pg_last_error($db_profes));
    }

    while ($row = pg_fetch_assoc($result_data)) {
        $columns = implode(", ", array_keys($row));
        $values = implode(", ", array_map(function ($value) {
            return $value === null ? "NULL" : "'" . pg_escape_string($value) . "'";
        }, array_values($row)));

        $insert_query = "INSERT INTO Profesores ($columns) VALUES ($values);";
        $db->exec($insert_query);
    }

    echo "La tabla Profesores ha sido transferida exitosamente.";
} catch (PDOException $e) {
    die("Error durante el proceso: " . $e->getMessage());
}
?>
?>
