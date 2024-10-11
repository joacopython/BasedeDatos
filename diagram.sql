CREATE TYPE estamento_enum AS ENUM ('Estudiante', 'Académico', 'Administrativo');
CREATE TYPE grado_academico_enum AS ENUM ('Licenciatura', 'Magíster', 'Doctor');
CREATE TYPE jerarquia_academica_enum AS ENUM ('Asistente', 'Asociado', 'Instructor', 'Titular', 'Sin Jerarquizar', 'Comisión Superior');
CREATE TYPE jornada_enum AS ENUM ('Diurna', 'Vespertina', 'Ambas');
CREATE TYPE contrato_enum AS ENUM ('Full Time', 'Part Time', 'Honorario');
CREATE TYPE modalidad_enum AS ENUM('Presencial', 'Online', 'Híbrida');
CREATE TYPE caracter_enum AS ENUM('Mínimo', 'Taller', 'Electivo', 'CTI', 'CSI');
CREATE TYPE calificacion_enum AS ENUM ('SO', 'MB', 'B', 'SU', 'I', 'M', 'MM', 'P', 'NP', 'EX', 'A', 'R');
CREATE TYPE convocatoria_enum AS ENUM ('JUL', 'AGO', 'DIC', 'MAR');


CREATE TABLE Persona (
    run INT,
    dv CHAR(1),
    nombres VARCHAR(100),
    apellido_paterno VARCHAR(50),
    apellido_materno VARCHAR(50),
    nombre_completo AS (nombres || ' ' || apellido_paterno || ' ' || apellido_materno),
    email_institucional VARCHAR(30) CHECK (email_institucional LIKE '%@lamejor.cl'),
    estamento estamento_enum,
    PRIMARY KEY (run, dv),
    UNIQUE (email_institucional),
);

CREATE TABLE Email_personal (
    run INT,
    dv CHAR(1),
    email_personal VARCHAR(30),
    PRIMARY KEY(run, dv),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv)
);

CREATE TABLE Telefono (
    run INT,
    dv CHAR(1),
    telefono VARCHAR(30),
    PRIMARY KEY(run, dv),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv)
);


CREATE TABLE Estudiante (
    run INT,
    dv CHAR(1),
    cohorte VARCHAR(30),
    numero_estudiante INT,
    fecha_logro DATE,
    ultima_carga DATE,
    PRIMARY KEY (numero_estudiante),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv) ON DELETE CASCADE,
    UNIQUE (numero_estudiante)
);

CREATE TABLE UltimoLogro(
    numero_estudiante INT,
    ultimo_logro VARCHAR(30),
    periodo VARCHAR(30),
    PRIMARY KEY(numero_estudiante),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante)
);

CREATE TABLE Bloqueo(
    numero_estudiante INT,
    bloqueo BOOLEAN,
    causal_bloqueo VARCHAR(30),
    PRIMARY KEY(numero_estudiante),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante),
);

CREATE TABLE Profesor (
    run INT(30),
    dv CHAR(1),
    contrato VARCHAR(30),
    jornada jornada_enum,
    grado_academico grado_academico_enum,
    jerarquia_academica jerarquia_academica_enum,
    contrato contrato_enum,
    PRIMARY KEY (run, dv),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv) ON DELETE CASCADE
);


CREATE TABLE Administrativo (
    run INT(30),
    dv CHAR(1),
    cargo VARCHAR(30),
    grado_academico VARCHAR(30),
    contrato contrato_enum,
    PRIMARY KEY (run, dv),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv) ON DELETE CASCADE
);

CREATE TABLE ExAlumno (
    run INT(30),
    dv CHAR(1),
    numero_estudiante VARCHAR(10),
    titulo VARCHAR(30),
    PRIMARY KEY (numero_estudiante),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv) ON DELETE CASCADE,
    UNIQUE (numero_estudiante)
);


CREATE TABLE PlanEstudio (
  codigo_plan VARCHAR(10),
  nombre_plan VARCHAR(100),
  duracion INT,
  inicio DATE,
  grado VARCHAR(50),
  sede VARCHAR(30),
  nombre_facultad VARCHAR(20),
  codigo_carrera VARCHAR(10),
  jornada jornada_enum,
  modalidad modalidad_enum,
  PRIMARY KEY (codigo_plan),
  FOREIGN KEY (nombre_facultad) REFERENCES Facultad(nombre_facultad),
  FOREIGN KEY (codigo_carrera) REFERENCES Carrera(codigo_carrera)
);

