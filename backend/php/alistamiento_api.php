<?php
/**
 * API UNIFICADA - Alistamiento de Ventas
 * Maneja todas las operaciones AJAX en un solo archivo
 */

session_start();
header('Content-Type: application/json');
require_once '../../config/ctconex.php';

// Verificar sesión
if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        // ========== LISTAR VENTAS ==========
        case 'listar_ventas':
            $sql = "SELECT
                        av.id,
                        av.idventa,
                        av.ticket,
                        av.fecha_venta,
                        av.estado,
                        av.total_venta,
                        av.valor_abono,
                        av.saldo,
                        CONCAT(c.nomcli, ' ', IFNULL(c.apecli, '')) as cliente,
                        u.nombre as solicitante
                    FROM alistamiento_venta av
                    LEFT JOIN clientes c ON av.idcliente = c.idclie
                    LEFT JOIN usuarios u ON av.usuario_id = u.id
                    ORDER BY av.fecha_venta DESC";

            $stmt = $connect->query($sql);
            $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $ventas]);
            break;


        // ========== BUSCAR CLIENTES ==========
        case 'buscar_clientes':
            $search = $_GET['q'] ?? '';

            if (strlen($search) < 2) {
                echo json_encode(['results' => []]);
                exit;
            }

            $sql = "SELECT
                        idclie as id,
                        CONCAT(nomcli, ' ', IFNULL(apecli, ''), ' - ', numid) as text,
                        numid,
                        nomcli,
                        apecli,
                        celu,
                        correo,
                        dircli,
                        ciucli,
                        idsede
                    FROM clientes
                    WHERE numid LIKE :search
                       OR nomcli LIKE :search
                       OR apecli LIKE :search
                       OR correo LIKE :search
                       OR celu LIKE :search
                    LIMIT 10";

            $stmt = $connect->prepare($sql);
            $stmt->execute([':search' => "%$search%"]);
            $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['results' => $clientes]);
            break;


        // ========== OBTENER INFO CLIENTE ==========
        case 'obtener_cliente':
            $id = $_GET['id'] ?? 0;

            $sql = "SELECT
                        idclie,
                        numid,
                        CONCAT(nomcli, ' ', IFNULL(apecli, '')) as nombre_completo,
                        nomcli,
                        apecli,
                        celu,
                        correo,
                        dircli,
                        ciucli,
                        idsede,
                        estad
                    FROM clientes
                    WHERE idclie = :id";

            $stmt = $connect->prepare($sql);
            $stmt->execute([':id' => $id]);
            $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($cliente) {
                echo json_encode(['success' => true, 'data' => $cliente]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cliente no encontrado']);
            }
            break;


        // ========== BUSCAR INVENTARIO ==========
        case 'buscar_inventario':
            $search = $_POST['search'] ?? '';

            $sql = "SELECT
                        id,
                        codigo_g,
                        producto,
                        marca,
                        modelo,
                        procesador,
                        ram,
                        disco,
                        pulgadas,
                        tactil,
                        grado,
                        disposicion,
                        ubicacion,
                        precio,
                        serial,
                        lote
                    FROM bodega_inventario
                    WHERE estado = 'activo'
                      AND grado IN ('A', 'B')
                      AND disposicion NOT IN ('Vendido')
                      AND (
                          producto LIKE :search
                          OR marca LIKE :search
                          OR modelo LIKE :search
                          OR procesador LIKE :search
                          OR ram LIKE :search
                          OR disco LIKE :search
                          OR codigo_g LIKE :search
                          OR serial LIKE :search
                      )
                    ORDER BY fecha_ingreso DESC
                    LIMIT 50";

            $stmt = $connect->prepare($sql);
            $stmt->execute([':search' => "%$search%"]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode(['success' => true, 'data' => $productos]);
            break;


        // ========== CREAR VENTA ==========
        case 'crear_venta':
            $clienteId = $_POST['cliente_id'] ?? 0;
            $sede = $_POST['sede'] ?? '';
            $ticket = $_POST['ticket'] ?? '';
            $ubicacion = $_POST['ubicacion'] ?? '';
            $items = json_decode($_POST['items'] ?? '[]', true);
            $descuento = floatval($_POST['descuento'] ?? 0);
            $abono = floatval($_POST['abono'] ?? 0);
            $medioAbono = $_POST['medio_abono'] ?? null;
            $observacion = $_POST['observacion'] ?? '';
            $estado = $_POST['estado'] ?? 'borrador';

            // Validaciones
            if (!$clienteId || !$ticket || empty($items)) {
                echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
                exit;
            }

            // Generar ID de venta
            $sqlCount = "SELECT COUNT(*) as total FROM alistamiento_venta";
            $stmtCount = $connect->query($sqlCount);
            $count = $stmtCount->fetch(PDO::FETCH_ASSOC)['total'];
            $idventa = 'AV-' . date('Y') . '-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

            $connect->beginTransaction();

            try {
                // Insertar venta
                $sqlVenta = "INSERT INTO alistamiento_venta
                            (idventa, ticket, usuario_id, sede, idcliente, ubicacion,
                             descuento, valor_abono, medio_abono, observacion_global,
                             estado, creado_por, saldo, total_venta, subtotal)
                            VALUES
                            (:idventa, :ticket, :usuario_id, :sede, :cliente_id, :ubicacion,
                             :descuento, :abono, :medio_abono, :observacion,
                             :estado, :creado_por, 0, 0, 0)";

                $stmtVenta = $connect->prepare($sqlVenta);
                $stmtVenta->execute([
                    ':idventa' => $idventa,
                    ':ticket' => $ticket,
                    ':usuario_id' => $_SESSION['id'],
                    ':sede' => $sede,
                    ':cliente_id' => $clienteId,
                    ':ubicacion' => $ubicacion,
                    ':descuento' => $descuento,
                    ':abono' => $abono,
                    ':medio_abono' => $medioAbono,
                    ':observacion' => $observacion,
                    ':estado' => $estado,
                    ':creado_por' => $_SESSION['id']
                ]);

                $ventaId = $connect->lastInsertId();

                // Insertar items
                $sqlItem = "INSERT INTO alistamiento_venta_items
                           (alistamiento_id, item_numero, inventario_id, producto, marca,
                            modelo, procesador, ram, disco, grado, descripcion, cantidad, precio_unitario)
                           VALUES
                           (:alistamiento_id, :item_numero, :inventario_id, :producto, :marca,
                            :modelo, :procesador, :ram, :disco, :grado, :descripcion, :cantidad, :precio)";

                $stmtItem = $connect->prepare($sqlItem);

                foreach ($items as $index => $item) {
                    $stmtItem->execute([
                        ':alistamiento_id' => $ventaId,
                        ':item_numero' => $index + 1,
                        ':inventario_id' => $item['inventario_id'] ?? null,
                        ':producto' => $item['producto'],
                        ':marca' => $item['marca'] ?? null,
                        ':modelo' => $item['modelo'] ?? null,
                        ':procesador' => $item['procesador'] ?? null,
                        ':ram' => $item['ram'] ?? null,
                        ':disco' => $item['disco'] ?? null,
                        ':grado' => $item['grado'] ?? null,
                        ':descripcion' => $item['descripcion'] ?? null,
                        ':cantidad' => $item['cantidad'],
                        ':precio' => $item['precio_unitario']
                    ]);

                    // Si viene de inventario, actualizar disposición
                    if (!empty($item['inventario_id'])) {
                        $sqlUpdateInv = "UPDATE bodega_inventario
                                        SET disposicion = 'Vendido', estado = 'inactivo'
                                        WHERE id = :id";
                        $stmtUpdateInv = $connect->prepare($sqlUpdateInv);
                        $stmtUpdateInv->execute([':id' => $item['inventario_id']]);
                    }
                }

                $connect->commit();

                echo json_encode([
                    'success' => true,
                    'message' => 'Venta creada exitosamente',
                    'idventa' => $idventa,
                    'id' => $ventaId
                ]);

            } catch (Exception $e) {
                $connect->rollBack();
                echo json_encode(['success' => false, 'message' => 'Error al crear venta: ' . $e->getMessage()]);
            }
            break;


        // ========== OBTENER DETALLE VENTA ==========
        case 'obtener_venta':
            $id = $_GET['id'] ?? 0;

            // Obtener venta
            $sqlVenta = "SELECT
                            av.*,
                            CONCAT(c.nomcli, ' ', IFNULL(c.apecli, '')) as cliente,
                            c.celu as telefono_cliente,
                            c.canal_venta,
                            u.nombre as solicitante
                        FROM alistamiento_venta av
                        LEFT JOIN clientes c ON av.idcliente = c.idclie
                        LEFT JOIN usuarios u ON av.usuario_id = u.id
                        WHERE av.id = :id";

            $stmtVenta = $connect->prepare($sqlVenta);
            $stmtVenta->execute([':id' => $id]);
            $venta = $stmtVenta->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                echo json_encode(['success' => false, 'message' => 'Venta no encontrada']);
                exit;
            }

            // Obtener items
            $sqlItems = "SELECT * FROM alistamiento_venta_items WHERE alistamiento_id = :id ORDER BY item_numero";
            $stmtItems = $connect->prepare($sqlItems);
            $stmtItems->execute([':id' => $id]);
            $items = $stmtItems->fetchAll(PDO::FETCH_ASSOC);

            $venta['items'] = $items;

            echo json_encode(['success' => true, 'data' => $venta]);
            break;


        // ========== CAMBIAR ESTADO ==========
        case 'cambiar_estado':
            $id = $_POST['id'] ?? 0;
            $nuevoEstado = $_POST['estado'] ?? '';
            $observacion = $_POST['observacion'] ?? '';

            $sql = "UPDATE alistamiento_venta
                    SET estado = :estado,
                        observacion_tecnico = CONCAT(IFNULL(observacion_tecnico, ''), '\n', :obs),
                        modificado_por = :usuario_id
                    WHERE id = :id";

            $stmt = $connect->prepare($sql);
            $stmt->execute([
                ':estado' => $nuevoEstado,
                ':obs' => date('Y-m-d H:i:s') . ' - ' . $observacion,
                ':usuario_id' => $_SESSION['id'],
                ':id' => $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Estado actualizado']);
            break;


        // ========== ELIMINAR VENTA ==========
        case 'eliminar_venta':
            $id = $_POST['id'] ?? 0;

            // Verificar estado
            $sqlCheck = "SELECT estado FROM alistamiento_venta WHERE id = :id";
            $stmtCheck = $connect->prepare($sqlCheck);
            $stmtCheck->execute([':id' => $id]);
            $venta = $stmtCheck->fetch(PDO::FETCH_ASSOC);

            if (!$venta) {
                echo json_encode(['success' => false, 'message' => 'Venta no encontrada']);
                exit;
            }

            if (!in_array($venta['estado'], ['borrador', 'cancelado'])) {
                echo json_encode(['success' => false, 'message' => 'Solo se pueden eliminar ventas en borrador o canceladas']);
                exit;
            }

            $sql = "DELETE FROM alistamiento_venta WHERE id = :id";
            $stmt = $connect->prepare($sql);
            $stmt->execute([':id' => $id]);

            echo json_encode(['success' => true, 'message' => 'Venta eliminada']);
            break;


        default:
            echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            break;
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de base de datos: ' . $e->getMessage()]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
