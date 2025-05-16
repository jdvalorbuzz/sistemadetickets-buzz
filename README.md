# Sistema de Gestión de Tickets

## Descripción Técnica

Este es un sistema de gestión de tickets avanzado desarrollado con Laravel 11 y Filament 3, diseñado para facilitar la comunicación estructurada entre clientes y personal de soporte. El sistema implementa una arquitectura MVC (Modelo-Vista-Controlador) completa con políticas de autorización RBAC (Role-Based Access Control) para la gestión integral del ciclo de vida de incidencias de soporte.

## Stack Tecnológico (Tech Stack)

### Backend
- **Framework Core**: Laravel v11.31
  - PHP >=8.2 requerido con extensiones: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
  - Arquitectura MVC (Modelo-Vista-Controlador) con enrutamiento RESTful
  - ORM Eloquent para mapeo objeto-relacional con migraciones
- **Capa de Persistencia**: 
  - Compatible con MySQL 8.0+ (producción recomendada)
  - SQLite 3.38.0+ (desarrollo y pruebas)
  - Esquema migracional v2 de Laravel con timestamps automáticos
  - Transacciones ACID garantizadas para operaciones críticas

### Frontend y Administración
- **Panel de Administración**: Filament v3.0
  - Basado en TALL Stack (Tailwind, Alpine.js, Laravel, Livewire)
  - Componentes UI dinamizados por Livewire 3 (estado reactivo sin necesidad de API)
  - Renderizado server-side con hidración frontend (SSR)
- **CSS Framework**: Tailwind CSS v3.4
  - Sistema de diseño utility-first con JIT (Just-In-Time) compiler
  - Theming personalizado a través de archivo de configuración tailwind.config.js

### Generación de Reportes
- **PDF**: Barryvdh/laravel-dompdf v3.1
  - Motor de renderizado HTML a PDF basado en DOMPDF
  - Soporte para CSS3, media queries y WebFonts
  - Renderizado mediante plantillas Blade con datos dinámicos
- **Excel/CSV**: Maatwebsite Excel v3.1
  - Utiliza internamente PhpSpreadsheet para operaciones de alto rendimiento
  - Exportación en memoria para optimizar recursos del servidor
  - Implementación de Queues para exportaciones asíncronas en segundo plano

### Herramientas de Desarrollo
- **Asistente de IDE**: Laravel IDE Helper para autocompletado y documentación
- **Control de Calidad**: PHP Code Sniffer con estándares PSR-12
- **Debugging**: Laravel Pail (v1.1) para manejo y visualización avanzada de logs

## Arquitectura del Sistema

### Diagrama de Componentes

```
+-------------------+      +-------------------+      +-------------------+
|                   |      |                   |      |                   |
|  Capa Presentación +<---->+  Capa Lógica     +<---->+  Capa Persistencia |
|  (UI/Filament)    |      |  (Controllers)    |      |  (Modelos/BD)     |
|                   |      |                   |      |                   |
+-------------------+      +-------------------+      +-------------------+
         ^                          ^                          ^
         |                          |                          |
         v                          v                          v
+-------------------+      +-------------------+      +-------------------+
|                   |      |                   |      |                   |
|  Políticas RBAC   +<---->+  Servicios        +<---->+  Exportación      |
|  (Autorización)   |      |  (Lógica Negocio)|      |  (Reportes)       |
|                   |      |                   |      |                   |
+-------------------+      +-------------------+      +-------------------+
```

### Modelos de Datos (Capa de Dominio)

El sistema utiliza el ORM Eloquent para mapear entidades de la base de datos a objetos PHP, facilitando operaciones CRUD sin necesidad de SQL directo.

#### `Department` (Categorías Departamentales)
- **Identificador**: Clave primaria autoincremental (`id`)
- **Atributos Principales**:
  - `name`: VARCHAR(255) UNIQUE, nombre completo del departamento
  - `code`: VARCHAR(20) UNIQUE, código corto para identificación rápida (ej. 'SOPORTE', 'DISENO')
  - `description`: TEXT, descripción detallada de las competencias del departamento
  - `color`: VARCHAR, código de color hexadecimal para identificación visual
  - `is_active`: BOOLEAN, indica si el departamento está activo para recibir tickets
  - `created_at`/`updated_at`: Timestamps autogestionados por Laravel

