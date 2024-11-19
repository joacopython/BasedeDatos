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
    // Verifica el código de error SQLSTATE
    $errorCode = $e->getCode();
    switch ($errorCode) {
        case '42P01': // Tabla no existe
            echo "<p>Error: La tabla especificada no existe.</p>";
            break;
        case '42703': // Columna no existe
            echo "<p>Error: Uno o más atributos especificados no existen en la tabla.</p>";
            break;
        case '42601': // Error de sintaxis SQL
            echo "<p>Error de sintaxis en la consulta SQL.</p>";
            break;
        default: // Otros errores
            echo "<p>Error en la consulta: " . $e->getMessage() . "</p>";
            break;
    }
} catch (Exception $e) {
    echo "<p>Ocurrió un error inesperado: " . $e->getMessage() . "</p>";
}catch (Exception $e) {
    echo "Ocurrió un error inesperado: " . $e->getMessage();
}

include('../templates/footer_admin.html');
?>

