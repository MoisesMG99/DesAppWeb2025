<?php require 'bloques/_config.php'?>
<?php include 'bloques/_header.php'?>
<?php 
    
    //Guardamos en una variable el nombre del archivo JSON
    $archivo = 'peliculas.json';

    //Llamamos a la función que nos verificará si el archivo existe y además nos decodifica el JSON
    $datos = cargarJSON($archivo);

    //Mostramos los elementos de la lista
    mostrarJSON($datos);
    


    //Comprobamos que los campos no están vacíos
    if(!empty($titulo) && !$director =='' && !$duracion =='' && !$poster =='' && !$sala =='')
    {
        array_push($datos['peliculas'], array('nombre' => $titulo, 'director' => $director, 'duracion' => $duracion, 'poster' => $poster, 'sala' => 'Sala'.$sala));
    }

    //Convertimos el array en JSON
    $nuevoJSON = json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    //Guardamos el JSON en el archivo
    file_put_contents($archivo, $nuevoJSON);

    ?>
<?php include 'bloques/_footer.php'?>
