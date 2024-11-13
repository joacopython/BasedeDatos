WITH Aprobados AS (
    SELECT 
        h.sigla_curso,
        COUNT(*) AS total_aprobados
    FROM 
        HistorialAcademico h
    WHERE 
        h.periodo = 'PERIODO_INGRESADO' AND
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
        h.periodo = 'PERIODO_INGRESADO'
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
    o.periodo = 'PERIODO_INGRESADO';
