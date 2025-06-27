<?php
// URL de la fuente Montserrat Bold
$url = 'https://fonts.gstatic.com/s/montserrat/v25/JTUHjIg1_i6t8kCHKm4532VJOt5-QNFgpCs16Hw5aXp-p7K4KLg.ttf';

// Intentar descargar el archivo TTF
$ttfContent = @file_get_contents($url);

if ($ttfContent === false) {
    die("No se pudo descargar la fuente. Por favor, verifica tu conexión a internet.");
}

// Comprimir el contenido
$compressed = gzcompress($ttfContent);

// Guardar el archivo comprimido
if (file_put_contents('Montserrat-Bold.z', $compressed) === false) {
    die("No se pudo guardar el archivo .z");
}

echo "Archivo .z creado correctamente\n";
echo "Tamaño del archivo original: " . strlen($ttfContent) . " bytes\n";
echo "Tamaño del archivo comprimido: " . strlen($compressed) . " bytes\n";
?> 