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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actividad_9_DAW - Rick and Morty</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <header>
        <h1>Buscador de Personajes Rick and Morty</h1>
        <p>Consumo de API pública para la actividad 9</p>
    </header>

    <main>
        <!-- Sección búsqueda del formulario -->
        <section class="formulario-busqueda">
            <form method="POST" action="">
                <label for="nombre">Nombre (opcional):</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($nombre) ?>" placeholder="Ej: Rick">

                <label for="estado">Estado:</label>
                <select id="estado" name="estado">
                    <option value="">Todos</option>
                    <option value="alive" <?= $estado === 'alive' ? 'selected' : '' ?>>Vivo</option>
                    <option value="dead" <?= $estado === 'dead' ? 'selected' : '' ?>>Muerto</option>
                    <option value="unknown" <?= $estado === 'unknown' ? 'selected' : '' ?>>Desconocido</option>
                </select>

                <label for="especie">Especie (opcional):</label>
                <input type="text" id="especie" name="especie" value="<?= htmlspecialchars($especie) ?>" placeholder="Ej: Human">

                <button type="submit">🔍 Buscar Personajes</button>
            </form>
        </section>

        <!-- Si existe error debe mostrar mensaje -->
        <?php if ($error): ?>
            <div class="mensaje-error">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <!-- Si hay personajes, muestra los resultados -->
        <?php if (!empty($personajes)): ?>
            <h2>Resultados (<?= count($personajes) ?> encontrados)</h2>
            <div class="tarjetas">
                <?php foreach ($personajes as $p): ?>
                    <div class="tarjeta">
                        <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                        <h3><?= htmlspecialchars($p['name']) ?></h3>
                        <p><strong>Estado:</strong> <span class="estado <?= strtolower($p['status']) ?>"><?= $p['status'] ?></span></p>
                        <p><strong>Especie:</strong> <?= htmlspecialchars($p['species'] ?? '—') ?></p>
                        <p><strong>Género:</strong> <?= htmlspecialchars($p['gender'] ?? '—') ?></p>
                        <p><strong>Origen:</strong> <?= htmlspecialchars($p['origin']['name'] ?? '—') ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <p>Desarrollado por Estudiantes de DAW - 6to Semestre - UTM | Actividad 9 | <?=  date('Y') ?></p>
    </footer>
</body>
</html>