
<?php
// ingresar_myl.php
// Versión robusta y oscura. Sobrescribe frontend/laboratorio/ingresar_myl.php

ob_start();
session_start();

/* -------------------- Seguridad -------------------- */
$ALLOWED_ROLES = [1,2,5,6,7];
if (!isset($_SESSION['rol']) || !in_array((int)$_SESSION['rol'], $ALLOWED_ROLES, true)) {
    header('Location: ../error404.php');
    exit();
}

/* -------------------- Cargar ctconex.php (ruta robusta) -------------------- */
$projectRoot = dirname(__DIR__, 2); // desde frontend/laboratorio -> sube 2 niveles
$ctconexCandidates = [
    $projectRoot . '/backend/bd/ctconex.php',
    dirname(__DIR__,1) . '/backend/bd/ctconex.php',
    __DIR__ . '/../../backend/bd/ctconex.php',
    __DIR__ . '/../../../backend/bd/ctconex.php'
];

$ctconexFound = false;
foreach ($ctconexCandidates as $p) {
    if (file_exists($p)) {
        require_once $p;
        $ctconexFound = true;
        break;
    }
}

if (!$ctconexFound) {
    die('Error: archivo de conexión no encontrado. Rutas probadas: ' . htmlspecialchars(implode(' | ', $ctconexCandidates)));
}

/* -------------------- Asegurar $mysqli (soporta $mysqli, $conn o crear desde constantes) -------------------- */
if (!isset($mysqli) || !($mysqli instanceof mysqli)) {
    if (isset($conn) && ($conn instanceof mysqli)) {
        $mysqli = $conn; // alias si ctconex definió $conn
    } else {
        // intentar crear a partir de constantes conocidas
        if (defined('dbhost') && defined('dbuser') && defined('dbpass') && defined('dbname')) {
            $mysqli = new mysqli(dbhost, dbuser, dbpass, dbname);
            if ($mysqli->connect_error) {
                die('❌ Error de conexión mysqli: ' . htmlspecialchars($mysqli->connect_error));
            }
            $mysqli->set_charset('utf8');
        } else {
            die('Error: conexión $mysqli no encontrada. Ajusta backend/bd/ctconex.php para exponer $mysqli o define constantes dbhost/dbuser/dbpass/dbname.');
        }
    }
}

/* -------------------- Obtener inventario_id (GET/POST) -------------------- */
$inventario_id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : (isset($_REQUEST['inventario_id']) ? (int)$_REQUEST['inventario_id'] : 0);
if ($inventario_id <= 0) {
    echo "<div style='padding:20px;font-family:Arial,Helvetica,sans-serif'>Falta inventario_id. Abre la página con ?id=NN o desde la ficha del inventario.</div>";
    exit;
}

/* -------------------- Cargar datos de inventario (seguro) -------------------- */
$inv = null;
if ($stmt = $mysqli->prepare("SELECT id, codigo_g, producto, marca, modelo, serial, ubicacion, grado, disposicion, estado, tecnico_id, fecha_ingreso FROM bodega_inventario WHERE id = ? LIMIT 1")) {
    $stmt->bind_param('i', $inventario_id);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        if ($res->num_rows === 0) {
            echo "<div style='padding:20px;font-family:Arial,Helvetica,sans-serif'>Inventario no encontrado (id={$inventario_id}).</div>";
            exit;
        }
        $inv = $res->fetch_assoc();
        $res->free();
    } else {
        echo "<div style='padding:20px;font-family:Arial,Helvetica,sans-serif'>Error al ejecutar consulta de inventario.</div>";
        exit;
    }
    $stmt->close();
} else {
    echo "<div style='padding:20px;font-family:Arial,Helvetica,sans-serif'>Error preparando consulta de inventario: " . htmlspecialchars($mysqli->error) . "</div>";
    exit;
}

/* -------------------- Cargar último registro de bodega_mantenimiento (precarga) -------------------- */
$lastMaintenance = [];
if ($stmt = $mysqli->prepare("SELECT * FROM bodega_mantenimiento WHERE inventario_id = ? ORDER BY fecha_registro DESC LIMIT 1")) {
    $stmt->bind_param('i', $inventario_id);
    if ($stmt->execute()) {
        $res = $stmt->get_result();
        if ($res->num_rows) $lastMaintenance = $res->fetch_assoc();
        $res->free();
    }
    $stmt->close();
}

