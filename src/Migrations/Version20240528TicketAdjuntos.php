-- SQL para agregar soporte para tickets relacionados y archivos adjuntos

-- Agregar campos para los archivos adjuntos
ALTER TABLE ticket ADD adjunto_nombre VARCHAR(255) DEFAULT NULL;
ALTER TABLE ticket ADD updated_at DATETIME DEFAULT NULL;

-- Agregar relación para tickets relacionados
ALTER TABLE ticket ADD ticket_padre_id INT DEFAULT NULL;
ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3EF08A5 FOREIGN KEY (ticket_padre_id) REFERENCES ticket (id);
CREATE INDEX IDX_97A0ADA3EF08A5 ON ticket (ticket_padre_id);

-- Script para revertir los cambios (rollback) si es necesario
/*
ALTER TABLE ticket DROP FOREIGN KEY FK_97A0ADA3EF08A5;
DROP INDEX IDX_97A0ADA3EF08A5 ON ticket;
ALTER TABLE ticket DROP ticket_padre_id;
ALTER TABLE ticket DROP adjunto_nombre;
ALTER TABLE ticket DROP updated_at;
*/ 