<?php
// Actividad 9 DAW Consumo de API Rick and Morty

// Inicialización de las variables para mantener el estado del formulario
$personajes = [];   // Permite almacenar los resultados de la API
$error = '';        // Si ocurre algún problema se envía mensaje de error
$nombre = '';
$estado = '';
$especie = '';

// Se verifica si el formulario fue enviado mediante el método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Se obtiene y limpia los datos del formualrio
    $nombre = trim($_POST['nombre'] ?? '');
    $estado = trim($_POST['estado'] ?? '');
    $especie = trim($_POST['especie'] ?? '');

    // Ubicamos la url base de la API Rick and Morty para poder buscar personajes
    $url = 'https://rickandmortyapi.com/api/character/?';

    // Se agregan parámetros de búsqueda sólo si están presentes
    if (!empty($nombre)) {
        $url .= 'name=' . urlencode($nombre) . '&';
    }
    if (!empty($estado)) {
        $url .= 'status=' . urlencode($estado) . '&';
    }
    if (!empty($especie)) {
        $url .= 'species=' . urlencode($especie) . '&';
    }

    // En caso de que haya un "&" sobrante, se elimina
    $url = rtrim($url, '&');

    // Para realizar la petición HTTP  a la API
    $respuesta = @file_get_contents($url);

    // Para manejar errores en la conexión
    if ($respuesta === false) {
        $error = 'Error al conectar con la API. Verifica tu conexión a internet por favor.';   
    } else {
        // Decodifica la respuesta JSON a un array asociativo
        $datos = json_decode($respuesta, true);

        // Se verifica si hubo un error al decodificar el JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = 'Error al procesar la respuesta de la API.';
        }
        // Se verifica si la API devolvió un mensaje de error explícito
        elseif (isset($datos['error'])) {
            $error = 'No se encontraron personajes con esos criterios.';
        }
        // Si todo lo anterior está bien, recien asigna los resultados
        else {
            $personajes = $datos['results'] ?? [];
            if (empty($personajes)) {
                $error = 'No se encontraron personajes con esos criterios.';
            }
        }
    }
    
}
?>

