WITH EstudiantesVigentes AS (
  SELECT 
    e.numero_estudiante,
    e.cohorte,
    ul.ultimo_logro,
    ul.periodo
  FROM 
    Estudiante e
  JOIN 
    UltimoLogro ul ON e.numero_estudiante = ul.numero_estudiante
  WHERE 
    ul.periodo = '2024-2'
  ),
CohorteNivel AS (
 SELECT 
    ev.numero_estudiante,
    ev.cohorte,
    ev.ultimo_logro,
  CASE 
    WHEN ev.cohorte = '2020-1' AND ev.ultimo_logro = '9 SEMESTRE' THEN 'Dentro de Nivel'
    WHEN ev.cohorte = '2021-1' AND ev.ultimo_logro = '8 SEMESTRE' THEN 'Dentro de Nivel'
    WHEN ev.cohorte = '2021-2' AND ev.ultimo_logro = '7 SEMESTRE' THEN 'Dentro de Nivel'
    WHEN ev.cohorte = '2022-1' AND ev.ultimo_logro = '6 SEMESTRE' THEN 'Dentro de Nivel'
    WHEN ev.cohorte = '2022-2' AND ev.ultimo_logro = '5 SEMESTRE' THEN 'Dentro de Nivel'
    WHEN ev.cohorte = '2023-1' AND ev.ultimo_logro = '4 SEMESTRE' THEN 'Dentro de Nivel'
    WHEN ev.cohorte = '2023-2' AND ev.ultimo_logro = '3 SEMESTRE' THEN 'Dentro de Nivel'
    WHEN ev.cohorte = '2024-1' AND ev.ultimo_logro = '2 SEMESTRE' THEN 'Dentro de Nivel'
    WHEN ev.cohorte = '2024-2' AND ev.ultimo_logro = '1 SEMESTRE' THEN 'Dentro de Nivel'
    ELSE 'Fuera de Nivel'
    END AS estado_nivel
  FROM 
    EstudiantesVigentes ev
)
SELECT 
  estado_nivel,
  COUNT(*) AS cantidad_estudiantes
FROM 
  CohorteNivel
GROUP BY 
  estado_nivel;
