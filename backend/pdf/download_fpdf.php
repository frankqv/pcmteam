<?php
// URL de la última versión de FPDF
$fpdfUrl = 'http://www.fpdf.org/en/download/fpdf186.zip';
$zipFile = 'fpdf.zip';

// Descargar el archivo ZIP
if (file_put_contents($zipFile, file_get_contents($fpdfUrl)) === false) {
    die("Error al descargar FPDF");
}

// Crear un objeto ZipArchive
$zip = new ZipArchive;
if ($zip->open($zipFile) === TRUE) {
    // Extraer solo los archivos necesarios
    $filesToExtract = array(
        'fpdf186/fpdf.php' => 'fpdf.php',
        'fpdf186/font/' => 'font/'
    );
    
    foreach ($filesToExtract as $zipPath => $extractPath) {
        if (substr($zipPath, -1) === '/') {
            // Es un directorio
            $zip->extractTo('.', array_filter(
                range(0, $zip->numFiles - 1),
                function ($i) use ($zip, $zipPath) {
                    return strpos($zip->getNameIndex($i), $zipPath) === 0;
                }
            ));
        } else {
            // Es un archivo
            copy("zip://$zipFile#$zipPath", $extractPath);
        }
    }
    
    $zip->close();
    unlink($zipFile);
    echo "FPDF instalado correctamente\n";
} else {
    die("Error al abrir el archivo ZIP");
}
?> 