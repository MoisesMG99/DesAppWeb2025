<?php

//Función que se encargará de cargar nuestro JSON
function cargarJSON($archivo){
    
    if(file_exists($archivo)){//Comprobamos que el archivo existe
        $miJSON = file_get_contents($archivo); //Cargamos nuestro documento JSON
        $datos = json_decode($miJSON, true); //Decodificamos el JSON
        return $datos;
    }
}

//Función que se encargará de mostrar por pantalla nuestro archivo JSON
function mostrarJSON($datos){
    //Mostramos los elementos de la lista
    echo '<ul class="miPelicula">';

    //Recorremos el array mostrando los datos
    foreach($datos['peliculas'] as $item)
    {
        echo "<li class= '{$item['sala']} miSala'>
            <img src='{$item['poster']}' alt='{$item['nombre']}'>
            <div class = 'contenido'>
                <h2>{$item['nombre']}</h2>
                <p>{$item['director']}</p>
                <p>{$item['duracion']}</p>
            </div>
            <div class = 'horarios'>
                <h3>Horarios</h3>
                <ul>";

                //Bucle foreach parar recorrer el array de horarios
                foreach($item['horario'] as $horario)
                {
                    echo "<li>{$horario['hora']}</li>";
                }
                echo "</ul>
            </div>
        </li>";
    }
    echo '</ul>';
}