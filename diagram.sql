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
    run VARCHAR(30) NOT NULL,
    dv CHAR(1) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellido_paterno VARCHAR(50) NOT NULL,
    apellido_materno VARCHAR(50),
    nombre_completo AS (nombres | ' ' | apellido_paterno | ' ' | apellido_materno),
    email_institucional VARCHAR(30) CHECK (email_institucional LIKE '%@lamejor.cl'),
    PRIMARY KEY (run, dv),
    UNIQUE (email_institucional)
    estamento estamento_enum,
);

CREATE TABLE Email_personal (
    run VARCHAR(30) NOT NULL,
    dv CHAR(1) NOT NULL,
    email_personal VARCHAR(30),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv),
);

CREATE TABLE Telefono (
    run VARCHAR(30) NOT NULL,
    dv CHAR(1) NOT NULL,
    telefono VARCHAR(30),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv),
);


CREATE TABLE Estudiante (
    run VARCHAR(30) NOT NULL,
    dv CHAR(1) NOT NULL,
    cohorte VARCHAR(30) NOT NULL,
    numero_estudiante INT NOT NULL,
    fecha_logro DATE,
    ultima_carga DATE,
    PRIMARY KEY (numero_estudiante),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv) ON DELETE CASCADE,
    UNIQUE (numero_estudiante)
);

--PRIMARYKEY?
CREATE TABLE UltimoLogro(
    numero_estudiante INT NOT NULL,
    ultimo_logro VARCHAR(30),
    periodo VARCHAR(30),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante),
);
--PRIMARYKEY?
CREATE TABLE Bloqueo(
    numero_estudiante VARCHAR(10),
    bloqueo BOOLEAN NOT NULL,
    causal_bloqueo VARCHAR(30),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante),
);



CREATE TABLE Profesor (
    run VARCHAR(30) NOT NULL,
    dv CHAR(1) NOT NULL,
    contrato VARCHAR(30),
    jornada jornada_enum,
    grado_academico grado_academico_enum,
    jerarquia_academica jerarquia_academica_enum,
    contrato contrato_enum,
    PRIMARY KEY (run, dv),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv)
    ON DELETE CASCADE
);


CREATE TABLE Administrativo (
    run VARCHAR(30) NOT NULL,
    dv CHAR(1) NOT NULL,
    cargo VARCHAR(30),
    grado_academico VARCHAR(30),
    contrato contrato_enum,
    PRIMARY KEY (run, dv),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv) ON DELETE CASCADE
);

CREATE TABLE ExAlumno (
    run VARCHAR(30) NOT NULL,
    dv CHAR(1) NOT NULL,
    numero_estudiante VARCHAR(10) NOT NULL,
    titulo VARCHAR(30),
    PRIMARY KEY (numero_estudiante),
    FOREIGN KEY (run, dv) REFERENCES Persona(run, dv) ON DELETE CASCADE,
    UNIQUE (numero_estudiante)
);



CREATE TABLE Topico (
    nombre_topico VARCHAR(30) NOT NULL,
    PRIMARY KEY (nombre_topico)
);


CREATE TABLE Investiga (
    run_profesor VARCHAR(30) NOT NULL,
    dv_profesor CHAR(1) NOT NULL,
    nombre_topico VARCHAR(30) NOT NULL,
    PRIMARY KEY (run_profesor, dv_profesor, nombre_topico),
    FOREIGN KEY (run_profesor, dv_profesor) REFERENCES
    Profesor(run, dv) ON DELETE CASCADE,
    FOREIGN KEY (nombre_topico) REFERENCES Topico(nombre)
    ON DELETE CASCADE
);

CREATE TABLE PlanEstudio (
  codigo_plan VARCHAR(10) NOT NULL,
  nombre_plan VARCHAR(100),
  duracion INT,
  inicio DATE,
  grado VARCHAR(50),
  sede VARCHAR(30),
  nombre_facultad VARCHAR(20),
  codigo_carrera VARCHAR(10),
  PRIMARY KEY (codigo_plan),
  FOREIGN KEY (nombre_facultad) REFERENCES Facultad(nombre_facultad),
  FOREIGN KEY (codigo_carrera) REFERENCES Carrera(codigo_carrera),
  jornada jornada_enum,
  modalidad modalidad_enum,
);

CREATE TABLE Curso (
  codigo_plan VARCHAR(10) NOT NULL,
  sigla_curso VARCHAR(10) NOT NULL,
  nombre_curso VARCHAR(100),
  ciclo VARCHAR(30),
  nivel INT,
  secciones INT,
  PRIMARY KEY (sigla_curso, nombre_curso),
  FOREIGN KEY (codigo_plan) REFERENCES Plan_de_Estudio(codigo_plan),
  caracter caracter_enum,
);


CREATE TABLE HistorialAcadémico (
    numero_estudiante INT NOT NULL,
    sigla_curso VARCHAR(30) NOT NULL,
    nombre_curso VARCHAR(30),
    periodo VARCHAR(30),
    seccion INT NOT NULL,
    nota DECIMAL(3, 2),
    calificacion calificacion_enum, 
    convocatoria convocatoria_enum, 
    PRIMARY KEY (numero_estudiante, sigla_curso, seccion),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante),
    FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso)
);