/* -------------------- Cargar lista de areas (intenta bodega_areas, fallback histórico) -------------------- */
$areas = [];
if ($rs = $mysqli->query("SELECT id, nombre FROM bodega_areas ORDER BY nombre")) {
    while ($r = $rs->fetch_assoc()) $areas[] = $r;
    $rs->free();
}
if (empty($areas)) {
    if ($rs2 = $mysqli->query("SELECT DISTINCT area_remite FROM bodega_mantenimiento WHERE area_remite IS NOT NULL AND area_remite != ''")) {
        while ($r2 = $rs2->fetch_assoc()) {
            $areas[] = ['id'=>null,'nombre'=>$r2['area_remite']];
        }
        $rs2->free();
    }
}

/* -------------------- Lista de técnicos (roles 5,6,7) -------------------- */
$techs = [];
if ($rs = $mysqli->query("SELECT id, nombre FROM usuarios WHERE rol IN (5,6,7) ORDER BY nombre")) {
    while ($r = $rs->fetch_assoc()) $techs[] = $r;
    $rs->free();
}

/* -------------------- Helper para precarga segura -------------------- */
function val($arr, $k, $default='') {
    if (isset($_POST[$k])) return htmlspecialchars($_POST[$k]);
    if (!empty($arr) && isset($arr[$k])) return htmlspecialchars($arr[$k]);
    return htmlspecialchars($default);
}

/* -------------------- Defaults -------------------- */
$defaults = [
  'limpieza_electronico' => 'pendiente',
  'obs_limpieza_electronico' => '',
  'mantenimiento_crema_disciplinaria' => 'pendiente',
  'obs_mantenimiento_crema' => '',
  'mantenimiento_partes' => 'pendiente',
  'cambio_piezas' => 'no',
  'piezas_cambiadas' => '',
  'proceso_reconstruccion' => 'no',
  'parte_reconstruida' => '',
  'limpieza_general' => 'pendiente',
  'remite_otra_area' => 'no',
  'area_remite' => '',
  'proceso_electronico' => '',
  'observaciones_globales' => '',
  'tecnico_diagnostico' => ''
];

