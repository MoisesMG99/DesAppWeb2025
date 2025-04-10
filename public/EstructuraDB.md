# Estructura de la Base de Datos: Event Management System

## Información General

- **Nombre de la Base de Datos**: `events_db`
- **Host**: localhost
- **Usuario**: root
- **Contraseña**: root

## Tablas

### Tabla: `events`

Almacena información sobre los eventos.

#### Columnas:

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | INT | Identificador único del evento (clave primaria) |
| `title` | VARCHAR | Título del evento |
| `description` | TEXT | Descripción detallada del evento |
| `event_date` | DATETIME | Fecha y hora del evento |
| `location` | VARCHAR | Ubicación física del evento |
| `image_url` | VARCHAR | URL de la imagen asociada al evento (opcional) |
| `category_id` | INT | Identificador de la categoría (clave foránea a categories.id) |
| `latitude` | DECIMAL | Coordenada de latitud para la ubicación en el mapa |
| `longitude` | DECIMAL | Coordenada de longitud para la ubicación en el mapa |

### Tabla: `categories`

Almacena las categorías para clasificar los eventos.

#### Columnas:

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `id` | INT | Identificador único de la categoría (clave primaria) |
| `name` | VARCHAR | Nombre de la categoría |

## Relaciones

- Un evento pertenece a una categoría (`events.category_id` → `categories.id`)
- Una categoría puede tener múltiples eventos

## Vistas de la Aplicación

La aplicación permite visualizar los eventos en diferentes formatos:

1. **Vista de Lista**: Muestra los eventos en formato de lista vertical
2. **Vista de Cuadrícula**: Muestra los eventos en formato de tarjetas en cuadrícula
3. **Vista de Tabla**: Muestra los eventos en formato tabular
4. **Vista de Mapa**: Muestra los eventos en un mapa interactivo usando Leaflet
5. **Vista de Calendario**: Muestra los eventos en un calendario interactivo usando FullCalendar

## Funcionalidades

- Búsqueda de eventos por título o descripción
- Filtrado de eventos por categoría
- Visualización detallada de un evento específico
- Múltiples formatos de visualización (lista, cuadrícula, tabla, mapa, calendario)