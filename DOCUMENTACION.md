# Documentación del Sistema de Tickets

## Índice
1. [Introducción](#introducción)
2. [Arquitectura del Sistema](#arquitectura-del-sistema)
3. [Modelos de Datos](#modelos-de-datos)
4. [Panel de Administración](#panel-de-administración)
5. [Roles y Permisos](#roles-y-permisos)
6. [Flujo de Trabajo de los Tickets](#flujo-de-trabajo-de-los-tickets)
7. [Funcionalidades Específicas](#funcionalidades-específicas)
8. [Personalización Visual](#personalización-visual)
9. [Integración con Correo Electrónico](#integración-con-correo-electrónico)
10. [Escalamiento de Tickets](#escalamiento-de-tickets)
11. [Reportes y Estadísticas](#reportes-y-estadísticas)
12. [Guía de Mantenimiento](#guía-de-mantenimiento)

## Introducción

Este sistema de tickets está diseñado para administrar solicitudes de soporte técnico y atención al cliente. Permite gestionar el ciclo de vida completo de los tickets, desde su creación hasta su resolución, asignándolos a diferentes departamentos y usuarios según sea necesario.

El sistema está construido con Laravel y Filament, utilizando una arquitectura MVC (Modelo-Vista-Controlador) que facilita su mantenimiento y expansión. La interfaz de usuario está optimizada para ofrecer una experiencia ágil tanto para los administradores como para el personal de soporte y los clientes.

## Arquitectura del Sistema

### Tecnologías Utilizadas
- **Backend**: Laravel 11
- **Panel de Administración**: Filament
- **Base de Datos**: MySQL/SQLite
- **Frontend**: Blade, Livewire, Alpine.js
- **CSS**: Tailwind CSS

### Estructura de Directorios Principales
- **app/Models/**: Modelos Eloquent que representan las entidades del sistema
- **app/Filament/**: Recursos, páginas y widgets del panel Filament
- **app/Http/Controllers/**: Controladores para manejar la lógica de negocio
- **app/Policies/**: Políticas para autorización y control de acceso
- **app/Providers/**: Proveedores de servicios para configurar el sistema
- **database/migrations/**: Migraciones para la estructura de la base de datos
- **resources/views/**: Vistas Blade para renderizar páginas HTML

## Modelos de Datos

### Ticket
El modelo central del sistema que almacena toda la información relacionada con las solicitudes de soporte.

**Atributos principales**:
- **id**: Identificador único del ticket
- **user_id**: Usuario que creó el ticket (cliente)
- **department_id**: Departamento al que está asignado el ticket
- **assigned_to**: Usuario (de soporte) al que está asignado el ticket
- **title**: Título descriptivo del ticket
- **description**: Descripción detallada del problema
- **status**: Estado actual (open, in_progress, closed)
- **priority**: Prioridad (low, medium, high)
- **source**: Origen del ticket (web, email, etc.)
- **closed_at**: Cuándo se cerró el ticket
- **closed_by**: Quién cerró el ticket

**Relaciones**:
- **user**: Pertenece a un Usuario (cliente)
- **department**: Pertenece a un Departamento
- **assignedTo**: Pertenece a un Usuario (soporte)
- **replies**: Tiene muchas Respuestas
- **attachments**: Tiene muchos Archivos Adjuntos
- **escalationLogs**: Tiene muchos Registros de Escalamiento
- **tags**: Pertenece a muchas Etiquetas
- **ratings**: Tiene muchas Valoraciones

### User
Representa a todos los usuarios del sistema, con diferentes roles.

**Atributos principales**:
- **id**: Identificador único del usuario
- **name**: Nombre completo
- **email**: Correo electrónico (usado para login)
- **role**: Rol del usuario (super_admin, admin, support, client)

**Relaciones**:
- **tickets**: Tiene muchos Tickets (creados como cliente)
- **assignedTickets**: Tiene muchos Tickets asignados (como soporte)
- **replies**: Tiene muchas Respuestas

### Department
Departamentos a los que pueden asignarse tickets.

**Atributos principales**:
- **id**: Identificador único
- **name**: Nombre del departamento
- **description**: Descripción del departamento
- **is_active**: Estado de activación

**Relaciones**:
- **tickets**: Tiene muchos Tickets

### Reply
Respuestas a los tickets, tanto de clientes como de personal de soporte.

**Atributos principales**:
- **id**: Identificador único
- **ticket_id**: Ticket al que pertenece
- **user_id**: Usuario que creó la respuesta
- **content**: Contenido de la respuesta
- **is_system**: Si es un mensaje automático del sistema

**Relaciones**:
- **ticket**: Pertenece a un Ticket
- **user**: Pertenece a un Usuario
- **attachments**: Tiene muchos Archivos Adjuntos

### Permission y RolePermission
Sistema flexible de permisos por rol.

**Permission - Atributos principales**:
- **id**: Identificador único
- **name**: Nombre técnico del permiso
- **display_name**: Nombre mostrado
- **description**: Descripción del permiso
- **category**: Categoría a la que pertenece

**RolePermission - Atributos principales**:
- **permission_id**: ID del permiso
- **role**: Rol al que se asigna el permiso

## Panel de Administración

El panel de administración está construido con Filament, proporcionando una interfaz moderna y responsive para gestionar todos los aspectos del sistema de tickets.

### Recursos Principales

#### TicketResource
Gestión completa de tickets, incluyendo:
- Listado con filtros avanzados
- Creación y edición
- Asignación a departamentos y usuarios
- Cambio de estado y prioridad
- Vista de previsualización para equipo de soporte
- Relación con respuestas

#### UserResource
Gestión de usuarios con diferentes roles:
- Clientes
- Personal de soporte
- Administradores
- Super administradores

#### DepartmentResource
Gestión de departamentos para categorizar tickets.

#### RolePermissionResource
Gestión de permisos por rol, permitiendo al super administrador controlar qué acciones pueden realizar los diferentes roles.

## Roles y Permisos

El sistema implementa un mecanismo flexible de control de acceso basado en roles.

### Roles Disponibles
1. **super_admin**: Acceso completo a todas las funcionalidades
2. **admin**: Administración general, con algunas limitaciones
3. **support**: Personal de soporte con acceso a tickets y respuestas
4. **client**: Clientes que pueden crear y ver sus propios tickets

### Sistema de Permisos
El módulo `RolePermissionResource` permite al super administrador gestionar qué permisos tiene cada rol, ofreciendo un control granular sobre quién puede hacer qué en el sistema.

Categorías de permisos:
- **tickets**: Relacionados con la gestión de tickets
- **users**: Relacionados con la gestión de usuarios
- **departments**: Relacionados con la gestión de departamentos
- **reports**: Relacionados con informes y estadísticas
- **settings**: Relacionados con la configuración del sistema

## Flujo de Trabajo de los Tickets

### Creación de Tickets
1. **Cliente**: Crea un ticket desde la interfaz web o por correo electrónico
2. **Sistema**: Asigna el ticket a un departamento según la configuración
3. **Estado**: El ticket comienza en estado "open" (abierto)

### Procesamiento de Tickets
1. **Visualización**: El personal de soporte puede ver todos los tickets abiertos
2. **Asignación**: Los tickets pueden ser tomados por un agente de soporte
3. **Interacción**: Tanto el personal como los clientes pueden añadir respuestas
4. **Estado**: El ticket pasa a "in_progress" (en progreso) cuando es tomado

### Cierre de Tickets
1. **Resolución**: El ticket se resuelve y se marca como "closed" (cerrado)
2. **Retroalimentación**: El cliente puede valorar la atención recibida
3. **Reapertura**: Un ticket cerrado puede reabrirse si es necesario

## Funcionalidades Específicas

### Vista de Previsualización para Soporte
El personal de soporte puede previsualizar los tickets sin editar su información base, permitiéndoles:
- Ver todos los detalles del ticket
- Ver el historial de respuestas
- Añadir respuestas
- Tomar/liberar el ticket
- Cerrar/reabrir el ticket

Implementado en: `TicketResource/Pages/PreviewTicket.php`

### Dashboard de Soporte
Panel específico para el personal de soporte con:
- Resumen de tickets abiertos
- Resumen de tickets en progreso
- Resumen de tickets cerrados por el agente actual
- Filtros de búsqueda avanzados
- Acceso rápido a tickets asignados

Implementado en: `SupportDashboardController.php` y `support/dashboard.blade.php`

### Sistema de Notificaciones
El sistema notifica a los usuarios relevantes sobre:
- Nuevos tickets
- Nuevas respuestas
- Cambios de estado
- Tickets escalados
- Menciones (@usuario)

### Archivos Adjuntos
Soporte para adjuntar archivos a tickets y respuestas, con:
- Validación de tipos de archivo
- Límites de tamaño
- Previsualización de imágenes
- Descarga segura

## Personalización Visual

### Tema Personalizado
El sistema utiliza un tema personalizado con el color principal de la marca (#fa4619) en:
- Botones de acción
- Enlaces
- Elementos seleccionados
- Insignias y elementos visuales

### Configuración de Colores
Los colores se configuran mediante:
1. **config/filament.php**: Configuración a nivel de aplicación
2. **resources/css/filament/admin/theme.css**: Estilos CSS personalizados
3. **app/Providers/FilamentPanelProvider.php**: Proveedor de panel personalizado

## Integración con Correo Electrónico

### Creación de Tickets por Correo
El sistema puede configurarse para crear tickets automáticamente a partir de correos recibidos.

### Notificaciones por Correo
Se envían notificaciones por correo electrónico para eventos importantes:
- Tickets nuevos
- Respuestas
- Cambios de estado
- Asignaciones

### Respuesta por Correo
Los usuarios pueden responder a un ticket directamente desde su cliente de correo.

## Escalamiento de Tickets

### Reglas de Escalamiento
El sistema permite configurar reglas para escalar tickets automáticamente basándose en:
- Tiempo sin respuesta
- Prioridad
- Departamento
- Cliente específico

### Registros de Escalamiento
Cada escalamiento se registra para seguimiento y análisis.

## Reportes y Estadísticas

### Métricas Disponibles
- Tiempo de respuesta promedio
- Tiempo de resolución promedio
- Distribución de tickets por estado
- Distribución de tickets por departamento
- Valoraciones de clientes
- Rendimiento de agentes

### Exportación
Los informes pueden exportarse en formatos:
- PDF
- Excel
- CSV

## Guía de Mantenimiento

### Actualización del Sistema
Para actualizar el sistema:
1. Realizar backup de datos
2. Actualizar el código (git pull)
3. Actualizar dependencias (composer update)
4. Ejecutar migraciones (php artisan migrate)
5. Limpiar caché (php artisan cache:clear)

### Personalización Adicional
Para personalizar más aspectos visuales:
1. Modificar **config/filament.php** para cambios a nivel de panel
2. Editar **resources/css/filament/admin/theme.css** para ajustes de CSS específicos
3. Publicar vistas de Filament para personalizaciones más profundas

### Seguridad
El sistema implementa múltiples capas de seguridad:
- Autenticación robusta
- Autorización basada en políticas
- Validación de entradas
- Protección contra CSRF
- Sanitización de salidas
