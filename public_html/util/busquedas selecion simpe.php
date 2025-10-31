<!-- Reemplaza esta sección en tu formulario original -->
<div class="row">
    <div class="col-md-6 col-lg-6">
        <label for="responsable">Responsable<span class="text-danger">*</span></label>
        <select class="form-control" required name="responsable">
            <option value="">----------Seleccione Responsable------------</option>
            <?php
                // Consulta para obtener usuarios con rol 5 y 6
                $stmt_responsables = $connect->prepare("SELECT id, nombre, rol FROM usuarios WHERE rol IN (5, 6) AND estado = '1' ORDER BY nombre ASC");
                $stmt_responsables->execute();
                while($row_resp = $stmt_responsables->fetch(PDO::FETCH_ASSOC)) {
                    $rol_texto = ($row_resp['rol'] == 5) ? 'Técnico' : 'Soporte Técnico';
                    ?>
                    <option value="<?php echo htmlspecialchars($row_resp['nombre']); ?>">
                        <?php echo htmlspecialchars($row_resp['nombre']); ?> - <?php echo $rol_texto; ?>
                    </option>
                    <?php
                }
            ?>
        </select>
    </div>
    <div class="col-md-6 col-lg-6">
        <div class="form-group">
            <label for="servtxt">Observación del Técnico<span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="servtxt" required placeholder="Ingrese observación del servicio">
        </div>
    </div>
</div>
<!-- ************************************************** -->
<div class="col-md-4 col-lg-4">
    <div class="form-group">
        <label for="email">Clientes<span class="text-danger">*</span></label>
        <select class="form-control" required name="txtcli">
            <option value="">----------Seleccione------------</option>
            <?php
                require '../../config/ctconex.php';
                $stmt = $connect->prepare("SELECT * FROM clientes where estad='Activo' order by idclie desc");
                $stmt->execute();
                while($row=$stmt->fetch(PDO::FETCH_ASSOC))
                    {
                        extract($row);
                        ?>
            <option value="<?php echo $idclie; ?>"><?php echo $nomcli; ?>
                <?php echo $apecli; ?></option>
            <?php
                    }
            ?>
        </select>
    </div>
</div>