#### `Ticket` (Core Entity)
- **Identificador**: Clave primaria autoincremental (`id`)
- **Atributos Principales**: 
  - `user_id`: Clave foránea (FK) al usuario creador (CASCADE on delete)
  - `department_id`: FK al departamento asignado (SET NULL on delete)
  - `title`: VARCHAR(255), título descriptivo indexado para búsquedas rápidas
  - `description`: TEXT/LONGTEXT, permite uso de HTML para formato enriquecido
  - `status`: ENUM con valores predefinidos:
    - `open`: Ticket recién creado sin asignación
    - `in_progress`: En proceso por personal de soporte
    - `closed`: Resuelto y cerrado
    - `archived`: Archivado (no visible en listados predeterminados)
  - `priority`: ENUM('low', 'medium', 'high', 'urgent') para categorización
  - `closed_at`: TIMESTAMP NULL, registra momento exacto de cierre
  - `closed_by`: FK al usuario que ejecutó la acción de cierre
  - `created_at`/`updated_at`: Timestamps autogestionados por Laravel

- **Relaciones ORM**:
  - `user()`: BelongsTo - Relación inversa N:1 con User (creador)
  - `department()`: BelongsTo - Relación inversa N:1 con Department (categoría departamental)
  - `closedBy()`: BelongsTo - Relación inversa N:1 con User (quien cerró)
  - `replies()`: HasMany - Relación 1:N con Reply (respuestas al ticket)
  - `tags()`: BelongsToMany - Relación N:M con Tag (tabla pivot: ticket_tag)
  - `ratings()`: HasMany - Relación 1:N con TicketRating (valoraciones)
  - `attachments()`: HasMany - Relación 1:N con TicketAttachment (archivos)

- **Scopes Eloquent**:
  - `scopeOpen()`: Filtro rápido para tickets abiertos
  - `scopeInProgress()`: Filtro para tickets en proceso
  - `scopeClosed()`: Filtro para tickets cerrados
  - `scopeArchived()`: Filtro para tickets archivados

#### `User` (Entity con autenticación)
- **Atributos Principales**:
  - `name`: Nombre completo para display
  - `email`: Campo único (unique) utilizado para login
  - `password`: Hash Bcrypt (60 chars) con protección contra ataques de fuerza bruta
  - `role`: Enum implementado como string con tipos estrictos:
    - `super_admin`: Acceso completo incluyendo exportación de informes
    - `admin`: Gestión completa del sistema sin exportación
    - `support`: Solo puede responder tickets sin modificarlos
    - `client`: Usuario final que crea y consulta tickets

- **Métodos Helper**:
  - `isSuperAdmin()`: Boolean check para super administradores
  - `isAdmin()`: Boolean check para administrador o superior
  - `isClient()`: Boolean check para cliente
  - `isStaff()`: Boolean check para cualquier rol de personal interno

#### `Reply` (Entity dependiente)
- **Atributos Principales**:
  - `ticket_id`: FK a ticket padre (CASCADE on delete)
  - `user_id`: FK al autor de la respuesta
  - `content`: LONGTEXT con formato HTML procesado y sanitizado
  - `is_from_admin`: BOOLEAN para diferenciar respuestas de staff vs clientes
  - `attachment_count`: INTEGER para optimizar conteo de archivos adjuntos


> **Notas para Principiantes**: 
> - **FK (Foreign Key)**: Una referencia a otro registro en otra tabla, como un ID.
> - **ENUM**: Un tipo de dato con valores predefinidos, como elegir de una lista.
> - **CASCADE on delete**: Si se elimina un registro relacionado, elimina también los relacionados.
> - **ORM**: Object-Relational Mapping, permite trabajar con bases de datos usando objetos en lugar de SQL.

### Sistema de Autorización RBAC (Role-Based Access Control)

El sistema implementa una capa de autorización granular mediante el uso de Políticas (Policies) de Laravel, que controlan el acceso a recursos basado en roles.

#### `TicketPolicy` (Implementación de Gate Checks)

Clase responsable de aplicar reglas de autorización a las entidades de tipo Ticket siguiendo el patrón de autorización Policy-Gate.

```php
// Estructura básica de validación
public function methodName(User $user, Ticket $ticket): bool
{
    // Lógica de autorización
    return $conditionIsMet;
}
```

- **`viewAny(User $user): bool`**: 
  - **Implementación**: Retorna `true` incondicionalmente
  - **Efecto**: Todos los usuarios pueden acceder a la vista de listado
  - **Filtrado Dinámico**: El listado se filtra automáticamente via query builder según rol