CREATE TABLE Facultad (
    nombre VARCHAR(20) NOT NULL,
    codigo_departamento INT,
    PRIMARY KEY (nombre)
);

CREATE TABLE Carrera (
    nombre VARCHAR(20) NOT NULL,
    PRIMARY KEY (nombre)
);

CREATE TABLE Departamento (
    codigo INT NOT NULL,
    nombre VARCHAR(30),
    PRIMARY KEY (codigo)
);




CREATE TABLE Oferta_Académica (
  codigo_plan VARCHAR(30) NOT NULL,
  sigla_curso VARCHAR(30) NOT NULL,--no habria que darle nombre_curso, sigla_curso?
  seccion INT NOT NULL,
  fecha_inicio DATE,
  fecha_fin DATE,
  duracion CHAR(10),
  inscritos INT,
  hora_inicio VARCHAR(15),
  dia VARCHAR(15),
  hora_fin VARCHAR(15),
  PRIMARY KEY (seccion),--no seria seccion, sigla_curso?
  FOREIGN KEY (codigo_plan) REFERENCES Plan_de_Estudio(codigo_plan),
  FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso)
);

CREATE TABLE Salas (
  sala VARCHAR(30) NOT NULL,
  sigla_curso VARCHAR(30) NOT NULL,--no habria que darle nombre_curso, sigla_curso?
  seccion INT NOT NULL,
  vacantes INT,
  edificio VARCHAR(30),
  PRIMARY KEY (sala),
  FOREIGN KEY (seccion) REFERENCES Oferta_Academica(seccion),
  FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso) 
);



CREATE TABLE Incluye_Curso(
    nombre_plan VARCHAR(30) NOT NULL,
    codigo_plan VARCHAR(30) NOT NULL,
    nombre_carrera VARCHAR(30) NOT NULL,
    sigla_curso VARCHAR(30) NOT NULL,
    nombre_curso VARCHAR(30) NOT NULL,
    PRIMARY KEY (nombre_plan, codigo_plan, nombre_carrera, sigla_curso),
    FOREIGN KEY (nombre_plan, codigo_plan, nombre_carrera) REFERENCES
    PlanEstudio(nombre, codigo,nombre_carrera) ON DELETE CASCADE,
    FOREIGN KEY (sigla_curso,  nombre_curso) REFERENCES Curso(sigla_curso, nombre)
    ON DELETE CASCADE
);


CREATE TABLE Inscripcion_Carrera (
    numero_estudiante VARCHAR(30) NOT NULL,
    nombre_carrera VARCHAR(30) NOT NULL,
    PRIMARY KEY (numero_estudiante, nombre_carrera),
    FOREIGN KEY (numero_estudiante) REFERENCES Estudiante(numero_estudiante)
    ON DELETE CASCADE,
    FOREIGN KEY (nombre_carrera) REFERENCES Curso(sigla_curso, nombre)
    ON DELETE CASCADE
);


CREATE TABLE Oferta_Academica (
    nombre_curso VARCHAR(30) NOT NULL,
    sigla_curso VARCHAR(30) NOT NULL,
    run_profesor VARCHAR(30) NOT NULL,
    seccion_curso VARCHAR(30) NOT NULL,
    periodo_curso VARCHAR(30) NOT NULL,
    dv_profesor CHAR(1) NOT NULL,
    horario VARCHAR(30) NOT NULL,
    sala VARCHAR(30) NOT NULL,
    PRIMARY KEY (sigla_curso, nombre_curso, run_profesor,
    dv_profesor, seccion_curso, periodo_curso, horario),
    FOREIGN KEY (sigla_curso, nombre_curso) REFERENCES Curso(sigla_curso, nombre)
    ON DELETE CASCADE,
    FOREIGN KEY (run_profesor, dv_profesor) REFERENCES Profesor(run, dv)
    ON DELETE CASCADE
);

CREATE TABLE CursoEquivalente (
  sigla_curso_1 VARCHAR(10) NOT NULL,--no habría que darle no,bre curso tambien?
  sigla_curso_2 VARCHAR(10) NOT NULL,
  PRIMARY KEY (sigla_curso_1, sigla_curso_2),
  FOREIGN KEY (sigla_curso_1) REFERENCES Curso(sigla_curso),
  FOREIGN KEY (sigla_curso_2) REFERENCES Curso(sigla_curso)
);

CREATE TABLE CursoPrerequisito (
  sigla_curso VARCHAR(10) NOT NULL,--no habría que darle no,bre curso tambien?
  sigla_prerequisito VARCHAR(10) NOT NULL,
  PRIMARY KEY (sigla_curso, sigla_prerequisito),
  FOREIGN KEY (sigla_curso) REFERENCES Curso(sigla_curso),
  FOREIGN KEY (sigla_prerequisito) REFERENCES Curso(sigla_curso)
);

