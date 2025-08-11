<?php
// Configuración de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Verificar si se han enviado datos del formulario
$mostrar_barcode = false;
$datos = [];
$errores = [];

if ($_POST) {
    // Validación y sanitización de datos
    $proveedor = isset($_POST['proveedor']) ? strtoupper(trim($_POST['proveedor'])) : '';
    $cantidad = isset($_POST['cantidad']) ? filter_var($_POST['cantidad'], FILTER_VALIDATE_INT) : 0;
    $lote = isset($_POST['lote']) ? strtoupper(trim($_POST['lote'])) : '';
    $num_equipo = isset($_POST['num_equipo']) ? filter_var($_POST['num_equipo'], FILTER_VALIDATE_INT) : 1;
    
    // Validaciones
    if (empty($proveedor)) {
        $errores[] = "El proveedor es requerido";
    } elseif (strlen($proveedor) > 10) {
        $errores[] = "El proveedor no puede tener más de 10 caracteres";
    }
    
    if ($cantidad === false || $cantidad <= 0) {
        $errores[] = "La cantidad debe ser un número mayor a 0";
    } elseif ($cantidad > 10000) {
        $errores[] = "La cantidad no puede ser mayor a 10,000";
    }
    
    if (empty($lote)) {
        $errores[] = "El lote es requerido";
    } elseif (strlen($lote) !== 1 || !ctype_alpha($lote)) {
        $errores[] = "El lote debe ser una sola letra";
    }
    
    if ($num_equipo === false || $num_equipo <= 0) {
        $errores[] = "El número de equipo debe ser mayor a 0";
    } elseif ($cantidad > 0 && $num_equipo > $cantidad) {
        $errores[] = "El número de equipo no puede ser mayor a la cantidad total";
    }
    
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

// Generar el texto del código de barras
function generarTextoBarcode($datos, $numero_equipo) {
    return $datos['proveedor'] . $datos['lote'] . str_pad($numero_equipo, 3, '0', STR_PAD_LEFT) . $datos['fecha'];
}

// Función CORREGIDA para generar código de barras Code128B
function generarBarcodeCanvas($texto, $id) {
    $texto_escaped = addslashes($texto);
    
    return "
    <canvas id='barcode_{$id}' width='300' height='80' style='border: 1px solid #ddd; max-width: 100%;'></canvas>
    <script>
        (function() {
            const canvas = document.getElementById('barcode_{$id}');
            if (!canvas) {
                console.error('Canvas barcode_{$id} no encontrado');
                return;
            }
            
            const ctx = canvas.getContext('2d');
            
            // Tabla Code128B CORREGIDA (valores ASCII 32-127)
            const code128B = [
                '11011001100', // 32 (Space)
                '11001101100', // 33 !
                '11001100110', // 34 \"
                '10010011000', // 35 #
                '10010001100', // 36 $
                '10001001100', // 37 %
                '10011001000', // 38 &
                '10011000100', // 39 '
                '10001100100', // 40 (
                '11001001000', // 41 )
                '11001000100', // 42 *
                '11000100100', // 43 +
                '10110011100', // 44 ,
                '10011011100', // 45 -
                '10011001110', // 46 .
                '10111001000', // 47 /
                '10011101000', // 48 0
                '10011100100', // 49 1
                '11001110010', // 50 2
                '11001011100', // 51 3
                '11001001110', // 52 4
                '11011100100', // 53 5
                '11001110100', // 54 6
                '11101101110', // 55 7
                '11101001100', // 56 8
                '11100101100', // 57 9
                '11100100110', // 58 :
                '11101100100', // 59 ;
                '11100110100', // 60 <
                '11100110010', // 61 =
                '11011011000', // 62 >
                '11011000110', // 63 ?
                '11000110110', // 64 @
                '10100011000', // 65 A
                '10001011000', // 66 B
                '10001000110', // 67 C
                '10110001000', // 68 D
                '10001101000', // 69 E
                '10001100010', // 70 F
                '11010001000', // 71 G
                '11000101000', // 72 H
                '11000100010', // 73 I
                '10110111000', // 74 J
                '10110001110', // 75 K
                '10001101110', // 76 L
                '10111011000', // 77 M
                '10111000110', // 78 N
                '10001110110', // 79 O
                '11101110110', // 80 P
                '11010001110', // 81 Q
                '11000101110', // 82 R
                '11011101000', // 83 S
                '11011100010', // 84 T
                '11011101110', // 85 U
                '11101011000', // 86 V
                '11101000110', // 87 W
                '11100010110', // 88 X
                '11101101000', // 89 Y
                '11101100010', // 90 Z
                '11100011010', // 91 [
                '11101111010', // 92 \\
                '11001000010', // 93 ]
                '11110001010', // 94 ^
                '10100110000', // 95 _
                '10100001100', // 96 `
                '10010110000', // 97 a
                '10010000110', // 98 b
                '10000101100', // 99 c
                '10000100110', // 100 d
                '10110010000', // 101 e
                '10110000100', // 102 f
                '10011010000', // 103 g
                '10011000010', // 104 h
                '10000110100', // 105 i
                '10000110010', // 106 j
                '11000010010', // 107 k
                '11001010000', // 108 l
                '11110111010', // 109 m
                '11000010100', // 110 n
                '10001111010', // 111 o
                '10100111100', // 112 p
                '10010111100', // 113 q
                '10010011110', // 114 r
                '10111100100', // 115 s
                '10011110100', // 116 t
                '10011110010', // 117 u
                '11110100100', // 118 v
                '11110010100', // 119 w
                '11110010010', // 120 x
                '11011011110', // 121 y
                '11011110110', // 122 z
                '11110110110', // 123 {
                '10101111000', // 124 |
                '10100011110', // 125 }
                '10001011110', // 126 ~
                '10111101000'  // 127 DEL
            ];
            
            // Patrones especiales
            const START_B = '11010010000';
            const STOP = '1100011101011';
            
            function drawBar(x, width, height) {
                ctx.fillRect(x, 10, width, height);
            }
            
            function drawBarcode(text) {
                const barWidth = 2;
                const barHeight = 50;
                let x = 10;
                let checksum = 104; // Valor inicial para Code128B
                
                try {
                    // Limpiar canvas
                    ctx.clearRect(0, 0, canvas.width, canvas.height);
                    ctx.fillStyle = '#000000';
                    
                    // Dibujar patrón de inicio (START B)
                    const startPattern = START_B;
                    for (let i = 0; i < startPattern.length; i++) {
                        if (startPattern[i] === '1') {
                            drawBar(x, barWidth, barHeight);
                        }
                        x += barWidth;
                    }
                    
                    // Dibujar cada carácter del texto
                    for (let i = 0; i < text.length; i++) {
                        const charCode = text.charCodeAt(i);
                        const index = charCode - 32; // ASCII offset para Code128B
                        
                        if (index >= 0 && index < code128B.length) {
                            const pattern = code128B[index];
                            
                            // Calcular checksum
                            checksum += (index * (i + 1));
                            
                            // Dibujar patrón
                            for (let j = 0; j < pattern.length; j++) {
                                if (pattern[j] === '1') {
                                    drawBar(x, barWidth, barHeight);
                                }
                                x += barWidth;
                            }
                        } else {
                            console.warn('Carácter no válido para Code128B:', text[i]);
                        }
                    }
                    
                    // Dibujar checksum
                    const checksumIndex = checksum % 103;
                    if (checksumIndex < code128B.length) {
                        const checksumPattern = code128B[checksumIndex];
                        for (let i = 0; i < checksumPattern.length; i++) {
                            if (checksumPattern[i] === '1') {
                                drawBar(x, barWidth, barHeight);
                            }
                            x += barWidth;
                        }
                    }
                    
                    // Dibujar patrón de fin
                    const stopPattern = STOP;
                    for (let i = 0; i < stopPattern.length; i++) {
                        if (stopPattern[i] === '1') {
                            drawBar(x, barWidth, barHeight);
                        }
                        x += barWidth;
                    }
                    
                    // Agregar texto debajo del código de barras
                    ctx.fillStyle = '#000000';
                    ctx.font = '12px monospace';
                    ctx.textAlign = 'center';
                    ctx.fillText(text, canvas.width / 2, canvas.height - 10);
                    
                    console.log('Código de barras generado exitosamente: {$texto_escaped}');
                    
                } catch (error) {
                    console.error('Error generando código de barras:', error);
                    ctx.fillStyle = '#ff0000';
                    ctx.font = '14px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillText('Error generando código', canvas.width / 2, canvas.height / 2);
                }
            }
            
            drawBarcode('{$texto_escaped}');
        })();
    </script>";
}


// Función para manejar datos GET con validación
function obtenerDatosGet() {
    $errores = [];
    
    $proveedor = isset($_GET['proveedor']) ? strtoupper(trim($_GET['proveedor'])) : '';
    $cantidad = isset($_GET['cantidad']) ? filter_var($_GET['cantidad'], FILTER_VALIDATE_INT) : 0;
    $lote = isset($_GET['lote']) ? strtoupper(trim($_GET['lote'])) : '';
    
    if (empty($proveedor) || $cantidad <= 0 || empty($lote)) {
        return false;
    }
    
    return [
        'proveedor' => $proveedor,
        'cantidad' => $cantidad,
        'lote' => $lote,
        'fecha' => substr(date('Y'), -2)
    ];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generador de Códigos de Barras - Zebra GK420T</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
        }
        
        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }
        
        .header p {
            font-size: 1.1em;
            opacity: 0.9;
        }
        
        .content {
            display: flex;
            min-height: 600px;
        }
        
        .form-section {
            flex: 1;
            padding: 40px;
            background: #f8f9fa;
        }
        
        .preview-section {
            flex: 1;
            padding: 40px;
            background: white;
            border-left: 3px solid #3498db;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 8px;
            font-size: 1.1em;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 10px rgba(52, 152, 219, 0.2);
        }
        
        .form-group input.error {
            border-color: #e74c3c;
            box-shadow: 0 0 10px rgba(231, 76, 60, 0.2);
        }
        
        .btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            padding: 15px 30px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 10px 5px;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(52, 152, 219, 0.3);
        }
        
        .btn:disabled {
            background: #95a5a6;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #27ae60, #219a52);
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #f39c12, #e67e22);
        }
        
        .sticker-preview {
            width: 100%;
            max-width: 400px;
            height: 100px;
            border: 2px dashed #3498db;
            margin: 20px 0;
            display: flex;
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .sticker-half {
            flex: 1;
            padding: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            font-size: 12px;
            border-right: 1px solid #ddd;
        }
        
        .sticker-half:last-child {
            border-right: none;
        }
        
        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 10px;
            font-weight: bold;
            color: #2c3e50;
            margin-top: 5px;
        }
        
        .info-box {
            background: #e8f4f8;
            border-left: 4px solid #3498db;
            padding: 20px;
            margin: 20px 0;
            border-radius: 0 8px 8px 0;
        }
        
        .print-options {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #ecf0f1;
        }
        
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 8px;
            font-weight: bold;
        }
        
        .alert-info {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .error-list {
            list-style: none;
            padding: 0;
        }
        
        .error-list li {
            margin-bottom: 5px;
        }
        
        .error-list li:before {
            content: "❌ ";
            margin-right: 5px;
        }
        
        @media (max-width: 768px) {
            .content {
                flex-direction: column;
            }
            
            .preview-section {
                border-left: none;
                border-top: 3px solid #3498db;
            }
        }
        
        /* Estilos para impresión */
        @media print {
            body {
                margin: 0;
                background: white !important;
            }
            
            .no-print {
                display: none !important;
            }
            
            .print-only {
                display: block !important;
            }
            
            .sticker-preview {
                position: absolute;
                left: 0;
                top: 0;
                width: 10cm;
                height: 2.5cm;
                border: none;
                box-shadow: none;
                margin: 0;
                page-break-inside: avoid;
            }
        }
        
        /* Estilos específicos para impresión masiva */
        .print-all-container {
            font-family: Arial, sans-serif;
        }
        
        .sticker-pair {
            width: 10cm;
            height: 2.5cm;
            border: 1px solid #000;
            margin-bottom: 2mm;
            display: flex;
            page-break-inside: avoid;
            background: white;
        }
        
        .sticker-single {
            width: 5cm;
            height: 2.5cm;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            border-right: 1px solid #000;
            padding: 1mm;
        }
        
        .sticker-single:last-child {
            border-right: none;
        }
        
        .text-print {
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            font-weight: bold;
            margin-top: 2mm;
        }
        
        .loading {
            text-align: center;
            color: #7f8c8d;
            padding: 20px;
        }
    </style>
</head>
<body>
    <?php if (!isset($_GET['imprimir_todos'])): ?>
    <div class="container no-print">
        <div class="header">
            <h1>🏷️ Generador de Códigos de Barras</h1>
            <p>Compatible con Impresora Zebra GK420T | Formato: 10cm x 2.5cm</p>
        </div>
        
        <div class="content">
            <div class="form-section">
                <h2 style="color: #2c3e50; margin-bottom: 30px;">📝 Datos del Lote</h2>
                
                <?php if (!empty($errores)): ?>
                    <div class="alert alert-error">
                        <strong>❌ Errores encontrados:</strong>
                        <ul class="error-list">
                            <?php foreach ($errores as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="barcodeForm" novalidate>
                    <div class="form-group">
                        <label for="proveedor">🏢 Proveedor (máx. 10 caracteres):</label>
                        <input type="text" id="proveedor" name="proveedor" 
                               value="<?php echo htmlspecialchars($datos['proveedor'] ?? $_POST['proveedor'] ?? ''); ?>" 
                               placeholder="Ej: DELL, HP, LENOVO" 
                               maxlength="10"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="cantidad">📦 Cantidad (1-10,000):</label>
                        <input type="number" id="cantidad" name="cantidad" 
                               value="<?php echo $datos['cantidad'] ?? $_POST['cantidad'] ?? ''; ?>" 
                               placeholder="Ej: 240" 
                               min="1" 
                               max="10000"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="lote">🔤 Lote (1 letra):</label>
                        <input type="text" id="lote" name="lote" 
                               value="<?php echo htmlspecialchars($datos['lote'] ?? $_POST['lote'] ?? ''); ?>" 
                               placeholder="Ej: A" 
                               maxlength="1" 
                               pattern="[A-Za-z]"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="num_equipo">💻 Número de Equipo:</label>
                        <input type="number" id="num_equipo" name="num_equipo" 
                               value="<?php echo $datos['num_equipo'] ?? $_POST['num_equipo'] ?? 1; ?>" 
                               placeholder="Ej: 1" 
                               min="1" 
                               max="<?php echo max(1, $datos['cantidad'] ?? $_POST['cantidad'] ?? 999); ?>"
                               required>
                    </div>
                    
                    <div class="info-box">
                        <strong>📅 Año Actual:</strong> <?php echo date('Y'); ?> 
                        (Se usará: <?php echo substr(date('Y'), -2); ?>)
                    </div>
                    
                    <button type="submit" class="btn" id="submitBtn">🔄 Generar Vista Previa</button>
                </form>
            </div>
            
            <div class="preview-section">
                <h2 style="color: #2c3e50; margin-bottom: 30px;">👁️ Vista Previa</h2>
                
                <?php if ($mostrar_barcode): ?>
                    <div class="alert alert-info">
                        ✅ Códigos de barras generados correctamente
                    </div>
                    
                    <div class="sticker-preview" id="stickerPreview">
                        <div class="sticker-half">
                            <?php
                            $texto_barcode1 = generarTextoBarcode($datos, $datos['num_equipo']);
                            echo generarBarcodeCanvas($texto_barcode1, '1');
                            ?>
                            <div class="barcode-text"><?php echo htmlspecialchars($texto_barcode1); ?></div>
                        </div>
                        
                        <div class="sticker-half">
                            <?php
                            $siguiente_equipo = min($datos['num_equipo'] + 1, $datos['cantidad']);
                            $texto_barcode2 = generarTextoBarcode($datos, $siguiente_equipo);
                            echo generarBarcodeCanvas($texto_barcode2, '2');
                            ?>
                            <div class="barcode-text"><?php echo htmlspecialchars($texto_barcode2); ?></div>
                        </div>
                    </div>
                    
                    <div class="print-options">
                        <h3 style="color: #2c3e50; margin-bottom: 20px;">🖨️ Opciones de Impresión</h3>
                        
                        <button onclick="imprimirSticker()" class="btn btn-success">
                            🖨️ Imprimir Sticker Actual
                        </button>
                        
                        <a href="?imprimir_todos=1&<?php echo http_build_query($datos); ?>" 
                           class="btn btn-warning" target="_blank">
                            📄 Generar Todos los Stickers (<?php echo $datos['cantidad']; ?>)
                        </a>
                        
                        <div class="alert alert-warning">
                            <strong>📏 Especificaciones de Impresión:</strong><br>
                            • Tamaño total: 10cm x 2.5cm<br>
                            • Cada código: 5cm x 2.5cm<br>
                            • Compatible con Zebra GK420T<br>
                            • Formato: <?php echo htmlspecialchars($texto_barcode1); ?><br>
                            • Total de stickers: <?php echo ceil($datos['cantidad'] / 2); ?> hojas
                        </div>
                    </div>
                    
                <?php elseif (!empty($errores)): ?>
                    <div style="text-align: center; color: #e74c3c; padding: 40px;">
                        <h3>❌ Hay errores en el formulario</h3>
                        <p>Corrige los errores mostrados arriba para continuar</p>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; color: #7f8c8d; padding: 40px;">
                        <h3>📋 Completa el formulario</h3>
                        <p>Ingresa los datos requeridos para generar los códigos de barras</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <script>
        // Variables globales
        let formValidado = false;
        
        function imprimirSticker() {
            window.print();
        }
        
        // Validación en tiempo real
        function validarCampo(campo, valor, tipo) {
            let esValido = true;
            let mensaje = '';
            
            switch(tipo) {
                case 'proveedor':
                    if (!valor.trim()) {
                        esValido = false;
                        mensaje = 'El proveedor es requerido';
                    } else if (valor.length > 10) {
                        esValido = false;
                        mensaje = 'Máximo 10 caracteres';
                    }
                    break;
                    
                case 'cantidad':
                    const cantidad = parseInt(valor);
                    if (isNaN(cantidad) || cantidad <= 0) {
                        esValido = false;
                        mensaje = 'Debe ser un número mayor a 0';
                    } else if (cantidad > 10000) {
                        esValido = false;
                        mensaje = 'Máximo 10,000';
                    }
                    break;
                    
                case 'lote':
                    if (!valor.trim()) {
                        esValido = false;
                        mensaje = 'El lote es requerido';
                    } else if (!/^[A-Za-z]$/.test(valor)) {
                        esValido = false;
                        mensaje = 'Debe ser una sola letra';
                    }
                    break;
                    
                case 'num_equipo':
                    const numEquipo = parseInt(valor);
                    const cantidadMax = parseInt(document.getElementById('cantidad').value) || 999;
                    if (isNaN(numEquipo) || numEquipo <= 0) {
                        esValido = false;
                        mensaje = 'Debe ser mayor a 0';
                    } else if (numEquipo > cantidadMax) {
                        esValido = false;
                        mensaje = 'No puede ser mayor a la cantidad';
                    }
                    break;
            }
            
            // Actualizar UI
            if (esValido) {
                campo.classList.remove('error');
            } else {
                campo.classList.add('error');
            }
            
            return esValido;
        }
        
        // Event listeners
        document.getElementById('proveedor').addEventListener('input', function() {
            this.value = this.value.toUpperCase();
            validarCampo(this, this.value, 'proveedor');
        });
        
        document.getElementById('cantidad').addEventListener('input', function() {
            validarCampo(this, this.value, 'cantidad');
            // Actualizar máximo del número de equipo
            const numEquipoField = document.getElementById('num_equipo');
            numEquipoField.max = this.value;
            validarCampo(numEquipoField, numEquipoField.value, 'num_equipo');
        });
        
        document.getElementById('lote').addEventListener('input', function() {
            this.value = this.value.toUpperCase().substring(0, 1);
            validarCampo(this, this.value, 'lote');
        });
        
        document.getElementById('num_equipo').addEventListener('input', function() {
            validarCampo(this, this.value, 'num_equipo');
        });
        
        // Validación del formulario
        document.getElementById('barcodeForm').addEventListener('submit', function(e) {
            const proveedor = document.getElementById('proveedor');
            const cantidad = document.getElementById('cantidad');
            const lote = document.getElementById('lote');
            const numEquipo = document.getElementById('num_equipo');
            
            const validaciones = [
                validarCampo(proveedor, proveedor.value, 'proveedor'),
                validarCampo(cantidad, cantidad.value, 'cantidad'),
                validarCampo(lote, lote.value, 'lote'),
                validarCampo(numEquipo, numEquipo.value, 'num_equipo')
            ];
            
            if (!validaciones.every(v => v)) {
                e.preventDefault();
                alert('Por favor corrige los errores en el formulario antes de continuar.');
                return false;
            }
            
            // Mostrar loading
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '⏳ Generando...';
            
            return true;
        });
        
        // Restaurar botón si hay errores
        window.addEventListener('load', function() {
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn.disabled) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '🔄 Generar Vista Previa';
            }
        });
    </script>
    
    <?php endif; ?>
    
    <?php
    // Generar página para imprimir todos los stickers
    if (isset($_GET['imprimir_todos']) && $_GET['imprimir_todos'] == '1') {
        $datos_get = obtenerDatosGet();
        
        if (!$datos_get) {
            echo '<div class="alert alert-error" style="margin: 20px;">
                    <strong>Error:</strong> Datos inválidos para generar los stickers.
                    <br><a href="' . $_SERVER['PHP_SELF'] . '">Volver al formulario</a>
                  </div>';
            exit;
        }
        
        echo '<div class="print-all-container">';
        echo '<h2 style="text-align: center; margin-bottom: 20px; color: #2c3e50;">
                Stickers Completos - Lote: ' . htmlspecialchars($datos_get['lote']) . 
                ' | Proveedor: ' . htmlspecialchars($datos_get['proveedor']) . '
              </h2>';
        
        $contador = 1;
        for ($i = 1; $i <= $datos_get['cantidad']; $i += 2) {
            echo '<div class="sticker-pair" style="margin-bottom: 5mm;">';
            
            // Primer sticker
            $texto1 = generarTextoBarcode($datos_get, $i);
            echo '<div class="sticker-single">';
            echo '<canvas id="print_barcode_' . $i . '" width="140" height="35" style="max-width: 90%; max-height: 15mm;"></canvas>';
            echo '<div class="text-print">' . htmlspecialchars($texto1) . '</div>';
            echo '</div>';
            
            // Segundo sticker (si existe)
            if ($i + 1 <= $datos_get['cantidad']) {
                $texto2 = generarTextoBarcode($datos_get, $i + 1);
                echo '<div class="sticker-single">';
                echo '<canvas id="print_barcode_' . ($i + 1) . '" width="140" height="35" style="max-width: 90%; max-height: 15mm;"></canvas>';
                echo '<div class="text-print">' . htmlspecialchars($texto2) . '</div>';
                echo '</div>';
            } else {
                echo '<div class="sticker-single" style="background: #f8f9fa;"></div>'; // Espacio vacío
            }
            
            echo '</div>';
            
            // Salto de página cada 10 pares
            if ($contador % 10 == 0 && $i < $datos_get['cantidad']) {
                echo '<div style="page-break-after: always;"></div>';
            }
            $contador++;
        }
        
        echo '</div>';
        
        // Información adicional
        echo '<div style="margin-top: 20px; padding: 10px; border-top: 2px solid #ccc; font-size: 10pt; color: #666;">';
        echo '<strong>Resumen:</strong> ' . $datos_get['cantidad'] . ' equipos | ';
        echo 'Lote: ' . htmlspecialchars($datos_get['lote']) . ' | ';
        echo 'Proveedor: ' . htmlspecialchars($datos_get['proveedor']) . ' | ';
        echo 'Fecha: ' . date('d/m/Y') . ' | ';
        echo 'Total hojas: ' . ceil($datos_get['cantidad'] / 2);
        echo '</div>';
        
        echo '<script>
            // Generar todos los códigos de barras para impresión
            window.onload = function() { 
                // Tabla de patrones Code128 (completa y mejorada)
                const code128Patterns = {
                    "0": "11011001100", "1": "11001101100", "2": "11001100110", "3": "10010011000",
                    "4": "10010001100", "5": "10001001100", "6": "10011001000", "7": "10011000100",
                    "8": "10001100100", "9": "11001001000", "A": "11001000100", "B": "11000100100",
                    "C": "10110011100", "D": "10011011100", "E": "10011001110", "F": "10111001000",
                    "G": "10011101000", "H": "10011100100", "I": "11001110010", "J": "11001011100",
                    "K": "11001001110", "L": "11011100100", "M": "11001110100", "N": "11101001110",
                    "O": "11101100010", "P": "11100010110", "Q": "11100100010", "R": "11100110010",
                    "S": "11000010010", "T": "11000100010", "U": "11100100010", "V": "11101000010",
                    "W": "11101001000", "X": "11101100100", "Y": "11100010100", "Z": "11100100100",
                    " ": "11000010010"
                };
                
                function drawPattern(ctx, pattern, startX, height, width) {
                    let x = startX;
                    for (let i = 0; i < pattern.length; i++) {
                        if (pattern[i] === "1") {
                            ctx.fillRect(x, 2, width, height);
                        }
                        x += width;
                    }
                }
                
                function generateBarcode(canvasId, text) {
                    const canvas = document.getElementById(canvasId);
                    if (!canvas) {
                        console.error("Canvas " + canvasId + " no encontrado");
                        return false;
                    }
                    
                    const ctx = canvas.getContext("2d");
                    const barWidth = 1;
                    const barHeight = 25;
                    let x = 5;
                    
                    try {
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.fillStyle = "#000000";
                        
                        // Patrón de inicio
                        const startPattern = "11010010000";
                        drawPattern(ctx, startPattern, x, barHeight, barWidth);
                        x += startPattern.length * barWidth;
                        
                        // Generar patrón para cada carácter
                        for (let i = 0; i < text.length; i++) {
                            const char = text[i].toUpperCase();
                            let pattern = code128Patterns[char];
                            
                            if (!pattern) {
                                console.warn("Patrón no encontrado para: " + char);
                                pattern = code128Patterns["0"]; // Patrón por defecto
                            }
                            
                            drawPattern(ctx, pattern, x, barHeight, barWidth);
                            x += pattern.length * barWidth;
                        }
                        
                        // Patrón de fin
                        const endPattern = "1100011101011";
                        drawPattern(ctx, endPattern, x, barHeight, barWidth);
                        
                        return true;
                    } catch (error) {
                        console.error("Error generando código de barras para " + canvasId + ":", error);
                        
                        // Mostrar mensaje de error en el canvas
                        ctx.fillStyle = "#ff0000";
                        ctx.font = "8px Arial";
                        ctx.textAlign = "center";
                        ctx.fillText("Error", canvas.width / 2, canvas.height / 2);
                        return false;
                    }
                }
                
                // Contador de éxitos y errores
                let generados = 0;
                let errores = 0;
                
                // Generar todos los códigos de barras';
        
        // Generar JavaScript para cada código de barras
        for ($j = 1; $j <= $datos_get['cantidad']; $j++) {
            $texto_js = addslashes(generarTextoBarcode($datos_get, $j));
            echo '
                if (generateBarcode("print_barcode_' . $j . '", "' . $texto_js . '")) {
                    generados++;
                } else {
                    errores++;
                }';
        }
        
        echo '
                
                console.log("Códigos generados: " + generados + ", Errores: " + errores);
                
                // Mostrar información al usuario
                if (errores > 0) {
                    alert("Se generaron " + generados + " códigos correctamente, pero hubo " + errores + " errores. Revise la consola para más detalles.");
                }
                
                // Imprimir automáticamente después de generar todos los códigos
                setTimeout(function() {
                    // Verificar si todos los canvas están listos
                    let canvasListos = 0;
                    for (let k = 1; k <= ' . $datos_get['cantidad'] . '; k++) {
                        const canvas = document.getElementById("print_barcode_" + k);
                        if (canvas && canvas.getContext("2d")) {
                            canvasListos++;
                        }
                    }
                    
                    if (canvasListos === ' . $datos_get['cantidad'] . ') {
                        console.log("Todos los canvas están listos. Iniciando impresión...");
                        window.print();
                    } else {
                        console.warn("Algunos canvas no están listos: " + canvasListos + "/" + ' . $datos_get['cantidad'] . ');
                        // Intentar imprimir de todos modos
                        window.print();
                    }
                }, 2000);
            }
            
            // Manejar errores de impresión
            window.addEventListener("beforeprint", function() {
                console.log("Iniciando proceso de impresión...");
            });
            
            window.addEventListener("afterprint", function() {
                console.log("Proceso de impresión completado.");
            });
            
            // Manejar errores generales
            window.addEventListener("error", function(e) {
                console.error("Error en la página:", e.error);
            });
        </script>';
        exit;
    }
    ?>
</body>
</html>