CREATE TABLE Curso (
  codigo_plan VARCHAR(10),
  sigla_curso VARCHAR(10),
  nombre_curso VARCHAR(100),
  ciclo VARCHAR(30),
  nivel INT,
  secciones INT,
  caracter caracter_enum,
  PRIMARY KEY (sigla_curso),
  FOREIGN KEY (codigo_plan) REFERENCES Plan_de_Estudio(codigo_plan)
);


CREATE TABLE HistorialAcadémico (
    numero_estudiante INT,
    sigla_curso VARCHAR(30),
    periodo VARCHAR(30),
    seccion INT,
    nota DECIMAL(3, 2),
    calificacion calificacion_enum, 
    convocatoria convocatoria_enum, 
    PRIMARY KEY (numero_estudiante, sigla_curso, seccion),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante),
    FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso)
);


CREATE TABLE Facultad (
    nombre VARCHAR(20),
    codigo_departamento INT,
    PRIMARY KEY (nombre)
);

CREATE TABLE Carrera (
    nombre VARCHAR(20),
    PRIMARY KEY (nombre)
);

CREATE TABLE Departamento (
    codigo INT,
    nombre VARCHAR(30),
    PRIMARY KEY (codigo)
);


CREATE TABLE Oferta_Académica (
  codigo_plan VARCHAR(30),
  sigla_curso VARCHAR(30),
  seccion INT,
  fecha_inicio DATE,
  fecha_fin DATE,
  duracion CHAR(10),
  inscritos INT,
  hora_inicio VARCHAR(15),
  dia VARCHAR(15),
  hora_fin VARCHAR(15),
  PRIMARY KEY (seccion),
  FOREIGN KEY (codigo_plan) REFERENCES Plan_de_Estudio(codigo_plan),
  FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso)
);

CREATE TABLE Salas(
  sala VARCHAR(30),
  vacantes INT,
  edificio VARCHAR(30),
  PRIMARY KEY (sala)
);

CREATE TABLE Incluye_Curso(
    nombre_plan VARCHAR(30),
    codigo_plan VARCHAR(30),
    nombre_carrera VARCHAR(30),
    sigla_curso VARCHAR(30),
    PRIMARY KEY (nombre_plan, codigo_plan, nombre_carrera, sigla_curso),
    FOREIGN KEY (nombre_plan, codigo_plan, nombre_carrera) REFERENCES PlanEstudio(nombre, codigo,nombre_carrera) ON DELETE CASCADE,
    FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso)
    ON DELETE CASCADE
);

CREATE TABLE Inscripcion_Carrera (
    numero_estudiante INT,
    nombre_carrera VARCHAR(30),
    PRIMARY KEY (numero_estudiante, nombre_carrera),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante) ON DELETE CASCADE,
    FOREIGN KEY (nombre_carrera) REFERENCES Carrera(nombre_carrera) ON DELETE CASCADE
);

CREATE TABLE Oferta_Academica (
    sigla_curso VARCHAR(30),
    run_profesor INT,
    seccion_curso VARCHAR(30),
    inscritos INT,
    duracion CHAR(1),
    fecha_fin VARCHAR(30),
    fecha_inicio VARCHAR(30),
    codigo_plan INT,
    hora_inicio VARCHAR(30),
    dia VARCHAR(30),
    dv_profesor CHAR(1),
    horario_fin VARCHAR(30),
    sala VARCHAR(30),
    PRIMARY KEY (sigla_curso, seccion_curso, codigo_plan),
    FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso) ON DELETE CASCADE,
    FOREIGN KEY (run_profesor, dv_profesor) REFERENCES Profesor(run, dv) ON DELETE CASCADE,
    FOREIGN KEY (codigo_plan) REFERENCES PlanEstudio(codigo_plan) ON DELETE CASCADE,
    FOREIGN KEY (sala) REFERENCES Salas(sala) ON DELETE CASCADE
);

CREATE TABLE CursoEquivalente (
  sigla_curso_2 VARCHAR(10),
  sigla_curso_1 VARCHAR(10),
  PRIMARY KEY (sigla_curso_1, sigla_curso_2),
  FOREIGN KEY (sigla_curso_1) REFERENCES Curso(sigla_curso),
  FOREIGN KEY (sigla_curso_2) REFERENCES Curso(sigla_curso)
);

CREATE TABLE CursoPrerequisito 
  sigla_prerequisito VARCHAR(10),
  sigla_curso VARCHAR(10),
  PRIMARY KEY (sigla_curso, sigla_prerequisito),
  FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso),
  FOREIGN KEY (sigla_prerequisito) REFERENCES Curso(sigla_curso)
;
