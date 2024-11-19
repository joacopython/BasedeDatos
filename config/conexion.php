<?php
  try {
    #Pide las variables para conectarse a la base de datos.
    require('data.php'); 
    # Se crea la instancia de PDO
    $db = new PDO("pgsql:dbname=$databaseName;host=localhost;port=5432;user=$user;password=$password");
  } catch (Exception $e) {
    echo "No se pudo conectar a la base de datos: $e";
  }
?>

<?php
$db_host = "bdd1.ing.puc.cl";
$db_port = "5432";
$db_name = "e3profesores";
$db_user = "grupo57e3";
$db_password = "exjofa";

$db_profes = pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_password");


if (!$db_profes) {
    die("Error: No se pudo conectar a la base de datos.");
}


?>