- **`view(User $user, Ticket $ticket): bool`**: 
  - **Implementación**: 
  ```php
  if ($user->isStaff()) { return true; }
  return $user->id === $ticket->user_id;
  ```
  - **Lógica**: Personal puede ver cualquier ticket; clientes solo los propios
  - **Punto de validación**: Se ejecuta via middleware `can:view,ticket` en rutas de detalle

- **`create(User $user): bool`**: 
  - **Implementación**: Retorna `true` para todos los roles
  - **Restricción Adicional**: UI muestra campos diferentes según rol

- **`update(User $user, Ticket $ticket): bool`**:
  - **Matriz de Permisos**:
  
  | Rol | Condición | Resultado |
  |-----|------------|----------|
  | super_admin | Cualquiera | ✔ Permitido |
  | admin | Cualquiera | ✔ Permitido |
  | support | Cualquiera | ❌ Denegado |
  | client | owner && !closed | ✔ Permitido |
  | client | !owner || closed | ❌ Denegado |
  
  - **Consecuencia UI**: Personal de soporte solo ve botón "Responder" y "Cerrar", no "Editar"

- **Operaciones Destructivas** (`delete`, `restore`, `forceDelete`): 
  - **Implementación**: `return $user->isAdmin();`
  - **Seguridad**: Restringidas a administradores con notificación de operaciones críticas

> **Nota Técnica**: Las políticas son registradas automáticamente mediante convenciones de nombres en Laravel, pero también pueden registrarse manualmente en `AuthServiceProvider` para casos especiales.

### Sistema de Exportación de Reportes (ReportExportController)

Implementa el patrón Export Strategy para generar reportes en múltiples formatos con procesamiento compatible con diferentes motores de base de datos.

#### Arquitectura de Exportación

```
+------------------------+       +------------------+
|                        |       |                  |
| ReportExportController +------>+ getReportData()  +---+
|                        |       |                  |   |
+------------------------+       +------------------+   |
     |          |                                       |
     |          |                                       |
     v          v                                       v
+----------+ +----------+                      +------------------+
|          | |          |                      |                  |
| PDF      | | Excel/CSV|                      | Query Builder    |
| Strategy | | Strategy |                      | Adaptable        |
|          | |          |                      |                  |
+----------+ +----------+                      +------------------+
     |          |                                       |
     |          |                                       |
     v          v                                       v
+----------+ +----------+                      +------------------+
|          | |          |                      |                  |
| Blade    | | PHP      |                      | Database         |
| Template | | Array    |                      | Agnostic Logic   |
|          | |          |                      |                  |
+----------+ +----------+                      +------------------+
```

#### Métodos Principales y Funcionamiento

- **Protección de Acceso**: 
  ```php
  protected function checkSuperAdminAccess()
  {
      if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
          abort(403, 'Solo Super Administradores pueden descargar informes.');
      }
  }
  ```
  
- **`exportPdf()`**: 
  - **Seguridad**: Validación de permisos mediante `checkSuperAdminAccess()`
  - **Procesamiento**: Obtiene datos via `getReportData()` 
  - **Renderización**: Utiliza DOMPDF para convertir Blade a PDF
  - **Respuesta HTTP**: Content-Disposition: attachment con nombre dinámico y fecha

- **`exportExcel()`** y **`exportCsv()`**: 
  - **Implementación**: Utilizan `Maatwebsite\Excel\Facades\Excel`
  - **Clase Export**: Envía datos a `TicketsExport` que implementa `FromArray`, `WithHeadings`
  - **Optimización**: Usa `->download()` en vez de `->store()` para evitar I/O en disco

#### Algoritmos de Procesamiento de Datos

- **Tickets por Estado/Prioridad**: 
  - **Query**: Agrupación con `groupBy()` y `count(*)` 
  - **Optimización**: Indexación en columnas `status` y `priority`

- **Tiempo Promedio de Resolución**: 
  - **Base de Datos Agnóstica**: Evita funciones específicas como `TIMESTAMPDIFF`
  - **Algoritmo**: Diferencia de tiempo en PHP con `DateTime::diff()`
  - **Código**:
  ```php
  $ticketsWithClosedAt = Ticket::whereNotNull('closed_at')->get();
  foreach ($ticketsWithClosedAt as $ticket) {
      $created = new \DateTime($ticket->created_at);
      $closed = new \DateTime($ticket->closed_at);
      $interval = $created->diff($closed);
      $hours = ($interval->days * 24) + $interval->h + ($interval->i / 60);
      $totalHours += $hours;
  }
  ```

