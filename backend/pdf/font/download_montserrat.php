<?php
// URL de Google Fonts para Montserrat Bold
$url = 'https://fonts.gstatic.com/s/montserrat/v25/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCs16Hw5aXp-p7K4KLg.ttf';

// Guardar el archivo
$ttfContent = file_get_contents($url);
if ($ttfContent !== false) {
    file_put_contents('Montserrat-Bold.ttf', $ttfContent);
    echo "Archivo TTF descargado correctamente\n";
    
    // Crear el archivo .z
    $compressed = gzcompress($ttfContent);
    file_put_contents('Montserrat-Bold.z', $compressed);
    echo "Archivo .z creado correctamente\n";
} else {
    die("Error al descargar el archivo TTF");
}
?> 