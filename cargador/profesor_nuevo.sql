
CREATE TABLE Profesores (
  run INTEGER NOT NULL,
  nombre CHARACTER VARYING,
  apellido1 CHARACTER VARYING,
  apellido2 CHARACTER VARYING,
  sexo CHARACTER(1),
  jerarquizacion CHARACTER VARYING,
  telefono INTEGER,
  email_personal CHARACTER VARYING(128),
  email_institucional CHARACTER VARYING(128),
  dedicacion INTEGER,
  contrato CHARACTER VARYING,
  PRIMARY KEY (run)
);