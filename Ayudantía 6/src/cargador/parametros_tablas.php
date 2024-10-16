<?php

$path_tablas = array(
    'estudiantes' => '../data/Estudiantes.csv',
    'asignaturas' => '../data/Asignaturas.csv',
    'docentes planificados' => '../data/docentes planificados.csv',
    'notas' => '../data/Notas.csv',
    'planeacion' => '../data/Planeacion.csv',
    'planes' => '../data/Planes.csv',
    'prerrequisitos' => '../data/prerrequisitos.csv',
);



$tablas_iniciales = array(
    'Persona' => 
        'run INT,
        dv CHAR(1),
        nombres VARCHAR(100),
        apellido_paterno VARCHAR(50),
        apellido_materno VARCHAR(50),
        nombre_completo VARCHAR(150) GENERATED ALWAYS AS (nombres || \' \' || apellido_paterno || \' \' || apellido_materno) STORED,
        email_institucional VARCHAR(30) CHECK (email_institucional LIKE \'%@lamejor.cl\'),
        estamento estamento_enum,
        PRIMARY KEY (run),
        UNIQUE (email_institucional)',
    
    'EmailPersonal' =>
        'run INT,
        email_personal VARCHAR(30),
        PRIMARY KEY(run),
        FOREIGN KEY (run) REFERENCES Persona(run)',

    'Telefono' =>
        'run INT,
        telefono VARCHAR(30),
        PRIMARY KEY(run),
        FOREIGN KEY (run) REFERENCES Persona(run)',    
    
    'Estudiante' =>
        'run INT,
        cohorte VARCHAR(30),
        numero_estudiante INT,
        fecha_logro DATE,
        ultima_carga DATE,
        PRIMARY KEY (numero_estudiante),
        FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE,
        UNIQUE (numero_estudiante)',

    'UltimoLogro' =>
        'numero_estudiante INT,
        ultimo_logro VARCHAR(30),
        periodo VARCHAR(30),
        PRIMARY KEY(numero_estudiante),
        FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante)',

    'Bloqueo' =>
        'numero_estudiante INT,
        bloqueo BOOLEAN,
        causal_bloqueo VARCHAR(30),
        PRIMARY KEY(numero_estudiante),
        FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante)',

    'Profesor' =>
        'run INT,
        dedicacion INT,
        grado_academico grado_academico_enum,
        jerarquia_academica jerarquia_academica_enum,
        contrato contrato_enum,
        PRIMARY KEY (run),
        FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE',

    'Jornada' =>
        'run INT,
        jornada_diurna BOOLEAN,
        jornada_vespertina BOOLEAN,
        PRIMARY KEY (run),
        FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE',

    'Administrativo' =>
        'run INT,
        cargo VARCHAR(30),
        grado_academico VARCHAR(30),
        contrato contrato_enum,
        PRIMARY KEY (run),
        FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE',

    'ExAlumno' =>
        'run INT,
        numero_estudiante VARCHAR(10),
        titulo VARCHAR(30),
        PRIMARY KEY (numero_estudiante),
        FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE,
        UNIQUE (numero_estudiante)',

    'PlanEstudio' =>
        'codigo_plan VARCHAR(10),
        nombre_plan VARCHAR(100),
        duracion CHAR(1),
        inicio DATE,
        grado VARCHAR(50),
        sede VARCHAR(30),
        nombre_facultad VARCHAR(20),
        nombre_carrera VARCHAR(10),
        modalidad modalidad_enum,
        PRIMARY KEY (codigo_plan),
        FOREIGN KEY (nombre_facultad) REFERENCES Facultad(nombre_facultad)',

    'Curso' =>
        'sigla_curso VARCHAR(30),
        nombre_curso VARCHAR(100),
        ciclo VARCHAR(30),
        nivel INT,
        secciones INT,
        prerequisito CHAR(1),
        caracter caracter_enum,
        PRIMARY KEY (sigla_curso)',

    'HistorialAcademico' =>
        'numero_estudiante INT,
        sigla_curso VARCHAR(30),
        periodo VARCHAR(30),
        seccion INT,
        nota DECIMAL(3, 2),
        calificacion calificacion_enum, 
        convocatoria convocatoria_enum, 
        PRIMARY KEY (numero_estudiante, sigla_curso, seccion),
        FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso) ON DELETE CASCADE,
        FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante) ON DELETE CASCADE',

        
    'OfertaAcademica' =>
        'sigla_curso VARCHAR(30),
        run_profesor INT,
        seccion_curso VARCHAR(30),
        periodo VARCHAR(30),
        inscritos INT,
        duracion CHAR(1),
        fecha_fin VARCHAR(30),
        fecha_inicio VARCHAR(30),
        codigo_plan VARCHAR(30),
        hora_inicio VARCHAR(30),
        dia VARCHAR(30),
        horario_fin VARCHAR(30),
        sala VARCHAR(30),
        profesor_principal BOOLEAN,
        PRIMARY KEY (sigla_curso, seccion_curso, codigo_plan),
        FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso) ON DELETE CASCADE,
        FOREIGN KEY (run_profesor) REFERENCES Profesor(run) ON DELETE CASCADE,
        FOREIGN KEY (codigo_plan) REFERENCES PlanEstudio(codigo_plan) ON DELETE CASCADE,
        FOREIGN KEY (sala) REFERENCES Salas(sala) ON DELETE CASCADE',
        
    'Facultad' =>
        'nombre_facultad VARCHAR(20),
        codigo_departamento INT,
        PRIMARY KEY (nombre_facultad)',
    
    'Departamento' =>
        'codigo_departamento INT,
        nombre VARCHAR(30),
        PRIMARY KEY (codigo_departamento)',

    'Salas' =>
        'sala VARCHAR(30),
        vacantes INT,
        edificio VARCHAR(30),
        PRIMARY KEY (sala)',
    
    'IncluyeCurso' =>
        'codigo_plan VARCHAR(30),
        sigla_curso VARCHAR(30),
        PRIMARY KEY (codigo_plan, sigla_curso),
        FOREIGN KEY (codigo_plan) REFERENCES PlanEstudio(codigo_plan) ON DELETE CASCADE,
        FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso) ON DELETE CASCADE',

    'InscripcionAlumno' =>
        'numero_estudiante INT,
        codigo_plan VARCHAR(10),
        PRIMARY KEY (numero_estudiante, codigo_plan),
        FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante) ON DELETE CASCADE,
        FOREIGN KEY (codigo_plan) REFERENCES PlanEstudio(codigo_plan) ON DELETE CASCADE',

    'CursoEquivalente' =>
        'sigla_curso_2 VARCHAR(30),
        sigla_curso_1 VARCHAR(30),
        PRIMARY KEY (sigla_curso_1, sigla_curso_2),
        FOREIGN KEY (sigla_curso_1) REFERENCES Curso(sigla_curso),
        FOREIGN KEY (sigla_curso_2) REFERENCES Curso(sigla_curso)',

    'CursoPrerequisito' =>
        'sigla_curso VARCHAR(30),
        prerequisito VARCHAR(30),
        PRIMARY KEY (sigla_curso, prerequisito),
        FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso),
        FOREIGN KEY (prerequisito) REFERENCES Curso(sigla_curso)'
);

?>
