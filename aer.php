<?php
// Conexión a la base de datos Personas
$db_personas = pg_connect("host=bdd1.ing.puc.cl port=5432 dbname=grupo57e3 user=grupo57e3 password=exjofa");
if (!$db_personas) {
    die("Error en la conexión a la base de datos de Personas.\n");
} else {
    echo "Conexión a la base de datos de Personas establecida.\n";
}

// Conexión a la base de datos Profesores
$db_profesores = pg_connect("host=bdd1.ing.puc.cl port=5432 dbname=e3profesores user=grupo57e3 password=exjofa");
if (!$db_profesores) {
    die("Error en la conexión a la base de datos de Profesores.\n");
} else {
    echo "Conexión a la base de datos de Profesores establecida.\n";
}

try {
    $result_check_column = pg_query($db_personas, "SELECT column_name FROM information_schema.columns WHERE table_name='persona' AND column_name='actualizado';");
    if (pg_num_rows($result_check_column) == 0) {
        pg_query($db_personas, "ALTER TABLE persona ADD COLUMN actualizado BOOLEAN DEFAULT FALSE;");
        echo "Columna 'actualizado' añadida a la tabla Persona.\n";
    }

    $result_profesores = pg_query($db_profesores, "SELECT run, nombre, apellido1, apellido2, telefono, email_institucional, email_personal FROM profesores;");
    if (!$result_profesores) {
        throw new Exception("Error al obtener datos de la tabla Profesores.\n");
    }

    $count_updates = 0;
    while ($profesor = pg_fetch_assoc($result_profesores)) {
        $run = $profesor['run'];
        $nombre_completo = $profesor['nombre'] . ' ' . $profesor['apellido1'] . ' ' . $profesor['apellido2'];
        $telefono = $profesor['telefono'];
        $email_institucional = $profesor['email_institucional'];
        $email_personal = $profesor['email_personal'];

        $result_persona = pg_query_params(
            $db_personas,
            "SELECT * FROM persona WHERE run = $1 AND actualizado = FALSE;",
            [$run]
        );

        if (pg_num_rows($result_persona) > 0) {
            $update_query = "
                UPDATE persona
                SET nombre_completo = $1,
                    telefono = $2,
                    email_institucional = $3,
                    email_personal = $4,
                    actualizado = TRUE
                WHERE run = $5;
            ";
            $result_update = pg_query_params(
                $db_personas,
                $update_query,
                [$nombre_completo, $telefono, $email_institucional, $email_personal, $run]
            );

            if (!$result_update) {
                throw new Exception("Error al actualizar el registro con RUN $run en la tabla Persona.\n");
            }

            $count_updates++;
        }
    }

    echo "Actualización completada. Total de registros actualizados: $count_updates.\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} finally {
    pg_close($db_personas);
    pg_close($db_profesores);
}
?>
