<?php include('../templates/header.html'); ?>

<body>
  <?php
  require("../config/conexion.php");
  $codigo_curso = $_POST["codigo_curso"];

  $query = "
    WITH Aprobaciones AS (
      SELECT
        o.run_profesor,
        COUNT(CASE WHEN h.calificacion IN ('SO', 'MB', 'B') THEN 1 END) AS aprobados,
        COUNT(h.numero_estudiante) AS total
      FROM
        HistorialAcademico h
      JOIN
        OfertaAcademica o ON h.sigla_curso = o.sigla_curso 
        AND h.periodo = o.periodo 
        AND h.seccion = o.seccion_curso
      WHERE
        h.sigla_curso = :codigo_curso
      GROUP BY
        o.run_profesor
    )

    SELECT
      p.run,
      p.nombres,
      p.apellido_paterno,
      p.apellido_materno,
      (CAST(a.aprobados AS DECIMAL) / NULLIF(a.total, 0)) * 100 AS porcentaje_aprobacion
    FROM
      Aprobaciones a
    JOIN
      Profesor p ON a.run_profesor = p.run
    ORDER BY
      porcentaje_aprobacion DESC;
  ";

  $result = $db->prepare($query);
  $result->bindParam(':codigo_curso', $codigo_curso);  // Vincular el parámetro
  $result->execute();
  $profesores = $result->fetchAll(PDO::FETCH_ASSOC);

  ?>

  <table class="styled-table">
    <tr>
      <th>RUN</th>
      <th>Nombres</th>
      <th>Apellido Paterno</th>
      <th>Apellido Materno</th>
      <th>Porcentaje de Aprobación</th>
    </tr>
    <?php
    foreach ($profesores as $profesor) {
      echo "<tr>
              <td>{$profesor['run']}</td>
              <td>{$profesor['nombres']}</td>
              <td>{$profesor['apellido_paterno']}</td>
              <td>{$profesor['apellido_materno']}</td>
              <td>{$profesor['porcentaje_aprobacion']}%</td>
            </tr>";
    }
    ?>
  </table>
</body>

<?php include('../templates/footer.html'); ?>
