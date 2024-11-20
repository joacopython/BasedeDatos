<?php
include('../config/conexion.php');

try {
    $db->beginTransaction();

    $crearFuncionTrigger = "
        CREATE OR REPLACE FUNCTION calcular_calificacion()
        RETURNS TRIGGER AS $$
        BEGIN
            IF NEW.nota BETWEEN 6.6 AND 7.0 THEN
                NEW.calificacion := 'SO';
            ELSIF NEW.nota BETWEEN 6.0 AND 6.5 THEN
                NEW.calificacion := 'MB';
            ELSIF NEW.nota BETWEEN 5.0 AND 5.9 THEN
                NEW.calificacion := 'B';
            ELSIF NEW.nota BETWEEN 4.0 AND 4.9 THEN
                NEW.calificacion := 'SU';
            ELSIF NEW.nota BETWEEN 3.0 AND 3.9 THEN
                NEW.calificacion := 'I';
            ELSIF NEW.nota BETWEEN 2.0 AND 2.9 THEN
                NEW.calificacion := 'M';
            ELSIF NEW.nota BETWEEN 1.0 AND 1.9 THEN
                NEW.calificacion := 'MM';
            END IF;
            RETURN NEW;
        END;
        $$ LANGUAGE plpgsql;
    ";
    
    $db->exec($crearFuncionTrigger);
    echo "Función del trigger creada exitosamente.\n";

    $dropTrigger = "
    DROP TRIGGER IF EXISTS trigger_calcular_calificacion ON HistorialAcademico;
    ";
    $db->exec($dropTrigger);


    $crearTrigger = "
        CREATE TRIGGER trigger_calcular_calificacion
        BEFORE INSERT ON HistorialAcademico
        FOR EACH ROW
        EXECUTE FUNCTION calcular_calificacion();
    ";

    $db->exec($crearTrigger);
    echo "Trigger para calcular calificación creado exitosamente.\n";

    $db->commit();
} catch (Exception $e) {
    $db->rollBack();
    echo "Error al crear el trigger de calificación: " . $e->getMessage() . "\n";
}
?>