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
      ELSE 'Vigente'  -- Asumiendo que las calificaciones no finales se consideran vigentes
    END AS estado_curso
  FROM 
    HistorialAcadémico h
  WHERE 
    h.numero_estudiante = 'NUMERO_ESTUDIANTE'  -- Reemplazar con el parámetro ingresado
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
        WHERE h.numero_estudiante = 'NUMERO_ESTUDIANTE' AND h.periodo = '2024-2'
      ) THEN 'Vigente'
      WHEN EXISTS (
        SELECT 1 
        FROM HistorialAcadémico h 
        WHERE h.numero_estudiante = 'NUMERO_ESTUDIANTE' 
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
  ResumenTotal t ON 1 = 1  -- Para incluir el resumen total en todos los registros
LEFT JOIN 
  EstadoEstudiante e ON 1 = 1
ORDER BY 
  h.periodo ASC, h.sigla_curso;
