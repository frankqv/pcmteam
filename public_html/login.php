<?php
session_start();
// Verificar si ya hay una sesión activa
if (isset($_SESSION['id']) && isset($_SESSION['rol'])) {
    switch ($_SESSION['rol']) {
        case 1:
            header('Location: administrador/escritorio.php');
            exit;
        case 2:
            header('Location: u_generico/escritorio.php');
            exit;
        case 3:
            header('Location: contable/escritorio.php');
            exit;
        case 4:
            header('Location: comercial/escritorio.php');
            exit;
        case 5:
            header('Location: jtecnico/escritorio.php');
            exit;
        case 6:
            header('Location: tecnico/escritorio.php');
            exit;
        case 7:
            header('Location: bodega/escritorio.php');
            exit;
        default:
            $errMsg = "Rol no definido.";
            session_destroy(); // cerrar sesión por seguridad
    }
}
// Procesar el formulario de login si se envía
if (isset($_POST['ctglog'])) {
    require_once '../config/ctconex.php';
    $errMsg = '';
    $usuario = $_POST['usuario'];
    $clave = MD5($_POST['clave']);
    if ($usuario == '')
        $errMsg = 'Digite su usuario';
    if ($clave == '')
        $errMsg = 'Digite su contraseña';
    if ($errMsg == '') {
        try {
            $stmt = $connect->prepare('SELECT id, nombre, usuario, correo, clave, rol, estado FROM usuarios WHERE usuario = :usuario');
            $stmt->execute([':usuario' => $usuario]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data == false) {
                $errMsg = "El nombre de usuario: $usuario no se encuentra, puede solicitarlo con el administrador.";
            } else {
                if ($clave == $data['clave']) {
                    $_SESSION['id'] = $data['id'];
                    $_SESSION['nombre'] = $data['nombre'];
                    $_SESSION['usuario'] = $data['usuario'];
                    $_SESSION['correo'] = $data['correo'];
                    $_SESSION['clave'] = $data['clave'];
                    $_SESSION['rol'] = $data['rol'];
                    $_SESSION['estado'] = $data['estado'];
                    switch ($_SESSION['rol']) {
                        case 1:
                            header('Location: administrador/escritorio.php');
                            exit;
                        case 2:
                            header('Location: u_generico/escritorio.php');
                            exit;
                        case 3:
                            header('Location: contable/escritorio.php');
                            exit;
                        case 4:
                            header('Location: comercial/escritorio.php');
                            exit;
                        case 5:
                            header('Location: jtecnico/escritorio.php');
                            exit;
                        case 6:
                            header('Location: tecnico/escritorio.php');
                            exit;
                        case 7:
                            header('Location: bodega/escritorio.php');
                            exit;
                        default:
                            $errMsg = 'Rol no definido. Contacte con el administrador.';
                    }
                } else {
                    $errMsg = 'Contraseña incorrecta.';
                }
            }
        } catch (PDOException $e) {
            $errMsg = 'Error de conexión a la base de datos.';
        }
    }
}
?>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PCMARKETTEAM</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" type="image/png" href="assets/img/favicon.webp" />
    <!-- Hotjar Tracking Code for PcMarketTEAM -->
    <script>
        (function(h, o, t, j, a, r) {
            h.hj = h.hj || function() {
                (h.hj.q = h.hj.q || []).push(arguments)
            };
            h._hjSettings = {
                hjid: 6474228,
                hjsv: 6
            };
            a = o.getElementsByTagName('head')[0];
            r = o.createElement('script');
            r.async = 1;
            r.src = t + h._hjSettings.hjid + j + h._hjSettings.hjsv;
            a.appendChild(r);
        })(window, document, 'https://static.hotjar.com/c/hotjar-', '.js?sv=');
    </script>
    <!-- script Mapa de calor -->
