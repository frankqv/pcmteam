<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$datos = [];
$errores = [];
$mostrar_barcode = false;

if ($_POST) {
    $proveedor = strtoupper(trim($_POST['proveedor'] ?? ''));
    $cantidad = filter_var($_POST['cantidad'] ?? 0, FILTER_VALIDATE_INT);
    $lote = strtoupper(trim($_POST['lote'] ?? ''));
    $num_equipo = filter_var($_POST['num_equipo'] ?? 1, FILTER_VALIDATE_INT);
    
    if (empty($proveedor)) $errores[] = "Proveedor requerido";
    if ($cantidad <= 0) $errores[] = "Cantidad debe ser mayor a 0";
    if (empty($lote) || strlen($lote) !== 1) $errores[] = "Lote debe ser una letra";
    if ($num_equipo <= 0 || $num_equipo > $cantidad) $errores[] = "Número de equipo inválido";
    
    if (empty($errores)) {
        $datos = [
            'proveedor' => $proveedor,
            'cantidad' => $cantidad,
            'lote' => $lote,
            'num_equipo' => $num_equipo,
            'fecha' => substr(date('Y'), -2)
        ];
        $mostrar_barcode = true;
    }
}

function generarTexto($datos, $numero) {
    return $datos['proveedor'] . $datos['lote'] . str_pad($numero, 3, '0', STR_PAD_LEFT) . $datos['fecha'];
}

function generarBarcode($texto, $id) {
    return "<canvas id='bc_{$id}' width='200' height='50'></canvas>
    <script>
    (function() {
        const canvas = document.getElementById('bc_{$id}');
        const ctx = canvas.getContext('2d');
        const patterns = {
            '0':'11011001100','1':'11001101100','2':'11001100110','3':'10010011000','4':'10010001100',
            '5':'10001001100','6':'10011001000','7':'10011000100','8':'10001100100','9':'11001001000',
            'A':'10100011000','B':'10001011000','C':'10001000110','D':'10110001000','E':'10001101000',
            'F':'10001100010','G':'11010001000','H':'11000101000','I':'11000100010','J':'10110111000',
            'K':'10110001110','L':'10001101110','M':'10111011000','N':'10111000110','O':'10001110110',
            'P':'11101110110','Q':'11010001110','R':'11000101110','S':'11011101000','T':'11011100010',
            'U':'11011101110','V':'11101011000','W':'11101000110','X':'11100010110','Y':'11101101000',
            'Z':'11101100010'
        };
        
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#000';
        
        let x = 5;
        const barWidth = 1.5;
        const barHeight = 30;
        
        // Start pattern
        const start = '11010010000';
        for (let i = 0; i < start.length; i++) {
            if (start[i] === '1') ctx.fillRect(x, 5, barWidth, barHeight);
            x += barWidth;
        }
        
        const text = '{$texto}';
        let checksum = 104;
        
        for (let i = 0; i < text.length; i++) {
            const char = text[i];
            const pattern = patterns[char] || patterns['0'];
            const value = char.charCodeAt(0) - 32;
            checksum += value * (i + 1);
            
            for (let j = 0; j < pattern.length; j++) {
                if (pattern[j] === '1') ctx.fillRect(x, 5, barWidth, barHeight);
                x += barWidth;
            }
        }
        
        // Checksum
        const checksumPattern = Object.values(patterns)[checksum % 103];
        for (let i = 0; i < checksumPattern.length; i++) {
            if (checksumPattern[i] === '1') ctx.fillRect(x, 5, barWidth, barHeight);
            x += barWidth;
        }
        
        // Stop pattern
        const stop = '1100011101011';
        for (let i = 0; i < stop.length; i++) {
            if (stop[i] === '1') ctx.fillRect(x, 5, barWidth, barHeight);
            x += barWidth;
        }
        
        ctx.font = '10px monospace';
        ctx.textAlign = 'center';
        ctx.fillText(text, canvas.width / 2, canvas.height - 5);
    })();
    </script>";
}

