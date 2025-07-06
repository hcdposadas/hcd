# Funcionalidad de Tickets Relacionados y Adjuntos

Este documento describe los cambios implementados para agregar soporte para tickets relacionados y archivos adjuntos en el sistema de tickets.

## Resumen de Cambios

1. **Entidad Ticket**: 
   - Relación auto-referencial para tickets padre/hijo
   - Soporte para archivos adjuntos mediante VichUploaderBundle

2. **Formularios**:
   - Selector de tickets relacionados
   - Campo para cargar archivos adjuntos

3. **Vistas**:
   - Visualización de relaciones entre tickets
   - Enlaces a archivos adjuntos
   - Botones para crear tickets relacionados

## Migración de Base de Datos

Para implementar estos cambios, es necesario ejecutar la siguiente migración:

### Opción 1: Usando la clase de migración

```bash
# Ejecutar la migración (cuando se desee aplicar los cambios)
php bin/console doctrine:migrations:migrate
```

### Opción 2: Ejecutar SQL directamente

Ejecutar el siguiente SQL en la base de datos:

```sql
-- Agregar campos para los archivos adjuntos
ALTER TABLE ticket ADD adjunto_nombre VARCHAR(255) DEFAULT NULL;
ALTER TABLE ticket ADD updated_at DATETIME DEFAULT NULL;

-- Agregar relación para tickets relacionados
ALTER TABLE ticket ADD ticket_padre_id INT DEFAULT NULL;
ALTER TABLE ticket ADD CONSTRAINT FK_97A0ADA3EF08A5 FOREIGN KEY (ticket_padre_id) REFERENCES ticket (id);
CREATE INDEX IDX_97A0ADA3EF08A5 ON ticket (ticket_padre_id);
```

## Directorio para Archivos Adjuntos

Se ha creado el directorio para almacenar los archivos adjuntos:

```bash
mkdir -p public/uploads/tickets/adjuntos
chmod 777 public/uploads/tickets/adjuntos
```

## Funcionalidades Implementadas

### 1. Crear un Ticket con Relación

Al crear un nuevo ticket, se puede seleccionar un ticket existente como "ticket padre".

### 2. Crear un Ticket Relacionado desde un Ticket Existente

En las vistas de tickets, hay un botón para crear directamente un ticket relacionado con el ticket actual.

### 3. Ver Tickets Relacionados

En las vistas de tickets, se muestran las relaciones existentes:
- Si el ticket tiene un padre, se muestra un enlace a ese ticket
- Si el ticket tiene tickets hijos, se muestran enlaces a esos tickets

### 4. Archivos Adjuntos

Al crear o editar un ticket, se puede adjuntar un archivo (PDF, DOC, DOCX, JPG, PNG, GIF). 