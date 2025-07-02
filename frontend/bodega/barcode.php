<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Generador de Códigos de Barras para Zebra GK420T</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f0f0; padding: 20px; }
    .container { background: #fff; padding: 20px; border-radius: 8px; max-width: 500px; margin: auto; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    .form-group { margin-bottom: 15px; }
    label { display: block; margin-bottom: 5px; font-weight: bold; }
    input[type="text"], input[type="number"] {
      width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;
    }
    button { background-color: #007cba; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
    button:hover { background-color: #005a8a; }
    .buttons { display: flex; gap: 10px; margin-top: 20px; }
    .error { color: red; margin-bottom: 10px; }
    .instrucciones { margin-top: 30px; font-size: 14px; background: #f9f9f9; padding: 15px; border-radius: 6px; border-left: 5px solid #007cba; }
  </style>
</head>
<body>
  <div class="container">
    <h2>Generador de Etiquetas ZPL - Zebra GK420T</h2>

    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $errores = [];
    $datos = [];

    function generarTexto($datos, $numero) {
      return $datos['proveedor'] . $datos['lote'] . str_pad($numero, 3, '0', STR_PAD_LEFT) . $datos['fecha'];
    }

    if (isset($_GET['zpl'])) {
      $datos_zpl = [
        'proveedor' => $_GET['proveedor'] ?? '',
        'cantidad' => (int) ($_GET['cantidad'] ?? 0),
        'lote' => $_GET['lote'] ?? '',
        'fecha' => substr(date('Y'), -2)
      ];

      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename="etiquetas.zpl"');

      for ($i = 1; $i <= $datos_zpl['cantidad']; $i++) {
        $codigo = generarTexto($datos_zpl, $i);
        echo "^XA\n";
        echo "^CF0,30\n";
        echo "^FO30,30^BY2\n";
        echo "^BCN,80,Y,N,N\n";
        echo "^FD>{$codigo}^FS\n";
        echo "^FO30,120^ADN,30,20^FD{$codigo}^FS\n";
        echo "^XZ\n";
      }
      exit;
    }

    if ($_POST) {
      $proveedor = strtoupper(trim($_POST['proveedor'] ?? ''));
      $cantidad = filter_var($_POST['cantidad'] ?? 0, FILTER_VALIDATE_INT);
      $lote = strtoupper(trim($_POST['lote'] ?? ''));

      if (empty($proveedor)) $errores[] = "Proveedor requerido";
      if ($cantidad <= 0) $errores[] = "Cantidad debe ser mayor a 0";
      if (empty($lote) || strlen($lote) !== 1) $errores[] = "Lote debe ser una letra";

      if (empty($errores)) {
        $datos = [
          'proveedor' => $proveedor,
          'cantidad' => $cantidad,
          'lote' => $lote,
          'fecha' => substr(date('Y'), -2)
        ];

        echo "<div class='buttons'>";
        echo "<a href='?zpl=1&" . http_build_query($datos) . "' target='_blank'><button>Descargar ZPL</button></a>";
        echo "</div>";
      } else {
        echo "<div class='error'>" . implode('<br>', $errores) . "</div>";
      }
    }
    ?>

    <form method="POST">
      <div class="form-group">
        <label>Proveedor:</label>
        <input type="text" name="proveedor" maxlength="10" required>
      </div>

      <div class="form-group">
        <label>Cantidad de Etiquetas:</label>
        <input type="number" name="cantidad" min="1" required>
      </div>

      <div class="form-group">
        <label>Lote (1 letra):</label>
        <input type="text" name="lote" maxlength="1" required>
      </div>

      <button type="submit">Generar</button>
    </form>

    <div class="instrucciones">
      <h4>🖨️ ¿Cómo usar el archivo .zpl con tu Zebra GK420T?</h4>
      <ol>
        <li>Haz clic en <strong>Descargar ZPL</strong> para guardar el archivo <code>etiquetas.zpl</code>.</li>
        <li>Conecta tu impresora Zebra GK420T y asegúrate que esté en modo ZPL.</li>
        <li>Arrastra el archivo sobre el ícono de la impresora, o usa este comando en Windows:<br>
          <code>copy /b etiquetas.zpl LPT1:</code> o <code>copy /b etiquetas.zpl "\\NombrePC\NombreImpresora"</code>
        </li>
        <li>En Mac/Linux puedes usar:<br>
          <code>lpr -P NombreImpresora etiquetas.zpl</code>
        </li>
      </ol>
      <p>💡 También puedes usar la app Zebra Setup Utilities para enviar el archivo fácilmente.</p>
    </div>

  </div>
</body>
</html>
