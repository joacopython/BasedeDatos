<?php include('../templates/header.html'); ?>

<body>
  <?php
  require("../config/conexion.php");

  // Capturar el número de estudiante desde el formulario
  $numero_estudiante = $_POST["numero_estudiante"];
  $numero_estudiante = intval($numero_estudiante);

  // Consulta SQL adaptada
  $query = "
    WITH EstudianteVigente AS (
      SELECT 
        e.numero_estudiante
      FROM 
        Estudiante e
      JOIN 
        HistorialAcadémico h ON e.numero_estudiante = h.numero_estudiante
      WHERE 
        e.numero_estudiante = :numero_estudiante 
        AND h.periodo = '2024-2'
    ),
    CursosAprobados AS (
      SELECT 
        h.sigla_curso
      FROM 
        HistorialAcadémico h
      WHERE 
        h.numero_estudiante = :numero_estudiante
        AND h.calificacion IN ('SO', 'MB', 'B', 'SU', 'EX', 'A')
    ),
    CursosDisponibles2025 AS (
      SELECT 
        o.sigla_curso
      FROM 
        OfertaAcademica o
      WHERE 
        o.periodo = '2025-1'
    ),
    PropuestaTomaRamos AS (
      SELECT 
        o.sigla_curso
      FROM 
        CursosDisponibles2025 o
      LEFT JOIN 
        CursosAprobados ca ON o.sigla_curso = ca.sigla_curso
      WHERE 
        ca.sigla_curso IS NULL
    )
    SELECT 
      p.sigla_curso AS propuesta_cursos
    FROM 
      PropuestaTomaRamos p
    JOIN 
      EstudianteVigente e ON 1 = 1;
    ";

  $result = $db->prepare($query);
  $result->bindParam(':numero_estudiante', $numero_estudiante, PDO::PARAM_STR);
  $result->execute();
  $propuesta = $result->fetchAll(PDO::FETCH_ASSOC);
  ?>

  <table class="styled-table">
    <tr>
      <th>Propuesta de Cursos</th>
    </tr>
    <?php
    foreach ($propuesta as $curso) {
      echo "<tr><td>{$curso['propuesta_cursos']}</td></tr>";
    }
    ?>
  </table>
</body>

<?php include('../templates/footer.html'); ?>