- **Tickets Mensuales**: 
  - **Agrupación y Formato**: Procesamiento manual en PHP con funciones de fecha
  - **Compatibilidad**: SQLite y MySQL sin dependencias de funciones específicas

### Características Técnicas Principales

#### Sistema de Categorías por Departamento

```
Cliente --> Selecciona Departamento --> Ticket asignado --> Personal del área correspondiente
```

- **Departamentos Predefinidos**:
  - Soporte Técnico (problemas técnicos, errores de sistema)
  - Atención al Cliente (consultas generales, información)
  - Diseño y Creatividad (solicitudes de materiales visuales)
  - Desarrollo de Software (nuevas funcionalidades, bugs)
  - Recursos Humanos (asuntos de personal)
  - Contabilidad y Finanzas (facturación, pagos)
  - Marketing (campañas publicitarias, redes sociales)
  - Operaciones (logística, procesos internos)

- **Personalización Visual**:
  - Cada departamento tiene un código de color asignado
  - Identificación visual rápida mediante badges con colores
  - Filtrado de tickets por departamento en tablas y reportes

- **Implementación**:
  - Modelo `Department` relacionado con `Ticket` (relación N:1)
  - Selector de departamento en el formulario de creación y edición de tickets
  - Validación para garantizar que cada ticket se asigne a un departamento activo
  - Índices optimizados para la búsqueda eficiente por departamento

#### Sistema de Respuestas con RichText Processing

```
Cliente/Staff --> RichEditor --> Sanitizador --> Almacenamiento --> Visualización Filtrada
```

- **Motor de RichText**: 
  - **Implementación**: Filament RichEditor Component `Forms\Components\RichEditor`
  - **Capacidades**: Formato HTML (negrita, cursiva, listas, enlaces, imágenes embebidas)
  - **Sanitización**: HTML Purifier integrado que elimina scripts y atributos peligrosos
  - **Toolbar API**: Configuración personalizada de botones disponibles
  ```php
  RichEditor::make('content')
    ->required()
    ->fileAttachmentsDisk('public')
    ->fileAttachmentsDirectory('reply-attachments')
    ->toolbarButtons([
        'blockquote', 'bold', 'bulletList', 'codeBlock', 
        'h2', 'h3', 'italic', 'link', 'orderedList', 'redo', 
        'strike', 'undo', 'attachFiles'
    ])
  ```

- **Filtrado Dinámico de Respuestas**:
  - **Implementación**: Query scope personalizado en RelationManager
  - **Algoritmo**: 
  ```php
  protected function getTableQuery(): Builder
  {
      $query = parent::getTableQuery();
      
      if (auth()->check() && auth()->user()->isClient()) {
          $query->where(function($query) {
              $query->where('user_id', auth()->id())
                    ->orWhere('is_from_admin', true);
          });
      }
      
      return $query;
  }
  ```
  - **Efecto**: Los clientes solo ven sus propias respuestas y las del staff, manteniendo la privacidad

- **Sistema de Adjuntos Optimizado**:
  - **Almacenamiento**: Configurable via `fileAttachmentsDisk('public')`
  - **Naming Strategy**: UUID v4 + timestamp para evitar colisiones
  - **Seguridad**: Validación de MIME types y tamaño máximo configurable
  - **Pruning Automático**: Cleanup de archivos temporales mediante jobs programados

#### Sistema RBAC Multinivel (Role-Based Access Control)

```
+-------------+       +-------------+       +-------------+
|             |       |             |       |             |
| super_admin +------>+    admin    +------>+   support   |
|             |       |             |       |             |
+-------------+       +-------------+       +-------------+
                                                  ^
                                                  |
                                                  v
                                          +-------------+
                                          |             |
                                          |   client    |
                                          |             |
                                          +-------------+
```

- **Implementación Técnica**: 
  - Columna `role` en tabla `users` con ENUM/STRING
  - Métodos helper en modelo (isAdmin, isSupport, etc.)
  - Policy Gates para autorización granular

- **Matriz de Capacidades por Rol**:

