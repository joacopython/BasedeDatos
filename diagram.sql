CREATE TYPE estamento_enum AS ENUM ('Estudiante', 'Académico', 'Administrativo');
CREATE TYPE grado_academico_enum AS ENUM ('Licenciatura', 'Magíster', 'Doctor');
CREATE TYPE jerarquia_academica_enum AS ENUM ('Asistente', 'Asociado', 'Instructor', 'Titular', 'Sin Jerarquizar', 'Comisión Superior');
CREATE TYPE contrato_enum AS ENUM ('Full Time', 'Part Time', 'Honorario');
CREATE TYPE modalidad_enum AS ENUM('Presencial', 'Online', 'Híbrida');
CREATE TYPE caracter_enum AS ENUM('Mínimo', 'Taller', 'Electivo', 'CTI', 'CSI');
CREATE TYPE calificacion_enum AS ENUM ('SO', 'MB', 'B', 'SU', 'I', 'M', 'MM', 'P', 'NP', 'EX', 'A', 'R');
CREATE TYPE convocatoria_enum AS ENUM ('JUL', 'AGO', 'DIC', 'MAR');
CREATE TYPE jornada_enum AS ENUM ('VESPERTINO', 'DIURNO');



CREATE TABLE Persona (
    run INT,
    dv CHAR(1),
    nombres VARCHAR(100),
    apellido_paterno VARCHAR(50),
    apellido_materno VARCHAR(50),
    nombre_completo VARCHAR(150) GENERATED ALWAYS AS (nombres || \' \' || apellido_paterno || \' \' || apellido_materno) STORED,
    email_institucional VARCHAR(30) CHECK (email_institucional LIKE \'%@lamejor.cl\'),
    estamento estamento_enum,
    PRIMARY KEY (run),
    UNIQUE (email_institucional),
);


CREATE TABLE EmailPersonal (
    run INT,
    email_personal TEXT,
    PRIMARY KEY(run),
    FOREIGN KEY (run) REFERENCES Persona(run) DELETE ON CASCADE,
);

CREATE TABLE Telefono (
    run INT,
    telefono VARCHAR(30),
    PRIMARY KEY(run),
    FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE,
);

CREATE TABLE Estudiante (
    run INT,
    cohorte VARCHAR(30),
    numero_estudiante INT,
    fecha_logro VARCHAR(30),
    ultima_carga VARCHAR(30),
    PRIMARY KEY (numero_estudiante),
    FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE,
    UNIQUE (numero_estudiante)
);

CREATE TABLE Profesor (
    run INT,
    dedicacion INT,
    contrato TEXT,
    sede VARCHAR(30),
    nombre_carrera VARCHAR(30),
    grado_academico TEXT,
    jerarquia_academica TEXT,
    cargo TEXT,
    PRIMARY KEY (run),
    FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE
);

CREATE TABLE Jornada(
    run INT,
    jornada_diurna BOOLEAN,
    jornada_vespertina BOOLEAN,
    PRIMARY KEY (run),
    FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE
);

CREATE TABLE Administrativo (
    run INT,
    cargo VARCHAR(30),
    grado_academico VARCHAR(30),
    contrato TEXT,
    dedicacion INT,
    PRIMARY KEY (run),
    FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE
);

CREATE TABLE ExAlumno (
    run INT,
    numero_estudiante VARCHAR(10),
    titulo VARCHAR(30),
    PRIMARY KEY (numero_estudiante),
    FOREIGN KEY (run) REFERENCES Persona(run) ON DELETE CASCADE,
    UNIQUE (numero_estudiante)
);

CREATE TABLE PlanEstudio (
    codigo_plan VARCHAR(10),
    nombre_plan VARCHAR(100),
    duracion CHAR(1),
    inicio DATE,
    grado VARCHAR(50),
    sede VARCHAR(30),
    nombre_facultad VARCHAR(100),
    nombre_carrera VARCHAR(100),
    jornada jornada_enum,
    modalidad modalidad_enum,
    PRIMARY KEY (codigo_plan),
    FOREIGN KEY (nombre_facultad) REFERENCES Facultad(nombre_facultad)
);

CREATE TABLE Curso (
    sigla_curso VARCHAR(30),
    nombre_curso VARCHAR(100),
    ciclo VARCHAR(30),
    nivel INT,
    secciones INT,
    prerequisito CHAR(1),
    caracter caracter_enum,
    PRIMARY KEY (sigla_curso)
);

CREATE TABLE Facultad (
    nombre_facultad VARCHAR(50),
    codigo_departamento INT,
    PRIMARY KEY (nombre_facultad)
);


CREATE TABLE Departamento (
    codigo_departamento INT,
    nombre VARCHAR(30),
    PRIMARY KEY (codigo_departamento)
);

CREATE TABLE Salas(
    sala VARCHAR(30),
    vacantes INT,
    edificio VARCHAR(30),
    PRIMARY KEY (sala)
);

CREATE TABLE UltimoLogro(
    numero_estudiante INT,
    ultimo_logro TEXT,
    periodo VARCHAR(30),
    PRIMARY KEY(numero_estudiante),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante)
);

CREATE TABLE Bloqueo(
    numero_estudiante INT,
    bloqueo BOOLEAN,
    causal_bloqueo TEXT,
    PRIMARY KEY(numero_estudiante),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante),
);


CREATE TABLE HistorialAcademico (
    numero_estudiante INT,
    sigla_curso VARCHAR(30),
    periodo VARCHAR(30), 
    seccion INT,
    nota DECIMAL(3, 2),
    calificacion calificacion_enum, 
    convocatoria convocatoria_enum, 
    PRIMARY KEY (numero_estudiante, sigla_curso),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante),
    FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso)
);

CREATE TABLE OfertaAcademica (
    sigla_curso VARCHAR(30),
    run_profesor INT,
    sede VARCHAR(30),
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
    FOREIGN KEY (sala) REFERENCES Salas(sala) ON DELETE CASCADE
);


CREATE TABLE IncluyeCurso(
    codigo_plan VARCHAR(30),
    sigla_curso VARCHAR(30),
    PRIMARY KEY (codigo_plan, sigla_curso),
    FOREIGN KEY (codigo_plan) REFERENCES PlanEstudio(codigo_plan) ON DELETE CASCADE,
    FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso) ON DELETE CASCADE
);

CREATE TABLE InscripcionAlumno (
    numero_estudiante INT,
    codigo_plan VARCHAR(10),
    PRIMARY KEY (numero_estudiante, codigo_plan),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante) ON DELETE CASCADE,
    FOREIGN KEY (codigo_plan) REFERENCES PlanEstudio(codigo_plan) ON DELETE CASCADE
);


CREATE TABLE CursoEquivalente (
    sigla_curso_2 VARCHAR(30),
    sigla_curso_1 VARCHAR(30),
    PRIMARY KEY (sigla_curso_1, sigla_curso_2),
    FOREIGN KEY (sigla_curso_1) REFERENCES Curso(sigla_curso),
    FOREIGN KEY (sigla_curso_2) REFERENCES Curso(sigla_curso)
);

CREATE TABLE CursoPrerequisito (
        sigla_curso VARCHAR(30),
        prerequisito_1 VARCHAR(30),
        prerequisito_2 VARCHAR(30),
        PRIMARY KEY (sigla_curso, prerequisito_1),
        FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso),
        FOREIGN KEY (prerequisito_1) REFERENCES Curso(sigla_curso)
);