if (isset($_GET['print'])) {
    $datos_print = [
        'proveedor' => $_GET['proveedor'] ?? '',
        'cantidad' => $_GET['cantidad'] ?? 0,
        'lote' => $_GET['lote'] ?? '',
        'fecha' => substr(date('Y'), -2)
    ];
    
    echo '<style>
        body { font-family: Arial; margin: 0; }
        .sticker { width: 10cm; height: 2.5cm; border: 1px solid #000; margin: 2mm; display: flex; page-break-inside: avoid; }
        .half { width: 5cm; display: flex; flex-direction: column; align-items: center; justify-content: center; border-right: 1px solid #000; }
        .half:last-child { border-right: none; }
        .text { font-family: monospace; font-size: 8pt; margin-top: 2mm; }
        @media print { body { margin: 0; } }
    </style>';
    
    for ($i = 1; $i <= $datos_print['cantidad']; $i += 2) {
        echo '<div class="sticker">';
        
        $texto1 = generarTexto($datos_print, $i);
        echo '<div class="half">';
        echo "<canvas id='p_{$i}' width='140' height='35'></canvas>";
        echo "<div class='text'>{$texto1}</div>";
        echo '</div>';
        
        if ($i + 1 <= $datos_print['cantidad']) {
            $texto2 = generarTexto($datos_print, $i + 1);
            echo '<div class="half">';
            echo "<canvas id='p_" . ($i + 1) . "' width='140' height='35'></canvas>";
            echo "<div class='text'>{$texto2}</div>";
            echo '</div>';
        } else {
            echo '<div class="half"></div>';
        }
        
        echo '</div>';
    }
    
    echo '<script>
    window.onload = function() {
        const patterns = {
            "0":"11011001100","1":"11001101100","2":"11001100110","3":"10010011000","4":"10010001100",
            "5":"10001001100","6":"10011001000","7":"10011000100","8":"10001100100","9":"11001001000",
            "A":"10100011000","B":"10001011000","C":"10001000110","D":"10110001000","E":"10001101000",
            "F":"10001100010","G":"11010001000","H":"11000101000","I":"11000100010","J":"10110111000",
            "K":"10110001110","L":"10001101110","M":"10111011000","N":"10111000110","O":"10001110110",
            "P":"11101110110","Q":"11010001110","R":"11000101110","S":"11011101000","T":"11011100010",
            "U":"11011101110","V":"11101011000","W":"11101000110","X":"11100010110","Y":"11101101000",
            "Z":"11101100010"
        };
        
        function drawBarcode(canvasId, text) {
            const canvas = document.getElementById(canvasId);
            if (!canvas) return;
            const ctx = canvas.getContext("2d");
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            ctx.fillStyle = "#000";
            
            let x = 5;
            const barWidth = 1;
            const barHeight = 25;
            
            const start = "11010010000";
            for (let i = 0; i < start.length; i++) {
                if (start[i] === "1") ctx.fillRect(x, 2, barWidth, barHeight);
                x += barWidth;
            }
            
            for (let i = 0; i < text.length; i++) {
                const pattern = patterns[text[i]] || patterns["0"];
                for (let j = 0; j < pattern.length; j++) {
                    if (pattern[j] === "1") ctx.fillRect(x, 2, barWidth, barHeight);
                    x += barWidth;
                }
            }
            
            const stop = "1100011101011";
            for (let i = 0; i < stop.length; i++) {
                if (stop[i] === "1") ctx.fillRect(x, 2, barWidth, barHeight);
                x += barWidth;
            }
        }
        ';
    
    for ($j = 1; $j <= $datos_print['cantidad']; $j++) {
        $texto_js = generarTexto($datos_print, $j);
        echo "drawBarcode('p_{$j}', '{$texto_js}');";
    }
    
    echo '
        setTimeout(() => window.print(), 1000);
    };
    </script>';
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Generador Códigos de Barras</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 20px; border-radius: 5px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input { width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px; }
        button { background: #007cba; color: white; padding: 10px 20px; border: none; border-radius: 3px; cursor: pointer; }
        button:hover { background: #005a8a; }
        .error { color: red; margin-bottom: 15px; }
        .preview { margin-top: 20px; padding: 20px; border: 1px solid #ddd; border-radius: 3px; }
        .sticker-preview { width: 100%; max-width: 400px; height: 80px; border: 1px solid #ccc; display: flex; margin: 10px 0; }
        .sticker-half { flex: 1; display: flex; flex-direction: column; align-items: center; justify-content: center; border-right: 1px solid #ccc; }
        .sticker-half:last-child { border-right: none; }
        .text { font-family: monospace; font-size: 10px; margin-top: 5px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Generador de Códigos de Barras</h1>
        
        <?php if (!empty($errores)): ?>
            <div class="error">
                <?php foreach ($errores as $error): echo "• $error<br>"; endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Proveedor:</label>
                <input type="text" name="proveedor" value="<?= htmlspecialchars($datos['proveedor'] ?? $_POST['proveedor'] ?? '') ?>" maxlength="10" required>
            </div>
            
            <div class="form-group">
                <label>Cantidad:</label>
                <input type="number" name="cantidad" value="<?= $datos['cantidad'] ?? $_POST['cantidad'] ?? '' ?>" min="1" max="9999" required>
            </div>
            
            <div class="form-group">
                <label>Lote (1 letra):</label>
                <input type="text" name="lote" value="<?= htmlspecialchars($datos['lote'] ?? $_POST['lote'] ?? '') ?>" maxlength="1" required>
            </div>
            
            <div class="form-group">
                <label>Número de Equipo:</label>
                <input type="number" name="num_equipo" value="<?= $datos['num_equipo'] ?? $_POST['num_equipo'] ?? 1 ?>" min="1" required>
            </div>
            
            <button type="submit">Generar</button>
        </form>
        
        <?php if ($mostrar_barcode): ?>
            <div class="preview">
                <h3>Vista Previa</h3>
                
                <div class="sticker-preview">
                    <div class="sticker-half">
                        <?php
                        $texto1 = generarTexto($datos, $datos['num_equipo']);
                        echo generarBarcode($texto1, '1');
                        ?>
                        <div class="text"><?= $texto1 ?></div>
                    </div>
                    
                    <div class="sticker-half">
                        <?php
                        $siguiente = min($datos['num_equipo'] + 1, $datos['cantidad']);
                        $texto2 = generarTexto($datos, $siguiente);
                        echo generarBarcode($texto2, '2');
                        ?>
                        <div class="text"><?= $texto2 ?></div>
                    </div>
                </div>
                
                <p>
                    <button onclick="window.print()">Imprimir Actual</button>
                    <a href="?print=1&<?= http_build_query($datos) ?>" target="_blank">
                        <button type="button">Imprimir Todos (<?= $datos['cantidad'] ?>)</button>
                    </a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>