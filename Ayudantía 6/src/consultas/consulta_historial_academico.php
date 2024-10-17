<?php include('../templates/header.html'); ?>

<body>
  <?php
  require("../config/conexion.php");

  // Obtener el parámetro número_estudiante desde un formulario (o cualquier otra entrada)
  $numero_estudiante = $_POST["numero_estudiante"];

  // Consulta SQL con el parámetro :numero_estudiante
  $query = "
  WITH HistorialEstudiante AS (
    SELECT 
      h.numero_estudiante,
      h.sigla_curso,
      h.periodo,
      h.nota_final,
      h.calificacion,
      CASE
        WHEN h.calificacion IN ('SO', 'MB', 'B', 'SU', 'EX', 'A') THEN 'Aprobado'
        WHEN h.calificacion IN ('D', 'E', 'F', 'I') THEN 'Reprobado'
        ELSE 'Vigente'
      END AS estado_curso
    FROM 
      HistorialAcadémico h
    WHERE 
      h.numero_estudiante = :numero_estudiante
  ),
  ResumenPorPeriodo AS (
    SELECT 
      h.periodo,
      SUM(CASE WHEN estado_curso = 'Aprobado' THEN 1 ELSE 0 END) AS cursos_aprobados,
      SUM(CASE WHEN estado_curso = 'Reprobado' THEN 1 ELSE 0 END) AS cursos_reprobados,
      SUM(CASE WHEN estado_curso = 'Vigente' THEN 1 ELSE 0 END) AS cursos_vigentes,
      AVG(CASE WHEN estado_curso = 'Aprobado' OR estado_curso = 'Reprobado' THEN h.nota_final ELSE NULL END) AS pps
    FROM 
      HistorialEstudiante h
    GROUP BY 
      h.periodo
  ),
  ResumenTotal AS (
    SELECT 
      SUM(CASE WHEN estado_curso = 'Aprobado' THEN 1 ELSE 0 END) AS total_aprobados,
      SUM(CASE WHEN estado_curso = 'Reprobado' THEN 1 ELSE 0 END) AS total_reprobados,
      SUM(CASE WHEN estado_curso = 'Vigente' THEN 1 ELSE 0 END) AS total_vigentes,
      AVG(CASE WHEN estado_curso = 'Aprobado' OR estado_curso = 'Reprobado' THEN h.nota_final ELSE NULL END) AS ppa
    FROM 
      HistorialEstudiante h
  ),
  EstadoEstudiante AS (
    SELECT 
      CASE 
        WHEN EXISTS (
          SELECT 1 
          FROM HistorialAcadémico h 
          WHERE h.numero_estudiante = :numero_estudiante AND h.periodo = '2024-2'
        ) THEN 'Vigente'
        WHEN EXISTS (
          SELECT 1 
          FROM HistorialAcadémico h 
          WHERE h.numero_estudiante = :numero_estudiante 
          AND h.calificacion IN ('LICENCIATURA', 'TITULO')
        ) THEN 'De Término'
        ELSE 'No Vigente'
      END AS estado_estudiante
  )
  SELECT 
    h.periodo,
    h.sigla_curso,
    h.nota_final,
    h.calificacion,
    r.cursos_aprobados,
    r.cursos_reprobados,
    r.cursos_vigentes,
    r.pps,
    t.total_aprobados,
    t.total_reprobados,
    t.total_vigentes,
    t.ppa,
    e.estado_estudiante
  FROM 
    HistorialEstudiante h
  LEFT JOIN 
    ResumenPorPeriodo r ON h.periodo = r.periodo
  LEFT JOIN 
    ResumenTotal t ON 1 = 1
  LEFT JOIN 
    EstadoEstudiante e ON 1 = 1
  ORDER BY 
    h.periodo ASC, h.sigla_curso;
  ";

  // Preparar y ejecutar la consulta
  $result = $db->prepare($query);
  $result->bindParam(':numero_estudiante', $numero_estudiante, PDO::PARAM_STR);
  $result->execute();
  $datos = $result->fetchAll(PDO::FETCH_ASSOC);

  ?>

  <table class="styled-table">
    <tr>
      <th>Periodo</th>
      <th>Sigla Curso</th>
      <th>Nota Final</th>
      <th>Calificación</th>
      <th>Cursos Aprobados</th>
      <th>Cursos Reprobados</th>
      <th>Cursos Vigentes</th>
      <th>PPS</th>
      <th>Total Aprobados</th>
      <th>Total Reprobados</th>
      <th>Total Vigentes</th>
      <th>PPA</th>
      <th>Estado Estudiante</th>
    </tr>
    <?php
    foreach ($datos as $dato) {
      echo "<tr>
              <td>{$dato['periodo']}</td>
              <td>{$dato['sigla_curso']}</td>
              <td>{$dato['nota_final']}</td>
              <td>{$dato['calificacion']}</td>
              <td>{$dato['cursos_aprobados']}</td>
              <td>{$dato['cursos_reprobados']}</td>
              <td>{$dato['cursos_vigentes']}</td>
              <td>{$dato['pps']}</td>
              <td>{$dato['total_aprobados']}</td>
              <td>{$dato['total_reprobados']}</td>
              <td>{$dato['total_vigentes']}</td>
              <td>{$dato['ppa']}</td>
              <td>{$dato['estado_estudiante']}</td>
            </tr>";
    }
    ?>
  </table>
</body>

<?php include('../templates/footer.html'); ?>