| Capacidad                  | super_admin | admin | support | client |
|----------------------------|-------------|-------|---------|--------|
| Ver todos los tickets      | ✔        | ✔    | ✔      | ❌     |
| Editar tickets             | ✔        | ✔    | ❌      | ❌     |
| Responder tickets          | ✔        | ✔    | ✔      | ✔     |
| Cerrar tickets             | ✔        | ✔    | ✔      | ❌     |
| Exportar reportes          | ✔        | ❌    | ❌      | ❌     |
| Ver respuestas de otros    | ✔        | ✔    | ✔      | ❌     |
| Ver reportes               | ✔        | ✔    | ✔      | ❌     |

#### Arquitectura de Cierre de Tickets

- **Separación de Concerns**:
  - Responder tickets y cerrarlos son acciones independientes
  - Implementación mediante actions separadas en Filament

- **Registro de Metadata**:
  - **Campos**: `closed_at` (timestamp) y `closed_by` (foreign key)
  - **Triggers**: Botón de cierre en UI que dispara `$ticket->update()`
  - **Código**:
  ```php
  public function close(): void
  {
      $ticket = $this->record;
        
      $ticket->update([
          'status' => 'closed',
          'closed_at' => now(),
          'closed_by' => auth()->id()
      ]);
        
      Notification::make()
          ->title('Ticket cerrado correctamente')
          ->success()
          ->send();
  }
  ```

- **Notificaciones Automáticas**:
  - Toast de confirmación via Filament
  - Opcionalmente: Email al cliente via Notification class

## Guía Técnica de Instalación y Configuración

### Requisitos del Sistema

| Componente | Versión Mínima | Recomendada | Notas |
|-----------|-----------------|------------|-------|
| **PHP** | 8.2.0 | 8.3+ | Con extensiones: `BCMath`, `Ctype`, `Fileinfo`, `JSON`, `Mbstring`, `OpenSSL`, `PDO`, `Tokenizer`, `XML` |
| **Composer** | 2.2.0 | 2.6+ | Gestor de dependencias PHP |
| **Base de Datos** | SQLite 3.38.0 / MySQL 5.7 | MySQL 8.0+ | Optimizado para MySQL en producción |
| **Node.js** | 16.x | 20.x LTS | Para compilación de assets |
| **NPM** | 8.x | 10.x | Gestor de paquetes JavaScript |
| **Memoria RAM** | 1GB | 2GB+ | Para operaciones de exportación |
| **Almacenamiento** | 256MB | 1GB+ | Sin contar adjuntos de usuarios |

### Proceso de Instalación (Paso a Paso)

1. **Clonar el Repositorio**
   ```bash
   # Usando HTTPS
   git clone https://github.com/usuario/tickets.git
   # O usando SSH
   git clone git@github.com:usuario/tickets.git
   
   # Entrar al directorio
   cd tickets
   ```
   > **Para principiantes**: Este comando copia todo el código del sistema desde un repositorio Git a tu computadora local.

2. **Instalar Dependencias del Backend**
   ```bash
   # Instalar dependencias con optimización para producción
   composer install --no-dev --optimize-autoloader
   
   # Para entorno de desarrollo (con herramientas de debug)
   composer install
   ```
   > **Para principiantes**: Este comando descarga todas las librerías PHP necesarias para el funcionamiento del sistema.

3. **Instalar Dependencias del Frontend**
   ```bash
   # Instalar paquetes NPM
   npm install
   
   # Alternativa usando Yarn
   yarn install
   ```
   > **Para principiantes**: Este comando descarga todos los componentes JavaScript y CSS necesarios para la interfaz de usuario.

4. **Configuración del Entorno**
   ```bash
   # Copiar archivo de configuración base
   cp .env.example .env
   
   # Generar clave de encriptación
   php artisan key:generate
   ```
   > **Para principiantes**: El archivo `.env` contiene todas las configuraciones personalizadas como contraseñas, nombres de bases de datos, etc. La clave generada protege datos sensibles como las sesiones.