</head>
<body>
    <div class="login-wrapper">
        <div class=" box-image box-col">
            <img src="assets/img/sideimage.webp" alt="sideimage">
        </div>
        <div class="box-col">
            <div class="box-form">
                <div class="inner">
                    <div class="form-head">
                        <div class="title">
                            Bienvenido de nuevo
                        </div>
                        <br>
                        <?php
                        if (isset($errMsg)) {
                            echo '
                            <div style="color:#FF0000;text-align:center;font-size:20px; font-weight:bold;">' . $errMsg . '</div>';;
                        } ?>
                        <form class="login-form" autocomplete="off" method="post" role="form">
                            <div class="form-group">
                                <div class="label-text">Nombre de usuario</div>
                                <input type="text" name="usuario" value="<?php if (isset($_POST['usuario']))
                                                                                echo $_POST['usuario'] ?>" autocomplete="off" required class="form-control"
                                    placeholder="usuario01">
                            </div>
                            <!--Contenido -->
                            <style>
                                /* Ajustes para el input con el botón ojo */
.password-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.password-wrapper input.form-control {
    width: 100%;
    padding-right: 44px; /* espacio para el botón */
    box-sizing: border-box;
}
.toggle-password {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    padding: 4px;
    margin: 0;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    line-height: 0;
}
.toggle-password:focus {
    outline: 2px solid #4d90fe; /* accesibilidad */
    outline-offset: 2px;
}
.toggle-password svg {
    pointer-events: none; /* clicks se manejan por el botón */
}
                            </style>
                            <div class="form-group">
                                <div class="label-text">Contraseña</div>
                                <div class="password-wrapper">
                                    <input id="clave" name="clave" type="password" required class="form-control"
                                        placeholder="********" autocomplete="current-password" />
                                    <button type="button" class="toggle-password" aria-label="Mostrar contraseña" title="Mostrar contraseña">
                                        <!-- Ojo abierto -->
                                        <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                            <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg>
                                        <!-- Ojo cerrado (inicialmente oculto) -->
                                        <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="display:none">
                                            <path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.8 21.8 0 0 1 5.06-5.94"></path>
                                            <path d="M1 1l22 22"></path>
                                            <path d="M9.53 9.53A3.5 3.5 0 0 0 14.47 14.47"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <script>
document.addEventListener('DOMContentLoaded', function() {
    const toggleBtn = document.querySelector('.toggle-password');
    const passwordInput = document.getElementById('clave');
    const eyeOpen = toggleBtn.querySelector('.eye-open');
    const eyeClosed = toggleBtn.querySelector('.eye-closed');
    toggleBtn.addEventListener('click', function() {
        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';
        // Alternar visibilidad de SVGs
        if (isPassword) {
            eyeOpen.style.display = 'none';
            eyeClosed.style.display = 'inline';
            toggleBtn.setAttribute('aria-label', 'Ocultar contraseña');
            toggleBtn.title = 'Ocultar contraseña';
        } else {
            eyeOpen.style.display = 'inline';
            eyeClosed.style.display = 'none';
            toggleBtn.setAttribute('aria-label', 'Mostrar contraseña');
            toggleBtn.title = 'Mostrar contraseña';
        }
    });
    // (opcional) Previene envío accidental del formulario si el botón recibe Enter
    toggleBtn.addEventListener('keydown', function(ev) {
        if (ev.key === 'Enter' || ev.key === ' ') {
            ev.preventDefault();
            toggleBtn.click();
        }
    });
});
</script>
                            <!-- incio -->
                            <div class="actions">
                                <button name='ctglog' type="submit" class="btn btn-submit">Acceder</button>
                            </div>
                        </form>
                        <div class="actions">
                            <button onclick="window.location.href='./registrar.php'"
                                class="btn btn-submit">Registrar</button>
                        </div>
                    </div>
                </div>
                <button onclick="window.location.href='../home.php'" class="btnHome">Home</button>
            </div>
        </div>
    </div>
    <script src="assets/js/jquery-3.3.1.min.js"></script>
    <script type="text/javascript" src="assets/js/reenvio.js"></script>
</body>
</html>