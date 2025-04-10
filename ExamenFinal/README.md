# Sistema de Gestión de Eventos

## Descripción General
Este sistema permite gestionar y visualizar eventos de manera dinámica, ofreciendo múltiples vistas y funcionalidades de búsqueda y filtrado.

## Estructura del Proyecto
```
/public
  ├── images/         # Directorio de imágenes
  ├── includes/       # Componentes reutilizables
  └── index.php      # Archivo principal de la aplicación
```

## Configuración de la Base de Datos
- **Host**: localhost
- **Base de Datos**: events_db
- **Codificación**: UTF-8 (utf8mb4)

### Tablas Principales
- `events`: Almacena la información de los eventos
- `categories`: Almacena las categorías de eventos

## Funcionalidades Principales

### 1. Visualización de Eventos
El sistema ofrece múltiples modos de visualización:
- **Lista**: Vista detallada con imágenes y descripción
- **Grid**: Visualización en cuadrícula
- **Tabla**: Formato tabular de datos
- **Mapa**: Visualización geográfica
- **Calendario**: Organización temporal

### 2. Búsqueda y Filtrado
- Búsqueda por texto en título y descripción
- Filtrado por categorías
- Combinación de búsqueda y filtros

### 3. Detalles del Evento
- Título y categoría
- Fecha y hora
- Descripción completa
- Ubicación con mapa interactivo
- Imagen del evento

## Manejo de Errores
El sistema implementa un manejo robusto de errores para:
- Conexión a la base de datos
- Ejecución de consultas
- Preparación de statements
- Obtención de resultados

## Características Técnicas

### Frontend
- Diseño responsivo con Tailwind CSS
- Soporte para modo oscuro
- Interfaz interactiva y amigable

### Backend
- PHP con MySQLi para gestión de base de datos
- Consultas preparadas para seguridad
- Manejo de sesiones y parámetros GET

### Seguridad
- Escape de datos HTML
- Prevención de inyección SQL
- Validación de parámetros de entrada

## Uso del Sistema

### Navegación
1. Seleccione el modo de visualización deseado
2. Utilice la barra de búsqueda para encontrar eventos específicos
3. Filtre por categoría usando el menú desplegable
4. Haga clic en "Ver Detalles" para información completa del evento

### Visualización de Eventos
- La vista lista muestra información detallada con imágenes
- La vista grid ofrece una presentación visual compacta
- El mapa muestra la ubicación geográfica de los eventos
- El calendario organiza los eventos por fecha

## Mantenimiento
Para mantener el sistema:
1. Realizar backups regulares de la base de datos
2. Verificar y actualizar las dependencias
3. Monitorear los logs de errores
4. Mantener actualizado el contenido de eventos