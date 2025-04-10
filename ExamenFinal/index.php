<?php include 'includes/_header.php'; ?>
    <?php
    // Datos utilizados para la conexión a la base de datos
    $host = 'localhost';
    $dbname = 'events_db';
    $user = 'root';
    $pass = 'root';

    // Establecer la conexión con la base de datos
    $conn = mysqli_connect($host, $user, $pass, $dbname);

    // Verificar conexión
    if (mysqli_connect_errno()) {
        echo "Error de Conexión a la Base de Datos: " . mysqli_connect_error();
        die(); // Terminar script si la conexión falla
    }

    // Establecer conjunto de caracteres a UTF-8 (recomendado)
    mysqli_set_charset($conn, "utf8mb4");

    // Obtener el modo de vista actual (lista, cuadrícula, tabla, mapa, calendario)
    $view_mode = isset($_GET['view']) ? $_GET['view'] : 'list';

    // Obtener consulta de búsqueda si está presente
    $search_query = isset($_GET['search']) ? $_GET['search'] : '';

    // Obtener filtro de categoría si está presente
    $category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;

    // Obtener ID del evento para vista detallada
    $event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

    // Obtener categorías para el filtro usando MySQLi
    $categories_result = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");
    $categories = [];
    if ($categories_result) {
        $categories = mysqli_fetch_all($categories_result, MYSQLI_ASSOC);
        mysqli_free_result($categories_result); // Liberar conjunto de resultados
    } else {
        echo "Error al obtener categorías: " . mysqli_error($conn);
        // Manejar el error apropiadamente, tal vez registrarlo o mostrar un mensaje amigable al usuario
    }


    // Consulta base para eventos
    $query = "SELECT e.*, c.name as category_name
              FROM events e
              LEFT JOIN categories c ON e.category_id = c.id
              WHERE 1";
    $params = [];
    $types = ''; // Cadena para almacenar tipos de parámetros para mysqli_stmt_bind_param
    
    // Agregar condición de búsqueda si se proporciona consulta
    if (!empty($search_query)) {
        $query .= " AND (e.title LIKE ? OR e.description LIKE ?)";
        $search_param = "%$search_query%";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= 'ss'; // Agregar 's' para tipo string dos veces
    }
    
    // Agregar filtro de categoría si está seleccionado
    if ($category_filter > 0) {
        $query .= " AND e.category_id = ?";
        $params[] = $category_filter;
        $types .= 'i'; // Agregar 'i' para tipo entero
    }
    
    // Agregar ordenamiento por fecha
    $query .= " ORDER BY e.event_date";

    // Prepare and execute query using MySQLi prepared statements
    $stmt = mysqli_prepare($conn, $query);
    $events = [];

    if ($stmt) {
        // Bind parameters dynamically if there are any
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params); // Use '...' splat operator
        }

        // Execute the statement
        if (mysqli_stmt_execute($stmt)) {
            // Get the result set
            $result = mysqli_stmt_get_result($stmt);
            if ($result) {
                // Fetch all events
                $events = mysqli_fetch_all($result, MYSQLI_ASSOC);
                mysqli_free_result($result); // Free result set
            } else {
                 echo "Error getting result: " . mysqli_error($conn);
            }
        } else {
            echo "Error executing statement: " . mysqli_stmt_error($stmt);
        }
        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo "Error preparing statement: " . mysqli_error($conn);
    }
    ?>

    <div class="container mx-auto px-4 py-8">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 dark:text-white mb-4">Sistema de Gestión de Eventos</h1>

            <div class="flex flex-col md:flex-row items-center justify-between space-y-4 md:space-y-0 mb-6">
                <form action="" method="GET" class="w-full md:w-auto flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    <input type="hidden" name="view" value="<?php echo htmlspecialchars($view_mode); ?>">
                    <div class="flex-grow">
                        <input type="text" name="search" placeholder="Buscar eventos"
                               value="<?php echo htmlspecialchars($search_query); ?>"
                               class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-base">
                    </div>
                    <div class="w-full md:w-auto">
                        <select name="category" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-base">
                            <option value="0">Todas las categorías</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <button type="submit" class="w-full md:w-auto px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Search
                        </button>
                    </div>
                </form>

                <div class="flex space-x-2">
                    <?php
                    // Helper function to build query string for view links
                    function build_view_link_params($search_query, $category_filter) {
                        $link_params = '';
                        if (!empty($search_query)) {
                            $link_params .= '&search=' . urlencode($search_query);
                        }
                        if ($category_filter > 0) {
                            $link_params .= '&category=' . $category_filter;
                        }
                        return $link_params;
                    }
                    $link_params = build_view_link_params($search_query, $category_filter);
                    ?>
                    <a href="?view=list<?php echo $link_params; ?>"
                       class="px-4 py-2 rounded-lg <?php echo $view_mode == 'list' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                        Lista
                    </a>
                    <a href="?view=grid<?php echo $link_params; ?>"
                       class="px-4 py-2 rounded-lg <?php echo $view_mode == 'grid' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                        Grid
                    </a>
                    <a href="?view=table<?php echo $link_params; ?>"
                       class="px-4 py-2 rounded-lg <?php echo $view_mode == 'table' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                        Tabla
                    </a>
                    <a href="?view=map<?php echo $link_params; ?>"
                       class="px-4 py-2 rounded-lg <?php echo $view_mode == 'map' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                        Mapa
                    </a>
                    <a href="?view=calendar<?php echo $link_params; ?>"
                       class="px-4 py-2 rounded-lg <?php echo $view_mode == 'calendar' ? 'bg-indigo-600 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'; ?>">
                        Calendario
                    </a>
                </div>
            </div>
        </header>

        <main>
            <?php if ($event_id > 0): ?>
                <?php
                // Fetch single event details using MySQLi prepared statement
                $event = null;
                $stmt_detail = mysqli_prepare($conn, "SELECT e.*, c.name as category_name
                                                     FROM events e
                                                     LEFT JOIN categories c ON e.category_id = c.id
                                                     WHERE e.id = ?");
                if ($stmt_detail) {
                    mysqli_stmt_bind_param($stmt_detail, "i", $event_id);
                    if (mysqli_stmt_execute($stmt_detail)) {
                        $result_detail = mysqli_stmt_get_result($stmt_detail);
                        if ($result_detail) {
                            $event = mysqli_fetch_assoc($result_detail); // Fetch single row
                            mysqli_free_result($result_detail);
                        } else {
                             echo "Error getting single event result: " . mysqli_error($conn);
                        }
                    } else {
                         echo "Error executing single event statement: " . mysqli_stmt_error($stmt_detail);
                    }
                    mysqli_stmt_close($stmt_detail);
                } else {
                     echo "Error preparing single event statement: " . mysqli_error($conn);
                }

                if ($event):
                ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2"><?php echo htmlspecialchars($event['title']); ?></h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        <span class="inline-block bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full px-3 py-1 text-sm font-semibold mr-2">
                                            <?php echo htmlspecialchars($event['category_name'] ?? 'Uncategorized'); ?>
                                        </span>
                                        <span>
                                            <?php echo date('F j, Y - g:i A', strtotime($event['event_date'])); ?>
                                        </span>
                                    </p>
                                </div>
                                <a href="?view=<?php echo htmlspecialchars($view_mode); ?><?php echo $link_params; ?>"
                                   class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300">
                                    Volver a eventos
                                </a>
                            </div>

                            <?php if (!empty($event['image_url'])): ?>
                                <div class="mb-6">
                                    <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="<?php echo htmlspecialchars($event['title']); ?>" class="w-full h-64 object-cover rounded-lg">
                                </div>
                            <?php endif; ?>

                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Description</h3>
                                <p class="text-gray-700 dark:text-gray-300"><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                            </div>

                            <div class="mb-6">
                                <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-2">Location</h3>
                                <p class="text-gray-700 dark:text-gray-300 mb-4"><?php echo htmlspecialchars($event['location']); ?></p>

                                <?php if (!empty($event['latitude']) && !empty($event['longitude'])): ?>
                                    <div id="detailMap" class="h-64 rounded-lg"></div>
                                    <script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            // Check if Leaflet is loaded
                                            if (typeof L !== 'undefined') {
                                                const map = L.map('detailMap').setView([<?php echo $event['latitude']; ?>, <?php echo $event['longitude']; ?>], 15);

                                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                                }).addTo(map);

                                                L.marker([<?php echo $event['latitude']; ?>, <?php echo $event['longitude']; ?>])
                                                    .addTo(map)
                                                    .bindPopup("<?php echo htmlspecialchars(addslashes($event['title'])); // Use addslashes for JS strings ?>")
                                                    .openPopup();
                                            } else {
                                                console.error('Leaflet library not loaded.');
                                                // Optionally display a message to the user
                                                document.getElementById('detailMap').innerHTML = '<p class="text-red-500">Map could not be loaded.</p>';
                                            }
                                        });
                                    </script>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        Evento no encontrado
                    </div>
                <?php endif; ?>

            <?php else: // Show event list/grid/table/map/calendar ?>
                <?php if ($view_mode == 'list'): ?>
                    <div class="space-y-6">
                        <?php if (empty($events)): ?>
                            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                               Eventos no econtrados según el criterio
                            </div>
                        <?php endif; ?>

                        <?php foreach ($events as $event): ?>
                            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-md transition-shadow duration-300 overflow-hidden flex flex-col md:flex-row">
                                <?php if (!empty($event['image_url'])): ?>
                                    <div class="md:w-1/4 h-48 md:h-auto">
                                        <img src="<?php echo htmlspecialchars($event['image_url']); ?>"
                                             alt="<?php echo htmlspecialchars($event['title']); ?>"
                                             class="w-full h-full object-cover">
                                    </div>
                                <?php endif; ?>
                                <div class="p-6 flex-1">
                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                        <a href="?view=<?php echo htmlspecialchars($view_mode); ?>&event_id=<?php echo $event['id']; ?><?php echo $link_params; ?>"
                                           class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                            <?php echo htmlspecialchars($event['title']); ?>
                                        </a>
                                    </h2>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                        <span class="inline-block bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full px-3 py-1 text-sm font-semibold mr-2">
                                            <?php echo htmlspecialchars($event['category_name'] ?? 'Uncategorized'); ?>
                                        </span>
                                        <span>
                                            <?php echo date('F j, Y - g:i A', strtotime($event['event_date'])); ?>
                                        </span>
                                    </p>
                                    <p class="text-gray-700 dark:text-gray-300 mb-4">
                                        <?php echo substr(htmlspecialchars($event['description']), 0, 150) . (strlen($event['description']) > 150 ? '...' : ''); ?>
                                    </p>
                                    <div class="flex justify-between items-center">
                                        <div class="text-gray-600 dark:text-gray-400">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1113.314-13.314 8 8 0 010 11.314z" />
                                            </svg>
                                            <?php echo htmlspecialchars($event['location']); ?>
                                        </div>
                                        <a href="?view=<?php echo htmlspecialchars($view_mode); ?>&event_id=<?php echo $event['id']; ?><?php echo $link_params; ?>"
                                           class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                            Ver Detalles
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                <?php elseif ($view_mode == 'grid'): ?>
                    <?php if (empty($events)): ?>
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            No se han encontrado eventos según el criterio
                        </div>
                    <?php else: ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($events as $event): ?>
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-300 overflow-hidden flex flex-col h-full">
                                    <?php if (!empty($event['image_url'])): ?>
                                        <div class="h-48 overflow-hidden">
                                            <img src="<?php echo htmlspecialchars($event['image_url']); ?>"
                                                 alt="<?php echo htmlspecialchars($event['title']); ?>"
                                                 class="w-full h-full object-cover transition-transform duration-300 hover:scale-105">
                                        </div>
                                    <?php endif; ?>
                                    <div class="p-6 flex-1 flex flex-col">
                                        <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                                            <a href="?view=<?php echo htmlspecialchars($view_mode); ?>&event_id=<?php echo $event['id']; ?><?php echo $link_params; ?>"
                                               class="hover:text-indigo-600 dark:hover:text-indigo-400">
                                                <?php echo htmlspecialchars($event['title']); ?>
                                            </a>
                                        </h2>
                                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                                            <span class="inline-block bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full px-3 py-1 text-sm font-semibold mr-2">
                                                <?php echo htmlspecialchars($event['category_name'] ?? 'Uncategorized'); ?>
                                            </span>
                                            <span>
                                                <?php echo date('F j, Y', strtotime($event['event_date'])); ?>
                                            </span>
                                        </p>
                                        <p class="text-gray-700 dark:text-gray-300 mb-4 flex-1">
                                            <?php echo substr(htmlspecialchars($event['description']), 0, 100) . (strlen($event['description']) > 100 ? '...' : ''); ?>
                                        </p>
                                        <div class="flex justify-between items-center mt-auto">
                                            <div class="text-gray-600 dark:text-gray-400 text-sm truncate mr-2">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1113.314-13.314 8 8 0 010 11.314z" />
                                                </svg>
                                                <?php echo htmlspecialchars($event['location']); ?>
                                            </div>
                                            <a href="?view=<?php echo htmlspecialchars($view_mode); ?>&event_id=<?php echo $event['id']; ?><?php echo $link_params; ?>"
                                               class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium">
                                                Ver
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php elseif ($view_mode == 'table'): ?>
                    <?php if (empty($events)): ?>
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                            Eventos no encontrados
                        </div>
                    <?php else: ?>
                        <div class="overflow-x-auto bg-white dark:bg-gray-800 rounded-lg shadow">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Título
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Categoría
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Fecha y Hora
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Localización
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Acción
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <?php foreach ($events as $event): ?>
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    <?php echo htmlspecialchars($event['title']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="inline-block bg-indigo-100 dark:bg-indigo-900 text-indigo-800 dark:text-indigo-200 rounded-full px-3 py-1 text-xs font-semibold">
                                                    <?php echo htmlspecialchars($event['category_name'] ?? 'Uncategorized'); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                                    <?php echo date('M j, Y', strtotime($event['event_date'])); ?>
                                                </div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    <?php echo date('g:i A', strtotime($event['event_date'])); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-700 dark:text-gray-300">
                                                    <?php echo htmlspecialchars($event['location']); ?>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <a href="?view=<?php echo htmlspecialchars($view_mode); ?>&event_id=<?php echo $event['id']; ?><?php echo $link_params; ?>"
                                                   class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                                    Ver Detalles
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                <?php elseif ($view_mode == 'map'): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div id="mapView" class="h-96 w-full rounded-lg"></div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Check if Leaflet is loaded
                                if (typeof L !== 'undefined') {
                                    const map = L.map('mapView').setView([0, 0], 2); // Default view

                                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                    }).addTo(map);

                                    const events = <?php echo json_encode($events); ?>;
                                    const markers = [];
                                    const bounds = [];

                                    events.forEach(event => {
                                        if (event.latitude && event.longitude) {
                                            const lat = parseFloat(event.latitude);
                                            const lon = parseFloat(event.longitude);
                                            if (!isNaN(lat) && !isNaN(lon)) { // Ensure coordinates are valid numbers
                                                const marker = L.marker([lat, lon])
                                                    .addTo(map)
                                                    .bindPopup(`
                                                        <div class="text-center p-1">
                                                            <h3 class="font-bold text-base mb-1">${event.title.replace(/'/g, "\\'")}</h3>
                                                            <p class="text-xs mb-2">${new Date(event.event_date).toLocaleDateString()} - ${new Date(event.event_date).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'})}</p>
                                                            <a href="?view=<?php echo htmlspecialchars($view_mode); ?>&event_id=${event.id}<?php echo $link_params; ?>"
                                                               class="text-indigo-600 hover:underline text-sm">
                                                                View Details
                                                            </a>
                                                        </div>
                                                    `);
                                                markers.push(marker);
                                                bounds.push([lat, lon]);
                                            }
                                        }
                                    });

                                    // Fit map to bounds if there are markers
                                    if (bounds.length > 0) {
                                        map.fitBounds(bounds, { padding: [50, 50] }); // Add padding
                                    } else {
                                        // Optionally set a default view if no events have locations
                                        map.setView([43.53, -5.66], 10); // Example: Center on Gijón
                                    }
                                } else {
                                    console.error('Leaflet library not loaded.');
                                     // Optionally display a message to the user
                                    document.getElementById('mapView').innerHTML = '<p class="text-red-500">Map could not be loaded.</p>';
                                }
                            });
                        </script>

                        <?php if (empty($events) || !array_filter($events, function($e) { return !empty($e['latitude']) && !empty($e['longitude']); })): ?>
                            <div class="mt-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                                No se han econtrado localizaciones de eventos según los criterios utilizados
                            </div>
                        <?php endif; ?>
                    </div>

                <?php elseif ($view_mode == 'calendar'): ?>
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                        <div id="calendarView" class="h-auto"></div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                // Check if FullCalendar is loaded
                                if (typeof FullCalendar !== 'undefined') {
                                    const calendarEl = document.getElementById('calendarView');
                                    const calendar = new FullCalendar.Calendar(calendarEl, {
                                        initialView: 'dayGridMonth',
                                        headerToolbar: {
                                            left: 'prev,next today',
                                            center: 'title',
                                            right: 'dayGridMonth,timeGridWeek,listWeek'
                                        },
                                        events: [
                                            <?php foreach ($events as $event): ?>
                                            ,{
                                                id: '<?php echo $event['id']; ?>',
                                                title: '<?php echo addslashes(htmlspecialchars($event['title'])); ?>', // Escape for JS
                                                start: '<?php echo $event['event_date']; ?>',
                                                url: '?view=<?php echo htmlspecialchars($view_mode); ?>&event_id=<?php echo $event['id']; ?><?php echo $link_params; ?>',
                                                extendedProps: {
                                                    category: '<?php echo addslashes(htmlspecialchars($event['category_name'] ?? 'Uncategorized')); ?>'
                                                },
                                                // You might want to add category-specific colors here via classNames or eventDidMount
                                                // classNames: ['event-category-<?php echo $event['category_id'] ?? 0; ?>']
                                            },
                                            <?php endforeach; ?>
                                        ],
                                        eventClick: function(info) {
                                            info.jsEvent.preventDefault(); // Prevent browser navigation
                                            if (info.event.url) {
                                                window.location.href = info.event.url; // Go to the event detail page
                                            }
                                        },
                                        // Add styling for dark mode if needed
                                        eventDidMount: function(info) {
                                            // Example: Add Tailwind classes based on category or dark mode
                                            // info.el.classList.add('dark:bg-indigo-700', 'dark:border-indigo-700');
                                        }
                                    });
                                    calendar.render();
                                } else {
                                     console.error('FullCalendar library not loaded.');
                                     // Optionally display a message to the user
                                    document.getElementById('calendarView').innerHTML = '<p class="text-red-500">Calendar could not be loaded.</p>';
                                }
                            });
                        </script>
                         <?php if (empty($events)): ?>
                            <div class="mt-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded">
                                No se han podido mostrar las fechas para los evetos.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>

    <?php
    // Close the database connection
    mysqli_close($conn);
    ?>
</body>
</html>
