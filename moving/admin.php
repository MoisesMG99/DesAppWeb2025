<?php require 'bloques/_config.php'?>
<?php include 'bloques/_header.php'?>
<?php 
    //Comprobamos si hay algún contenido en el input
    if(isset($_GET['miTitulo']) && isset($_GET['miDirector']) && isset($_GET['miDuracion']) && isset($_GET['miImagen']) && isset($_GET['miSala']))
    {
        //Si hay contenido en el input, lo recogemos en una variable
        $titulo = strip_tags($_GET['miTitulo']);          //En esta variable recogemos el título de la película del inputa
        $director = strip_tags($_GET['miDirector']);      //En esta variabe recogemos el director de la película del input 
        $duracion = strip_tags($_GET['miDuracion']);      //En esta variable recogemos la duración de la película del input
        $poster = strip_tags($_GET['miImagen']);          //En esta variable recogemos la URL del póster de la película del input
        $sala = strip_tags($_GET['miSala']);
        $horario = strip_tags($_GET['miHorario']);             //En esta variable recogemos la sala de la película del input
    }
?>
<div class="añadir"><!-- INICIO Formulario -->
        <form action="admin.php">
            <label for='titulo'>Título de la película:
                <input type='text' id='titulo' name='miTitulo'>
            </label>
            
            <label for='director'>Nombre del director:
                <input type='text' id='director' name='miDirector'>
            </label>
            
            <label for='duracion'>Duración de la película:
                <input type='text' id='duracion' name='miDuracion'>
            </label>
            
            <label for='imagen'>URL del póster:
                <input type='text' id='imagen' name='miImagen'>
            </label>
            
            <label for='sala'>Sala de la película:
                <input type='number' min = '0' max = '5' id='sala' name='miSala'>
            </label>
            <label for='horario'>Horario de la película:
                <input type="time" id="hora" name='miHorario'>
            </label>
            <br>
            <input type="submit" value="Añadir película">
        </form>

    </div><!-- FIN Formulario -->
    <?php 
    
    //Guardamos en una variable el nombre del archivo JSON
    $archivo = 'peliculas.json';

    //Llamamos a la función que nos verificará si el archivo existe y además nos decodifica el JSON
    $datos = cargarJSON($archivo);



    //Comprobamos que los campos no están vacíos
    if(!empty($titulo) && !$director =='' && !$duracion =='' && !$poster =='' && !$sala =='')
    {
        array_push($datos['peliculas'], array(
            'nombre' => $titulo, 
            'director' => $director, 
            'duracion' => $duracion, 
            'poster' => $poster, 
            'sala' => 'Sala'.$sala, 
            'horario' => array(array('hora' => $horario))
        ));
        
    }

    //Convertimos el array en JSON
    $nuevoJSON = json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

    //Guardamos el JSON en el archivo
    file_put_contents($archivo, $nuevoJSON);

    ?>

<?php include 'bloques/_footer.php'?>