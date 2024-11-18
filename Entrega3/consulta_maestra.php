<?php
include('../config/conexion.php');

$attributes = $_POST['attributes'];
$table = $_POST['table'];
$condition = $_POST['condition'];

if (!isset($attributes) || !isset($table) || !isset($condition)) {
  echo "<p>Por favor, complete todos los campos del formulario.</p>";
  die;
}



try {
    $query = $db->prepare("SELECT $attributes FROM $table WHERE $condition");
    $query->execute();

    $resultados = $query->fetchAll(PDO::FETCH_ASSOC);

    if ($query->rowCount() > 0) {
        echo "<h3>Resultados:</h3>";
        echo "<table border='1'><tr>";

        foreach ($resultados[0] as $key => $value) {
            echo "<th>$key</th>";
        }
        echo "</tr>";

        // Mostrar los resultados
        foreach ($resultados as $row) {
            echo "<tr>";
            foreach ($row as $cell) {
                echo "<td>$cell</td>";
            }
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No se encontraron resultados.";
    }

} catch (PDOException $e) {
    echo "Error en la consulta: " . $e->getMessage();
} catch (Exception $e) {
    echo "Ocurrió un error inesperado: " . $e->getMessage();
}

?>

<?php include('../templates/footer_admin.html'); ?>

