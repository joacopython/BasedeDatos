<?php
// Configuración de conexión
$db_host = "localhost";
$db_port = "5432";
$db_name = "e3profesores";
$db_user = "grupo57";
$db_password = "exjofa";


// Establecer conexión
$db_profes = pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_password");

if (!$db_profes) {
    die("Error: No se pudo conectar a la base de datos.");
}

// Consulta SQL
$query = "SELECT * FROM Profesores;";
$result = pg_query($db_profes, $query);

if (!$result) {
    die("Error al ejecutar la consulta: " . pg_last_error($db_profes));
}

// Mostrar resultados
while ($row = pg_fetch_assoc($result)) {
    echo "ID: " . htmlspecialchars($row['id']) . " - Nombre: " . htmlspecialchars($row['nombre']) . "<br>";
}

// Cerrar conexión
pg_close($db_profes);
?>
