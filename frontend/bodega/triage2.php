<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Limpieza y Mantenimiento</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-cyan: #00d4ff;
            --secondary-cyan: #00a8cc;
            --dark-bg: #1a1a1a;
            --card-bg: #2d2d2d;
            --border-color: #444;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
        }

        body {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #2d2d2d 100%);
            color: #ffffff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
        }

        .dashboard-header {
            background: linear-gradient(90deg, var(--primary-cyan) 0%, var(--secondary-cyan) 100%);
            color: #000;
            padding: 1rem 0;
            text-align: center;
            font-weight: bold;
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            box-shadow: 0 4px 15px rgba(0, 212, 255, 0.3);
        }

        .stats-row {
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: var(--primary-cyan);
        }

        .stat-label {
            font-size: 0.9rem;
            color: #ccc;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-top: 2rem;
        }

        .ficha-equipo, .mantenimiento-form {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4);
        }

        .section-title {
            color: var(--primary-cyan);
            font-size: 1.3rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 1.5rem;
            text-align: center;
            border-bottom: 2px solid var(--primary-cyan);
            padding-bottom: 0.5rem;
        }

        .search-container {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .form-control, .form-select {
            background: #3d3d3d;
            border: 1px solid var(--border-color);
            color: #fff;
            border-radius: 8px;
        }

        .form-control:focus, .form-select:focus {
            background: #3d3d3d;
            border-color: var(--primary-cyan);
            color: #fff;
            box-shadow: 0 0 0 0.2rem rgba(0, 212, 255, 0.25);
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary-cyan), var(--secondary-cyan));
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(45deg, var(--secondary-cyan), var(--primary-cyan));
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 212, 255, 0.4);
        }

        .btn-success {
            background: linear-gradient(45deg, var(--success), #34ce57);
            border: none;
            border-radius: 8px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .equipment-card {
            background: #3d3d3d;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        .equipment-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .equipment-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(45deg, var(--primary-cyan), var(--secondary-cyan));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #000;
        }

        .equipment-info h5 {
            color: var(--primary-cyan);
            margin-bottom: 0.5rem;
        }

        .equipment-tests {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin: 1rem 0;
        }

        .test-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background: #2a2a2a;
            border-radius: 5px;
        }

        .badge {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
        }

        .badge-success { background: var(--success); }
        .badge-warning { background: var(--warning); color: #000; }
        .badge-danger { background: var(--danger); }
        .badge-info { background: var(--info); }
        .badge-secondary { background: #6c757d; }

        .maintenance-table {
            width: 100%;
            margin-top: 1rem;
        }

        .maintenance-table th {
            background: var(--primary-cyan);
            color: #000;
            padding: 1rem 0.75rem;
            font-weight: bold;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }

        .maintenance-table td {
            padding: 0.75rem;
            border-bottom: 1px solid var(--border-color);
        }

        .process-row {
            background: #3d3d3d;
        }

        .process-row:hover {
            background: #4d4d4d;
        }

        .alert {
            border-radius: 10px;
            border: none;
            margin-top: 1rem;
        }

        .alert-info {
            background: rgba(23, 162, 184, 0.2);
            color: #ffffff;
            border-left: 4px solid var(--info);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #888;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            border: 3px solid #444;
            border-top: 3px solid var(--primary-cyan);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1050;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            min-width: 300px;
        }

        .toast-success {
            border-left: 4px solid var(--success);
        }

        .toast-error {
            border-left: 4px solid var(--danger);
        }

        @media (max-width: 768px) {
            .main-content {
                grid-template-columns: 1fr;
            }
            
            .equipment-tests {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="dashboard-header">
        <i class="fas fa-tools"></i> Limpieza y Mantenimiento
    </div>

    <div class="container-fluid py-4">
        <!-- Stats Row -->
        <div class="row stats-row">
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-number" id="totalAsignados">103</div>
                    <div class="stat-label">Total Equipos Asignados</div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <div class="stat-number" id="totalProcesados">84</div>
                    <div class="stat-label">Total Equipos Procesados</div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Left Column: Equipment Info -->
            <div class="ficha-equipo">
                <h3 class="section-title">
                    <i class="fas fa-laptop"></i> Ficha de Equipo
                </h3>

                <!-- Search -->
                <div class="search-container">
                    <input type="text" 
                           class="form-control flex-grow-1" 
                           id="codigoSearch" 
                           placeholder="Buscar por código (ej: EQ001)">
                    <button class="btn btn-primary" onclick="buscarEquipo()">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>

                <!-- Loading -->
                <div class="loading" id="loadingEquipo">
                    <div class="spinner"></div>
                    <p>Buscando equipo...</p>
                </div>

                <!-- Equipment Details -->
                <div id="equipmentDetails" class="empty-state">
                    <i class="fas fa-search"></i>
                    <p>Ingresa un código para buscar el equipo</p>
                </div>
            </div>

            <!-- Right Column: Maintenance Form -->
            <div class="mantenimiento-form">
                <h3 class="section-title">
                    <i class="fas fa-wrench"></i> Limpieza y Mantenimiento
                </h3>

                <form id="mantenimientoForm">
                    <input type="hidden" id="inventarioId" name="inventario_id">
                    
                    <table class="table maintenance-table">
                        <thead>
                            <tr>
                                <th>Proceso</th>
                                <th>Estado</th>
                                <th>¿Qué piezas?</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="process-row">
                                <td><strong>Limpieza Electrónica</strong></td>
                                <td>
                                    <select class="form-select" name="limpieza_electronica">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="realizado">Realizado</option>
                                        <option value="rechazado">Rechazado</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="piezas_limpieza_electronica" placeholder="Detallar piezas">
                                </td>
                            </tr>
                            <tr class="process-row">
                                <td><strong>Mantenimiento</strong></td>
                                <td>
                                    <select class="form-select" name="mantenimiento">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="realizado">Realizado</option>
                                        <option value="rechazado">Rechazado</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="piezas_mantenimiento" placeholder="Detallar piezas">
                                </td>
                            </tr>
                            <tr class="process-row">
                                <td><strong>Reconstrucción</strong></td>
                                <td>
                                    <select class="form-select" name="reconstruccion">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="realizado">Realizado</option>
                                        <option value="rechazado">Rechazado</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="piezas_reconstruccion" placeholder="Detallar piezas">
                                </td>
                            </tr>
                            <tr class="process-row">
                                <td><strong>Limpieza General</strong></td>
                                <td>
                                    <select class="form-select" name="limpieza_general">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="realizado">Realizado</option>
                                        <option value="rechazado">Rechazado</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="piezas_limpieza_general" placeholder="Detallar piezas">
                                </td>
                            </tr>
                            <tr class="process-row">
                                <td><strong>Remisión</strong></td>
                                <td>
                                    <select class="form-select" name="remision">
                                        <option value="pendiente">Pendiente</option>
                                        <option value="realizado">Realizado</option>
                                        <option value="rechazado">Rechazado</option>
                                    </select>
                                </td>
                                <td>
                                    <input type="text" class="form-control" name="piezas_remision" placeholder="Detallar piezas">
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="row mt-4">
                        <div class="col-md-6">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" name="observaciones" rows="3" placeholder="Observaciones generales"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Partes Solicitadas</label>
                            <textarea class="form-control" name="partes_solicitadas" rows="3" placeholder="Lista de partes solicitadas"></textarea>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-label">Técnico Responsable</label>
                            <select class="form-select" name="tecnico_id" required>
                                <option value="">Seleccionar técnico...</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-success w-100" disabled id="guardarBtn">
                                <i class="fas fa-save"></i> Guardar Mantenimiento
                            </button>
                        </div>
                    </div>
                </form>

                <div class="alert alert-info" style="display: none;" id="infoAlert">
                    <i class="fas fa-info-circle"></i> 
                    Selecciona un equipo para habilitar el formulario de mantenimiento
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div id="toastContainer"></div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Función helper para sanitizar datos
        function safe(value, defaultValue = 'N/A') {
            if (value === null || value === undefined || value === '') {
                return defaultValue;
            }
            return String(value).trim();
        }

        // Variables globales
        let currentEquipmentId = null;
        let searchTimeout = null;

        // CSRF Token simple
        let csrfToken = Math.random().toString(36).substring(2);

        // Inicializar la aplicación
        document.addEventListener('DOMContentLoaded', function() {
            loadTechnicians();
            setupSearchDebounce();
            setupFormValidation();
            
            // Mostrar mensaje inicial
            document.getElementById('infoAlert').style.display = 'block';
        });

        // Configurar búsqueda con debounce
        function setupSearchDebounce() {
            const searchInput = document.getElementById('codigoSearch');
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const codigo = this.value.trim();
                
                if (codigo.length >= 3) {
                    searchTimeout = setTimeout(() => {
                        buscarEquipo();
                    }, 300);
                } else if (codigo.length === 0) {
                    resetEquipmentDisplay();
                }
            });

            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    buscarEquipo();
                }
            });
        }

        // Cargar técnicos para el select
        function loadTechnicians() {
            const technicianSelect = document.querySelector('select[name="tecnico_id"]');
            
            // Datos de técnicos simulados basados en la BD
            const technicians = [
                {id: 8, nombre: 'Sergio Lara'},
                {id: 9, nombre: 'Juan González'},
                {id: 10, nombre: 'Luis González'},
                {id: 12, nombre: 'Fabian Sanchez'},
                {id: 13, nombre: 'José Borda'},
                {id: 14, nombre: 'Felipe Romero'},
                {id: 15, nombre: 'Rodrigo Martínez'},
                {id: 16, nombre: 'Deivi Lopez'},
                {id: 32, nombre: 'frank5'},
                {id: 33, nombre: 'Tecnico FranciscoQV'},
                {id: 34, nombre: 'frank7'}
            ];

            technicians.forEach(tech => {
                const option = document.createElement('option');
                option.value = tech.id;
                option.textContent = tech.nombre;
                technicianSelect.appendChild(option);
            });
        }

        // Buscar equipo por código
        async function buscarEquipo() {
            const codigo = document.getElementById('codigoSearch').value.trim();
            
            if (!codigo) {
                showToast('Por favor ingresa un código', 'error');
                return;
            }

            const loading = document.getElementById('loadingEquipo');
            const details = document.getElementById('equipmentDetails');
            
            loading.style.display = 'block';
            details.innerHTML = '';

            try {
                // Simular llamada AJAX - en implementación real usarías fetch()
                setTimeout(() => {
                    // Datos simulados basados en la BD
                    const equipmentData = getSimulatedEquipmentData(codigo);
                    
                    loading.style.display = 'none';
                    
                    if (equipmentData) {
                        displayEquipmentDetails(equipmentData);
                        enableMaintenanceForm(equipmentData.id);
                    } else {
                        showEquipmentNotFound();
                    }
                }, 800);

            } catch (error) {
                loading.style.display = 'none';
                showToast('Error al buscar el equipo', 'error');
                console.error('Error:', error);
            }
        }

        // Obtener datos simulados del equipo
        function getSimulatedEquipmentData(codigo) {
            // Datos basados en la BD proporcionada
            const equipos = {
                'EQ001': {
                    id: 1,
                    codigo_g: 'EQ001',
                    serial: 'DL123456789',
                    producto: 'Portatil',
                    marca: 'Dell',
                    modelo: 'Latitude 5520',
                    procesador: 'Intel i5-1135G7',
                    ram: '8GB',
                    disco: '256GB SSD',
                    pulgadas: '15.6',
                    observaciones: 'Equipo en buen estado',
                    grado: 'A',
                    ubicacion: 'Principal',
                    tecnico_nombre: 'Tecnico FranciscoQV',
                    diagnostico: {
                        camara: 'Funcional',
                        teclado: 'Funcional',
                        parlantes: 'Funcional',
                        bateria: '85% capacidad',
                        microfono: 'Funcional',
                        disco: 'Estado excelente'
                    }
                },
                'EQ002': {
                    id: 2,
                    codigo_g: 'EQ002',
                    serial: 'HP987654321',
                    producto: 'Desktop',
                    marca: 'HP',
                    modelo: 'EliteDesk 800',
                    procesador: 'Intel i7-10700',
                    ram: '16GB',
                    disco: '512GB SSD',
                    pulgadas: '16',
                    observaciones: 'EQUIPO LISTO',
                    grado: 'A',
                    ubicacion: 'Principal',
                    tecnico_nombre: 'frank7',
                    diagnostico: {
                        camara: 'N/A',
                        teclado: 'Funcional',
                        parlantes: 'Funcional',
                        bateria: 'N/A',
                        microfono: 'N/A',
                        disco: 'Buen estado'
                    }
                },
                'EQ003': {
                    id: 3,
                    codigo_g: 'EQ003',
                    serial: 'LN456789123',
                    producto: 'AIO',
                    marca: 'Lenovo',
                    modelo: 'ThinkCentre M90a',
                    procesador: 'Intel i5-10400T',
                    ram: '8GB',
                    disco: '1TB HDD',
                    pulgadas: '23.8',
                    observaciones: 'Pantalla con rayones menores',
                    grado: 'C',
                    ubicacion: 'Cúcuta',
                    tecnico_nombre: 'N/A',
                    diagnostico: {
                        camara: 'Funcional',
                        teclado: 'Tecla Space pegajosa',
                        parlantes: 'Funcional',
                        bateria: 'N/A',
                        microfono: 'Funcional',
                        disco: 'Fragmentación alta'
                    }
                }
            };

            return equipos[codigo.toUpperCase()] || null;
        }

        // Mostrar detalles del equipo
        function displayEquipmentDetails(equipment) {
            const details = document.getElementById('equipmentDetails');
            const diagnostico = equipment.diagnostico || {};
            
            const html = `
                <div class="equipment-card">
                    <div class="equipment-header">
                        <div class="equipment-icon">
                            <i class="fas ${getEquipmentIcon(equipment.producto)}"></i>
                        </div>
                        <div class="equipment-info">
                            <h5>${safe(equipment.codigo_g)}</h5>
                            <p class="mb-0 text-muted">Serial: ${safe(equipment.serial)}</p>
                        </div>
                    </div>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <small class="text-muted">Producto</small>
                            <p class="mb-1">${safe(equipment.producto)} ${safe(equipment.marca)}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Modelo</small>
                            <p class="mb-1">${safe(equipment.modelo)}</p>
                        </div>
                    </div>
                    
                    <div class="equipment-tests">
                        <div class="test-item">
                            <span>Cámara</span>
                            <span class="badge ${getBadgeClass(diagnostico.camara)}">${safe(diagnostico.camara)}</span>
                        </div>
                        <div class="test-item">
                            <span>Teclado</span>
                            <span class="badge ${getBadgeClass(diagnostico.teclado)}">${safe(diagnostico.teclado)}</span>
                        </div>
                        <div class="test-item">
                            <span>Parlantes</span>
                            <span class="badge ${getBadgeClass(diagnostico.parlantes)}">${safe(diagnostico.parlantes)}</span>
                        </div>
                        <div class="test-item">
                            <span>Batería</span>
                            <span class="badge ${getBadgeClass(diagnostico.bateria)}">${safe(diagnostico.bateria)}</span>
                        </div>
                        <div class="test-item">
                            <span>Micrófono</span>
                            <span class="badge ${getBadgeClass(diagnostico.microfono)}">${safe(diagnostico.microfono)}</span>
                        </div>
                        <div class="test-item">
                            <span>Disco</span>
                            <span class="badge ${getBadgeClass(diagnostico.disco)}">${safe(diagnostico.disco)}</span>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <small class="text-muted">Observaciones</small>
                        <p class="mb-1">${safe(equipment.observaciones)}</p>
                    </div>
                    
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <small class="text-muted">Técnico que ingresó</small>
                            <p class="mb-1">${safe(equipment.tecnico_nombre)}</p>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted">Vida útil disco</small>
                            <p class="mb-1">${getDiscoLifeStatus(diagnostico.disco)}</p>
                        </div>
                    </div>
                </div>
            `;
            
            details.innerHTML = html;
        }

        // Obtener icono según tipo de equipo
        function getEquipmentIcon(producto) {
            const icons = {
                'Portatil': 'fa-laptop',
                'Desktop': 'fa-desktop',
                'AIO': 'fa-tv',
                'Monitor': 'fa-tv'
            };
            return icons[producto] || 'fa-computer';
        }

        // Obtener clase de badge según estado
        function getBadgeClass(value) {
            if (!value || value === 'N/A') return 'badge-secondary';
            
            const lowerValue = value.toLowerCase();
            if (lowerValue.includes('funcional') || lowerValue.includes('excelente') || lowerValue.includes('bueno')) {
                return 'badge-success';
            } else if (lowerValue.includes('dañad') || lowerValue.includes('no funciona')) {
                return 'badge-danger';
            } else if (lowerValue.includes('%') || lowerValue.includes('pegajosa') || lowerValue.includes('rayones')) {
                return 'badge-warning';
            }
            return 'badge-info';
        }

        // Obtener estado de vida útil del disco
        function getDiscoLifeStatus(discoState) {
            if (!discoState || discoState === 'N/A') return 'N/A';
            
            const lower = discoState.toLowerCase();
            if (lower.includes('excelente')) return '100%';
            if (lower.includes('bueno')) return '85%';
            if (lower.includes('fragmentación')) return '60%';
            return '75%';
        }

        // Mostrar mensaje de equipo no encontrado
        function showEquipmentNotFound() {
            const details = document.getElementById('equipmentDetails');
            details.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Equipo no encontrado</p>
                    <small class="text-muted">Verifica el código e intenta nuevamente</small>
                </div>
            `;
        }

        // Resetear vista de equipo
        function resetEquipmentDisplay() {
            const details = document.getElementById('equipmentDetails');
            details.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-search"></i>
                    <p>Ingresa un código para buscar el equipo</p>
                </div>
            `;
            disableMaintenanceForm();
        }

        // Habilitar formulario de mantenimiento
        function enableMaintenanceForm(equipmentId) {
            currentEquipmentId = equipmentId;
            document.getElementById('inventarioId').value = equipmentId;
            document.getElementById('guardarBtn').disabled = false;
            document.getElementById('infoAlert').style.display = 'none';
            
            // Limpiar formulario
            document.getElementById('mantenimientoForm').reset();
            document.getElementById('inventarioId').value = equipmentId;
        }

        // Deshabilitar formulario de mantenimiento
        function disableMaintenanceForm() {
            currentEquipmentId = null;
            document.getElementById('inventarioId').value = '';
            document.getElementById('guardarBtn').disabled = true;
            document.getElementById('infoAlert').style.display = 'block';
            document.getElementById('mantenimientoForm').reset();
        }

        // Configurar validación del formulario
        function setupFormValidation() {
            const form = document.getElementById('mantenimientoForm');
            
            form.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                if (!currentEquipmentId) {
                    showToast('Selecciona un equipo primero', 'error');
                    return;
                }

                const tecnicoId = document.querySelector('select[name="tecnico_id"]').value;
                if (!tecnicoId) {
                    showToast('Selecciona un técnico responsable', 'error');
                    return;
                }

                await guardarMantenimiento();
            });
        }

        // Guardar mantenimiento
        async function guardarMantenimiento() {
            const form = document.getElementById('mantenimientoForm');
            const formData = new FormData(form);
            const guardarBtn = document.getElementById('guardarBtn');
            
            // Mostrar loading en el botón
            guardarBtn.disabled = true;
            guardarBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

            try {
                // Preparar datos para envío
                const data = {
                    csrf_token: csrfToken,
                    inventario_id: currentEquipmentId,
                    tecnico_id: formData.get('tecnico_id'),
                    observaciones: formData.get('observaciones') || '',
                    partes_solicitadas: formData.get('partes_solicitadas') || '',
                    procesos: {
                        limpieza_electronica: {
                            estado: formData.get('limpieza_electronica'),
                            piezas: formData.get('piezas_limpieza_electronica') || ''
                        },
                        mantenimiento: {
                            estado: formData.get('mantenimiento'),
                            piezas: formData.get('piezas_mantenimiento') || ''
                        },
                        reconstruccion: {
                            estado: formData.get('reconstruccion'),
                            piezas: formData.get('piezas_reconstruccion') || ''
                        },
                        limpieza_general: {
                            estado: formData.get('limpieza_general'),
                            piezas: formData.get('piezas_limpieza_general') || ''
                        },
                        remision: {
                            estado: formData.get('remision'),
                            piezas: formData.get('piezas_remision') || ''
                        }
                    }
                };

                // Simular llamada AJAX - en implementación real usarías fetch()
                setTimeout(() => {
                    // Simular respuesta exitosa
                    const response = {
                        ok: true,
                        msg: 'Mantenimiento guardado correctamente'
                    };

                    if (response.ok) {
                        showToast(response.msg, 'success');
                        // Limpiar formulario pero mantener el equipo seleccionado
                        form.reset();
                        document.getElementById('inventarioId').value = currentEquipmentId;
                        
                        // Actualizar estadísticas (opcional)
                        updateStats();
                    } else {
                        showToast(response.msg || 'Error al guardar', 'error');
                    }

                    // Restaurar botón
                    guardarBtn.disabled = false;
                    guardarBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Mantenimiento';
                }, 1000);

            } catch (error) {
                console.error('Error:', error);
                showToast('Error de conexión', 'error');
                
                // Restaurar botón
                guardarBtn.disabled = false;
                guardarBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Mantenimiento';
            }
        }

        // Actualizar estadísticas
        function updateStats() {
            // Simular actualización de stats
            const totalProcesados = document.getElementById('totalProcesados');
            const currentValue = parseInt(totalProcesados.textContent);
            totalProcesados.textContent = currentValue + 1;
            
            // Animación simple
            totalProcesados.style.transform = 'scale(1.2)';
            setTimeout(() => {
                totalProcesados.style.transform = 'scale(1)';
            }, 300);
        }

        // Mostrar toast de notificación
        function showToast(message, type = 'info') {
            const toastContainer = document.getElementById('toastContainer');
            const toastId = 'toast_' + Date.now();
            
            const toastHtml = `
                <div class="toast toast-${type}" id="${toastId}" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header">
                        <i class="fas ${getToastIcon(type)} me-2"></i>
                        <strong class="me-auto">${getToastTitle(type)}</strong>
                        <small>Ahora</small>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });
            
            toast.show();
            
            // Remover del DOM después de que se oculte
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastElement.remove();
            });
        }

        // Obtener icono para toast
        function getToastIcon(type) {
            const icons = {
                'success': 'fa-check-circle',
                'error': 'fa-exclamation-circle',
                'warning': 'fa-exclamation-triangle',
                'info': 'fa-info-circle'
            };
            return icons[type] || 'fa-info-circle';
        }

        // Obtener título para toast
        function getToastTitle(type) {
            const titles = {
                'success': 'Éxito',
                'error': 'Error',
                'warning': 'Advertencia',
                'info': 'Información'
            };
            return titles[type] || 'Notificación';
        }

        // Función para generar datos de prueba adicionales (opcional)
        function generateTestData() {
            const equipos = ['EQ004', 'EQ005', 'EQ006', 'LPDA-1432'];
            const select = document.getElementById('codigoSearch');
            
            // Agregar datalist para autocompletado
            const datalist = document.createElement('datalist');
            datalist.id = 'equiposList';
            
            equipos.forEach(codigo => {
                const option = document.createElement('option');
                option.value = codigo;
                datalist.appendChild(option);
            });
            
            select.setAttribute('list', 'equiposList');
            document.body.appendChild(datalist);
        }

        // Llamar función de datos de prueba
        generateTestData();
    </script>
</body>
</html>