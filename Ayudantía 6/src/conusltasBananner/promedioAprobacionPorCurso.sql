WITH Aprobados AS (
  SELECT 
    h.sigla_curso,
    COUNT(*) AS total_aprobados
  FROM 
    HistorialAcadémico h
  WHERE 
    h.sigla_curso = 'CODIGO_CURSO_INGRESADO' AND
    h.calificacion IN ('SO', 'MB', 'B', 'SU', 'EX', 'A')  -- Definir las calificaciones aprobatorias
  GROUP BY 
    h.sigla_curso
),
TotalEstudiantes AS (
  SELECT 
    h.sigla_curso,
    COUNT(*) AS total_estudiantes
  FROM 
    HistorialAcadémico h
  WHERE 
    h.sigla_curso = 'CODIGO_CURSO_INGRESADO'
  GROUP BY 
    h.sigla_curso
)
SELECT 
  p.nombres || ' ' || p.apellido_paterno AS nombre_profesor,
  AVG(COALESCE(a.total_aprobados, 0) * 100.0 / NULLIF(t.total_estudiantes, 0)) AS promedio_porcentaje_aprobacion
FROM 
  Profesor p
  JOIN OfertaAcademica o ON p.run = o.run_profesor
  LEFT JOIN Aprobados a ON o.sigla_curso = a.sigla_curso
  LEFT JOIN TotalEstudiantes t ON o.sigla_curso = t.sigla_curso
WHERE 
  o.sigla_curso = 'CODIGO_CURSO_INGRESADO' AND 
  o.periodo = 'PERIODO_DE_INTERES'  -- Reemplaza con el periodo deseado
GROUP BY 
  p.run;
