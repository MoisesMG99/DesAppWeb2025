# CREACIÓN DE LA BASE DE DATOS
```sql
CREATE DATABASE IF NOT EXISTS events_db;
USE events_db;
```
# CREACIÓN DE TABLAS
```sql	
-- Tabla de categorías
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
)

-- Tabla de eventos
CREATE TABLE IF NOT EXISTS events (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  description TEXT,
  event_date DATETIME NOT NULL,
  location VARCHAR(255) NOT NULL,
  image_url VARCHAR(255),
  category_id INT NOT NULL,
  latitude DECIMAL(10,8),
  longitude DECIMAL(11,8),
  FOREIGN KEY (category_id) REFERENCES categories(id)
)
```
# DATOS DE EJEMPLO (OPCIONAL)
```sql
-- Insertar categorías de ejemplo
INSERT INTO categories (name) VALUES 
  ('Conferencia'),
  ('Taller'),
  ('Seminario'),
  ('Exposición'),
  ('Concierto');

-- Insertar eventos de ejemplo
INSERT INTO events (title, description, event_date, location, image_url, category_id, latitude, longitude) VALUES
  ('Conferencia de Desarrollo Web', 'Aprende las últimas tendencias en desarrollo web', '2023-12-15 10:00:00', 'Centro de Convenciones', 'images/conf1.jpg', 1, 40.4168, -3.7038),
  ('Taller de Diseño UX/UI', 'Taller práctico sobre diseño de experiencia de usuario', '2023-12-20 16:00:00', 'Campus Tecnológico', 'images/taller1.jpg', 2, 40.4169, -3.7035),
  ('Exposición de Arte Digital', 'Muestra de arte creado con tecnologías digitales', '2023-12-25 11:00:00', 'Galería Central', 'images/expo1.jpg', 4, 40.4170, -3.7040);