5. **Configurar Base de Datos**

   Edita el archivo `.env` con tu editor preferido:
   
   **Para SQLite** (recomendado para pruebas rápidas):
   ```env
   DB_CONNECTION=sqlite
   DB_DATABASE=/ruta/absoluta/a/database.sqlite
   
   # Crear archivo de base de datos
   touch database/database.sqlite
   ```
   
   **Para MySQL** (recomendado para producción):
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=tickets
   DB_USERNAME=usuario
   DB_PASSWORD=contraseña
   ```
   > **Para principiantes**: Estas configuraciones le dicen al sistema cómo conectarse a tu base de datos. SQLite es un archivo único mientras que MySQL es un servidor de base de datos.

6. **Crear Estructura de Base de Datos**
   ```bash
   # Ejecutar migraciones (crear tablas)
   php artisan migrate
   
   # Opcional: Poblar con datos de prueba
   php artisan db:seed
   ```
   > **Para principiantes**: Las "migraciones" crean todas las tablas necesarias en la base de datos. Los "seeders" insertan datos de ejemplo para que puedas probar el sistema inmediatamente.

7. **Configurar Almacenamiento**
   ```bash
   # Crear enlace simbólico para archivos públicos
   php artisan storage:link
   
   # Establecer permisos adecuados en Linux/macOS
   chmod -R 775 storage bootstrap/cache
   ```
   > **Para principiantes**: Este paso permite que los archivos subidos por los usuarios (como imágenes) sean accesibles desde la web.

8. **Compilar Assets**
   ```bash
   # Compilar para desarrollo (con mapas de origen)
   npm run dev
   
   # Compilar para producción (optimizado)
   npm run build
   ```
   > **Para principiantes**: Este comando convierte los archivos CSS y JavaScript del proyecto en versiones optimizadas para el navegador.

9. **Iniciar Servidor de Desarrollo**
   ```bash
   # Iniciar servidor en localhost:8000
   php artisan serve
   
   # Opcional: Especificar host/puerto
   php artisan serve --host=0.0.0.0 --port=8080
   ```
   > **Para principiantes**: Esto inicia un servidor web local para que puedas acceder al sistema en tu navegador sin necesidad de instalar software adicional.

10. **Acceder al Sistema**
    - Abre tu navegador y visita: `http://localhost:8000`
    - Credenciales predeterminadas:
      - **Super Admin**: admin@example.com / password
      - **Cliente**: client@example.com / password
      - **Soporte**: support@example.com / password

### Configuración para Producción

```bash
# Optimizar autoloader
composer install --no-dev --optimize-autoloader

# Cachear configuración
php artisan config:cache

# Cachear rutas
php artisan route:cache

# Cachear vistas
php artisan view:cache
```

> **Para principiantes**: Estos comandos hacen que el sistema funcione más rápido en un servidor real, preparando todo para uso en producción.

## Consideraciones Técnicas y Arquitectónicas

### Compatibilidad de Bases de Datos

- **Abstracción de Funciones Específicas**:
  - El sistema implementa un patrón de adaptador para compatibilidad entre MySQL y SQLite
  - Evita el uso de funciones nativas como `TIMESTAMPDIFF()` (MySQL) o `julianday()` (SQLite)
  - Preferencia por cálculos en PHP para operaciones complejas de fecha/hora
  ```php
  // En lugar de SQL específico, usamos:
  $interval = $created->diff($closed);
  $hours = ($interval->days * 24) + $interval->h + ($interval->i / 60);
  ```

### Seguridad e Implementación

- **Protección CSRF**: Todas las solicitudes POST protegidas con tokens CSRF
- **XSS Prevention**: Sanitización de HTML en campos RichText mediante HTML Purifier
- **Middleware para Reportes**: Protección adicional para exportación:
  ```php
  protected function checkSuperAdminAccess()
  {
      if (!Auth::check() || !Auth::user()->isSuperAdmin()) {
          abort(403, 'Solo Super Administradores pueden descargar informes.');
      }
  }
  ```

### Renderizado PDF

- **Pipeline de Generación**:
  1. Datos recolectados vía `getReportData()`
  2. Renderizado a HTML mediante Blade (`exports.report-pdf`)
  3. Conversión de HTML a PDF vía DOMPDF
  4. Entrega como descarga o visualización inline

- **Optimización de Memoria**:
  - Procesamiento por lotes para grandes volúmenes de datos
  - Configuración personalizada de DOMPDF:
  ```php
  $dompdf->set_option('isRemoteEnabled', true);
  $dompdf->set_option('isHtml5ParserEnabled', true);
  ```

### Requisitos de Hosting

- **Shared Hosting**: Compatible con la mayoría de proveedores que soporten PHP 8.2+
- **VPS/Dedicado**: Rendimiento óptimo con NGINX + PHP-FPM
- **Docker**: Incluye Dockerfile y configuración docker-compose para despliegue containerizado

## Licencia

Este software está licenciado bajo la [Licencia MIT](LICENSE).

Copyright (c) 2025 Tickets Management System

Se concede permiso, de forma gratuita, a cualquier persona que obtenga una copia de este software y de los archivos de documentación asociados.
