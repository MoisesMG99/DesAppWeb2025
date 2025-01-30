
//VARIABLES
let indice =0;

//Creamos nuestro array multidimensional donde guardaremos las cosas
const miDestino = [
    //0Monumento            1Lugar          2Rating     3Descripcion                                                                                                                                                                                                                                                                                                                                                                                                                    4Imagen                                                                                                    
    ['Coliseo',             'Roma',         4,          'Largo 189 metros, ancho 156 metros, para una altura de más de 48 metros, el Coliseo se extiende sobre una superficie de 24.000 metros cuadrados y podía hospedar cerca de 50.000 espectadores que podían acomodarse en la cueva, formada por peldaños en ladrillo revestidos de mármol.',                                                                                                                                      'https://upload.wikimedia.org/wikipedia/commons/thumb/d/de/Colosseo_2020.jpg/800px-Colosseo_2020.jpg'],
    ['Torre Eiffel',        'Paris',        1,          'La torre Eiffel(tour Eiffel, en francés), inicialmente llamada Tour de 300 mètres («Torre de 300 metros») es una estructura de hierro pudelado diseñada inicialmente por los ingenieros civiles Maurice Koechlin y Émile Nouguier y construida, tras el rediseño estético de Stephen Sauvestre, por el ingeniero civil francés Gustave Eiffel y sus colaboradores para la Exposición Universal de 1889 en París (Francia)',    'https://cdn-imgix.headout.com/media/images/c90f7eb7a5825e6f5e57a5a62d05399c-25058-BestofParis-EiffelTower-Cruise-Louvre-002.jpg'],
    ['Machu Picchu',        'Perú',         3,          'Machu Picchu es una antigua ciudad inca en la cordillera de los Andes en Perú. Se encuentra en la cresta de una montaña, a más de 2,400 metros sobre el nivel del mar. Construida en el siglo XV y más tarde abandonada, es famosa por sus sofisticadas paredes de piedra seca que se unen sin el uso de mortero, la arquitectura de terrazas que se funden con la ladera de la montaña y las impresionantes vistas.',         'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Machu_Picchu%2C_Peru_%282018%29.jpg/1200px-Machu_Picchu%2C_Peru_%282018%29.jpg'],
    ['Gran Muralla China',  'China',        2,          'La Gran Muralla China es una antigua fortificación china construida para proteger el imperio',                                                                                                                                                                                                                                                                                                                                 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/23/The_Great_Wall_of_China_at_Jinshanling-edit.jpg/800px-The_Great_Wall_of_China_at_Jinshanling-edit.jpg']
];


//Llamamos a la función que nos carga el contenido para que nos haga una previsualización de lo que nos mostrará
cargaContenido(indice);

//Realizamos una comprobación de que nos encontremos en el primer valor del array, y si es así nos ocultaría el botón anterior.
if(indice == 0){
    document.querySelector('.anterior').style.display = 'none';
}

//Funcion que nos genera un numero aleatorio
function aleatorio(min, max){
    return Math.floor(Math.random() * (max - min)) + min;
}

function cargaContenido(miNumerin){//Función que nos carga el contenido
    //Cargamos el contenido
    document.querySelector('.imgDestino').src = miDestino[miNumerin][4];//Cargamos la imagen
    document.querySelector('.imgDestino').alt = miDestino[miNumerin][0];//Cargamos el alt de la imagen
    document.querySelector('.subtitulo h2').textContent = miDestino[miNumerin][0];//Cargamos el nombre del monumento
    document.querySelector('.lugar').textContent = miDestino[miNumerin][1];//Cargamos el lugar
    document.querySelector('.valoracion').textContent = '⭐'.repeat(miDestino[miNumerin][2]);//Cargamos la valoración
    document.querySelector('.info p').textContent = miDestino[miNumerin][3];//Cargamos la descripción
}
//Creamos el evento que nos cargará todo
function cargarMonumento(numerin){

    //Creamos un switch para gestionar los botones
    switch(numerin){
        case 0: //Caso en el que gestionaremos que el usuario haga click en Anterior
            
            indice--;//Restamos uno al valor de indice

            //Comprobamos que si el valor de indice es igual a 0 nos oculte el boton anterior
            if(indice == 0){
                document.querySelector('.anterior').style.display = 'none';
            }

            cargaContenido(indice);//Llamamos a la función que nos carga el contenido

            //Mostramos el botón siguiente en el caso de que estuviesemos al final del array
            document.querySelector('.siguiente').style.display = 'block';
            break;
        case 1://Caso en el que gestionaremos que el usuario haga click en Aleatorio
            //Generamos un numero aleatorio
            indice = aleatorio(0, miDestino.length);//Llamamos al método aleatorio para que nos pase un número aleatorio entre 0 y la longitud máxima del array
            
            console.log('Te vamos a mostrar el monumento: ' + miDestino[indice][0]);//Mostramos un alert con el nombre del monumento

            //Hacemos las comprobaciones de que nos oculte los botones correspondientes(si está al final del array tiene que ocultar siguiente, y si está al principio tiene que ocultar anterior)
            if(indice == 0){ //Si el valor de indice es igual a 0 esconemos el botón anterior
                document.querySelector('.anterior').style.display = 'none';
                document.querySelector('.siguiente').style.display = 'block';
            }

            if(indice == miDestino.length - 1){//Si el valor de indice es igua a la longitud del array escondemos el boton siguiente
                document.querySelector('.siguiente').style.display = 'none';
                document.querySelector('.anterior').style.display = 'block';
            }

            if(indice !=0 && indice != miDestino.length - 1){//Si el valor de indice no es igual a 0 ni a la longitud del array mostramos los dos botones
                document.querySelector('.anterior').style.display = 'block';
                document.querySelector('.siguiente').style.display = 'block';
            }

            //Mostramos el contenido equivalente al valor del indice
            cargaContenido(indice);
            break;
        case 2://Caso en el que gestionaremos que el usuario haga click en siguiente
            
            indice++;//Le sumamos uno al valor de indice

            //Comprobamos que si el valor de indice es igual a la longitud del array nos esconda el boton siguiente
            if(indice == miDestino.length - 1){
                document.querySelector('.siguiente').style.display = 'none';
            }

            //Mostramos el contenido equivalente al valor de indice
            cargaContenido(indice);
            
            //Mostramos el botón anterior en el caso de que estuviesemos en el principio del array
            document.querySelector('.anterior').style.display = 'block';
            break;
    }
}