?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Limpieza y Mantenimiento — TRIAGE 2</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <style>
    /* Estilo oscuro - inspirado en tu referencia */
    :root{ --bg:#0f1113; --panel:#0b0c0d; --accent:#00c9d0; --muted:#9aa3a8; --card-border: rgba(0,201,208,0.15); --input-bg:#0b0f10; --label-color:#90f0ec; }
    *{box-sizing:border-box}
    body{margin:0;background:var(--bg);color:#e6eef0;font-family: 'Courier New', monospace;}
    .wrap{max-width:1000px;margin:28px auto;padding:18px}
    .header{background:#cfeeea;color:#042;padding:18px;border-radius:6px 6px 0 0}
    .panel{background:var(--panel);border:1px solid var(--card-border);padding:18px;border-radius:6px;margin-top:8px}
    .section-title{color:var(--accent);font-weight:700;margin:10px 0;text-transform:uppercase;font-size:12px}
    label{display:block;font-size:12px;color:var(--label-color);margin-bottom:6px;text-transform:uppercase}
    .field{margin-bottom:12px}
    select,input[type="text"],textarea{width:100%;padding:10px;border-radius:4px;background:var(--input-bg);border:1px solid rgba(0,201,208,0.25);color:#e8f6f6;font-size:13px}
    .grid2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
    .btn{display:inline-block;padding:10px 14px;border-radius:6px;background:var(--accent);color:#042;border:none;cursor:pointer;font-weight:700}
    .hidden{display:none}
    .alert{padding:10px;border-radius:6px;margin-top:12px}
    .alert-success{background:#062b26;color:#bff1e9}
    .alert-error{background:#2b0606;color:#ffbdbd}
    @media(max-width:820px){ .grid2{grid-template-columns:1fr} }
  </style>
</head>
<body>
  <div class="wrap">
    <div class="header"><h1 style="margin:0;letter-spacing:2px">LIMPIEZA Y MANTENIMIENTO</h1></div>

    <div class="panel">
      <div class="section-title">SECCIÓN DE LIMPIEZA Y MANTENIMIENTO</div>

      <!-- Info equipo -->
      <div style="display:flex;justify-content:space-between;gap:12px;margin-bottom:12px">
        <div>
          <div style="color:var(--muted)">CÓDIGO</div>
          <div style="font-weight:700;text-transform:uppercase"><?php echo htmlspecialchars($inv['codigo_g']); ?></div>
          <div class="small-muted"><?php echo htmlspecialchars($inv['producto'].' — '.$inv['marca'].' / '.$inv['modelo']); ?></div>
        </div>
        <div style="text-align:right">
          <div style="color:var(--muted)">SERIAL</div>
          <div style="font-weight:700;text-transform:uppercase"><?php echo htmlspecialchars($inv['serial']); ?></div>
          <div class="small-muted"><?php echo 'UBIC: '.htmlspecialchars($inv['ubicacion']); ?></div>
        </div>
      </div>

      <form id="triageForm" novalidate>
        <input type="hidden" name="inventario_id" value="<?php echo (int)$inv['id']; ?>">

        <div class="field">
          <label for="tecnico_diagnostico">Técnico diagnóstico</label>
          <select id="tecnico_diagnostico" name="tecnico_diagnostico">
            <option value="">-- seleccionar --</option>
            <?php foreach ($techs as $t): 
                $sel = (!empty($lastMaintenance['tecnico_diagnostico']) && $lastMaintenance['tecnico_diagnostico'] == $t['id']) ? 'selected' : '';
            ?>
              <option value="<?php echo (int)$t['id']; ?>" <?php echo $sel; ?>><?php echo htmlspecialchars($t['nombre']); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="section-title">PROCESO DE LIMPIEZA</div>
        <div class="grid2">
          <div class="field">
            <label for="limpieza_electronico">Limpieza electrónico</label>
            <select id="limpieza_electronico" name="limpieza_electronico">
              <option value="realizada" <?php if(val($lastMaintenance,'limpieza_electronico',$defaults['limpieza_electronico'])==='realizada') echo 'selected'; ?>>REALIZADA</option>
              <option value="pendiente" <?php if(val($lastMaintenance,'limpieza_electronico',$defaults['limpieza_electronico'])==='pendiente') echo 'selected'; ?>>PENDIENTE</option>
              <option value="no_aplica" <?php if(val($lastMaintenance,'limpieza_electronico',$defaults['limpieza_electronico'])==='no_aplica') echo 'selected'; ?>>N/A</option>
            </select>
            <div id="obs_limpieza_block" class="<?php if(val($lastMaintenance,'limpieza_electronico',$defaults['limpieza_electronico'])!=='realizada') echo 'hidden'; ?>" style="margin-top:8px">
              <label for="obs_limpieza">Observaciones limpieza</label>
              <textarea id="obs_limpieza" name="obs_limpieza_electronico" rows="2"><?php echo val($lastMaintenance,'observaciones_limpieza_electronico',''); ?></textarea>
            </div>
          </div>

          <div class="field">
            <label for="mantenimiento_crema">Cambio de crema disciplinaria</label>
            <select id="mantenimiento_crema" name="mantenimiento_crema_disciplinaria">
              <option value="realizada" <?php if(val($lastMaintenance,'mantenimiento_crema_disciplinaria',$defaults['mantenimiento_crema_disciplinaria'])==='realizada') echo 'selected'; ?>>REALIZADA</option>
              <option value="pendiente" <?php if(val($lastMaintenance,'mantenimiento_crema_disciplinaria',$defaults['mantenimiento_crema_disciplinaria'])==='pendiente') echo 'selected'; ?>>PENDIENTE</option>
              <option value="no_aplica" <?php if(val($lastMaintenance,'mantenimiento_crema_disciplinaria',$defaults['mantenimiento_crema_disciplinaria'])==='no_aplica') echo 'selected'; ?>>N/A</option>
            </select>
            <div id="obs_crema_block" class="<?php if(val($lastMaintenance,'mantenimiento_crema_disciplinaria',$defaults['mantenimiento_crema_disciplinaria'])!=='realizada') echo 'hidden'; ?>" style="margin-top:8px">
              <label for="obs_crema">Observaciones crema</label>
              <textarea id="obs_crema" name="obs_mantenimiento_crema"><?php echo val($lastMaintenance,'observaciones_mantenimiento_crema',''); ?></textarea>
            </div>
          </div>
        </div>

        <div class="section-title">PROCESO DE MANTENIMIENTO</div>
        <div class="grid2">
          <div class="field">
            <label for="mantenimiento_partes">Mantenimiento de partes</label>
            <select id="mantenimiento_partes" name="mantenimiento_partes">
              <option value="realizada" <?php if(val($lastMaintenance,'mantenimiento_partes',$defaults['mantenimiento_partes'])==='realizada') echo 'selected'; ?>>REALIZADA</option>
              <option value="pendiente" <?php if(val($lastMaintenance,'mantenimiento_partes',$defaults['mantenimiento_partes'])==='pendiente') echo 'selected'; ?>>PENDIENTE</option>
              <option value="no_aplica" <?php if(val($lastMaintenance,'mantenimiento_partes',$defaults['mantenimiento_partes'])==='no_aplica') echo 'selected'; ?>>N/A</option>
            </select>
          </div>

          <div class="field">
            <label for="cambio_piezas">Cambio de piezas que no funcionan</label>
            <select id="cambio_piezas" name="cambio_piezas">
              <option value="no" <?php if(val($lastMaintenance,'cambio_piezas',$defaults['cambio_piezas'])==='no') echo 'selected'; ?>>NO</option>
              <option value="si" <?php if(val($lastMaintenance,'cambio_piezas',$defaults['cambio_piezas'])==='si') echo 'selected'; ?>>SI</option>
            </select>
            <div id="piezas_block" class="<?php if(val($lastMaintenance,'cambio_piezas',$defaults['cambio_piezas'])!=='si') echo 'hidden'; ?>" style="margin-top:8px">
              <label for="piezas_cambiadas">¿Qué piezas se cambiaron?</label>
              <input id="piezas_cambiadas" name="piezas_cambiadas" type="text" value="<?php echo val($lastMaintenance,'piezas_solicitadas_cambiadas',$defaults['piezas_cambiadas']); ?>">
            </div>
          </div>
        </div>

        <div class="section-title">PROCESO DE RECONSTRUCCIÓN</div>
        <div class="grid2">
          <div class="field">
            <label for="reconstruccion">Reconstrucción</label>
            <select id="reconstruccion" name="proceso_reconstruccion">
              <option value="no" <?php if(val($lastMaintenance,'proceso_reconstruccion',$defaults['proceso_reconstruccion'])==='no') echo 'selected'; ?>>NO</option>
              <option value="si" <?php if(val($lastMaintenance,'proceso_reconstruccion',$defaults['proceso_reconstruccion'])==='si') echo 'selected'; ?>>SI</option>
            </select>
            <div id="parte_block" class="<?php if(val($lastMaintenance,'proceso_reconstruccion',$defaults['proceso_reconstruccion'])!=='si') echo 'hidden'; ?>" style="margin-top:8px">
              <label for="parte_reconstruida">Parte reconstruida</label>
              <input id="parte_reconstruida" name="parte_reconstruida" type="text" value="<?php echo val($lastMaintenance,'parte_reconstruida',$defaults['parte_reconstruida']); ?>">
            </div>
          </div>

          <div class="field">
            <label for="limpieza_general">Limpieza general</label>
            <select id="limpieza_general" name="limpieza_general">
              <option value="realizada" <?php if(val($lastMaintenance,'limpieza_general',$defaults['limpieza_general'])==='realizada') echo 'selected'; ?>>REALIZADA</option>
              <option value="pendiente" <?php if(val($lastMaintenance,'limpieza_general',$defaults['limpieza_general'])==='pendiente') echo 'selected'; ?>>PENDIENTE</option>
              <option value="no_aplica" <?php if(val($lastMaintenance,'limpieza_general',$defaults['limpieza_general'])==='no_aplica') echo 'selected'; ?>>N/A</option>
            </select>
          </div>
        </div>

        <div class="section-title">OTRA ÁREA</div>
        <div class="field">
          <label for="remite_otra_area">¿Remite a otra área?</label>
          <select id="remite_otra_area" name="remite_otra_area">
            <option value="no" <?php if(val($lastMaintenance,'remite_otra_area',$defaults['remite_otra_area'])==='no') echo 'selected'; ?>>NO</option>
            <option value="si" <?php if(val($lastMaintenance,'remite_otra_area',$defaults['remite_otra_area'])==='si') echo 'selected'; ?>>SI</option>
          </select>
        </div>

        <div id="area_block" class="field <?php if(val($lastMaintenance,'remite_otra_area',$defaults['remite_otra_area'])!=='si') echo 'hidden'; ?>">
          <label for="area_remite">¿A qué área(s) remite?</label>
          <select id="area_remite" name="area_remite">
            <option value="">-- seleccionar --</option>
            <?php foreach ($areas as $a):
                $areaVal = (isset($a['id']) && $a['id']) ? $a['id'].'| '.$a['nombre'] : $a['nombre'];
                $lmArea = $lastMaintenance['area_remite'] ?? '';
                $selected = ($lmArea && ($lmArea === $areaVal || $lmArea === $a['nombre'] || $lmArea === (string)($a['id'] ?? ''))) ? 'selected' : '';
            ?>
              <option value="<?php echo htmlspecialchars($areaVal); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($areaVal); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="proceso_electronico">Proceso electrónico (detalle)</label>
          <textarea id="proceso_electronico" name="proceso_electronico" rows="3"><?php echo val($lastMaintenance,'proceso_electronico',$defaults['proceso_electronico']); ?></textarea>
        </div>

        <div class="field">
          <label for="observaciones_globales">Observaciones globales</label>
          <textarea id="observaciones_globales" name="observaciones_globales" rows="3"><?php echo val($lastMaintenance,'observaciones_globales',$defaults['observaciones_globales']); ?></textarea>
        </div>

        <div id="alert_ok" class="alert alert-success hidden">Guardado correctamente</div>
        <div id="alert_err" class="alert alert-error hidden"></div>

        <div style="text-align:right;margin-top:14px">
          <button type="button" id="btnSave" class="btn">GUARDAR</button>
        </div>
      </form>
    </div>
  </div>

<script>
(function(){
  // helpers
  function $(s){ return document.querySelector(s); }
  function show(el){ el.classList.remove('hidden'); }
  function hide(el){ el.classList.add('hidden'); }

  function toggle(selectSel, blockSel, showOn){
    var sel = document.querySelector(selectSel);
    var block = document.querySelector(blockSel);
    if (!sel || !block) return;
    var v = sel.value;
    if (v === showOn || v === 'si') show(block); else hide(block);
  }

  // Bind events
  var ids = ['#cambio_piezas','#remite_otra_area','#reconstruccion','#limpieza_electronico','#mantenimiento_crema'];
  ids.forEach(function(id){
    var el = document.querySelector(id);
    if (el) el.addEventListener('change', function(){
      if (id === '#cambio_piezas') toggle('#cambio_piezas','#piezas_block','si');
      if (id === '#remite_otra_area') toggle('#remite_otra_area','#area_block','si');
      if (id === '#reconstruccion') toggle('#reconstruccion','#parte_block','si');
      if (id === '#limpieza_electronico') toggle('#limpieza_electronico','#obs_limpieza_block','realizada');
      if (id === '#mantenimiento_crema') toggle('#mantenimiento_crema','#obs_crema_block','realizada');
    });
  });

  // initial triggers
  ids.forEach(function(id){ var el=document.querySelector(id); if(el) el.dispatchEvent(new Event('change')); });

  // validation
  function validate(){
    var inv = document.querySelector('input[name="inventario_id"]').value;
    if (!inv || parseInt(inv) <= 0) { showError('inventario_id inválido'); return false; }
    var cambio = document.getElementById('cambio_piezas');
    if (cambio && cambio.value === 'si') {
      var p = document.getElementById('piezas_cambiadas').value.trim();
      if (!p) { showError('Debe indicar qué piezas se cambiaron'); return false; }
    }
    var rem = document.getElementById('remite_otra_area');
    if (rem && rem.value === 'si') {
      var a = document.getElementById('area_remite').value.trim();
      if (!a) { showError('Debe especificar a qué área remite'); return false; }
    }
    return true;
  }

  function showError(msg){ var e=document.getElementById('alert_err'); e.textContent = msg; show(e); setTimeout(()=>hide(e),8000); }
  function showOk(msg){ var e=document.getElementById('alert_ok'); e.textContent = msg||'Guardado correctamente'; show(e); setTimeout(()=>hide(e),5000); }

  // submit AJAX
  document.getElementById('btnSave').addEventListener('click', function(){
    if (!validate()) return;
    var btn = this; btn.disabled = true; btn.textContent = 'Guardando...';

    var form = document.getElementById('triageForm');
    var fd = new FormData(form);
    var params = new URLSearchParams();
    for (var pair of fd.entries()) params.append(pair[0], pair[1]);

    fetch('../../backend/php/st_triage2.php', {
      method: 'POST',
      headers: { 'Accept': 'application/json' },
      body: params
    }).then(function(resp){
      return resp.json().catch(function(){ throw new Error('Respuesta inválida del servidor'); });
    }).then(function(json){
      if (json && json.status === 'ok') {
        showOk(json.message || 'Guardado');
        setTimeout(function(){ location.reload(); }, 900);
      } else {
        showError((json && json.message) ? json.message : 'Error en servidor');
      }
    }).catch(function(err){
      showError(err.message || 'Error en la petición');
    }).finally(function(){ btn.disabled = false; btn.textContent = 'GUARDAR'; });
  });

})();
</script>
</body>
</html>
