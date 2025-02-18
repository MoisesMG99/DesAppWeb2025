<?php require 'bloques/_config.php'?>
<?php include 'bloques/_header.php'?>
<?php 
    
    //Guardamos en una variable el nombre del archivo JSON
    $archivo = 'peliculas.json';

    //Llamamos a la función que nos verificará si el archivo existe y además nos decodifica el JSON
    $datos = cargarJSON($archivo);

    //Mostramos los elementos de la lista
    mostrarJSON($datos);
    ?>
<?php include 'bloques/_footer.php'?>
