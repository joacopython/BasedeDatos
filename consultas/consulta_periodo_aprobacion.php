<?php include('../templates/header.html'); ?>

<body>
  <?php
  require("../config/conexion.php");

  $periodo = $_POST["periodo"];
  $query = "
    WITH Aprobados AS (
      SELECT 
        h.sigla_curso,
        COUNT(*) AS total_aprobados
      FROM 
        HistorialAcademico h
      WHERE 
        h.periodo = '%$periodo%' AND
        h.calificacion IN ('SO', 'MB', 'B', 'SU', 'EX', 'A')
      GROUP BY 
        h.sigla_curso
    ),
    TotalEstudiantes AS (
      SELECT 
        h.sigla_curso,
        COUNT(*) AS total_estudiantes
      FROM 
        HistorialAcademico h
      WHERE 
        h.periodo = '%$periodo%'
      GROUP BY 
        h.sigla_curso
    )
    SELECT 
      c.sigla_curso AS codigo_curso,
      c.nombre_curso AS nombre_curso,
      p.nombres || ' ' || p.apellido_paterno AS nombre_profesor,
      COALESCE(a.total_aprobados, 0) * 100.0 / NULLIF(t.total_estudiantes, 0) AS porcentaje_aprobacion
    FROM 
      Curso c
    JOIN 
      OfertaAcademica o ON c.sigla_curso = o.sigla_curso
    JOIN 
      Profesor p ON o.run_profesor = p.run
    LEFT JOIN 
      Aprobados a ON c.sigla_curso = a.sigla_curso
    LEFT JOIN 
      TotalEstudiantes t ON c.sigla_curso = t.sigla_curso
    WHERE 
      o.periodo = '%$periodo%';
  ";
  
  $result = $db->prepare($query);
  $result->bindParam(':periodo', $periodo, PDO::PARAM_STR);
  
  if ($result->execute()) {
    $profesores = $result->fetchAll();
  } else {
    $profesores = [];
  }
  ?>
  
  <table class="styled-table">
    <tr>
      <th>Nombre del Profesor</th>
      <th>Promedio Porcentaje Aprobación</th>
    </tr>
    <?php
    foreach ($profesores as $p) {
      echo "<tr><td>{$p['nombre_profesor']}</td><td>{$p['porcentaje_aprobacion']}</td></tr>";
    }
    ?>
  </table>
</body>

<?php include('../templates/footer.html'); ?>


