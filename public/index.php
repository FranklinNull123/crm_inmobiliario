<?php
declare(strict_types=1);

header('Content-Type: text/html; charset=utf-8');
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    $https = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    session_set_cookie_params(['httponly' => true, 'secure' => $https, 'samesite' => 'Lax', 'path' => '/']);
    session_start();
}

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/app/bootstrap.php';
require_once BASE_PATH . '/app/Security.php';
require_once BASE_PATH . '/app/Controllers/AuthController.php';
require_once BASE_PATH . '/app/Controllers/ConfiguracionController.php';

$authController = new AuthController(new AuthModel($pdo));
$configuracionController = new ConfiguracionController(new ConfiguracionModel($pdo));
$csrfToken = $_SESSION['csrf_token'] ??= bin2hex(random_bytes(32));
$setupRequired = (new AuthModel($pdo))->bootstrapRequired();
$authError = null;

if (($_GET['action'] ?? '') === 'logout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!cms_valid_csrf($_POST['_csrf'] ?? null)) {
        http_response_code(403);
        exit('La sesión expiró. Recarga la página.');
    }
    cms_audit($pdo, 'logout', 'usuarios', (string) (cms_current_user()['id'] ?? ''));
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    header('Location: ?view=login', true, 303);
    exit;
}

$requestedView = (string) ($_GET['view'] ?? 'dashboard');
$api = $_GET['api'] ?? null;
if ($requestedView === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!cms_valid_csrf($_POST['_csrf'] ?? null)) {
            $authError = 'La sesión expiró. Recarga la página e inténtalo de nuevo.';
        } else {
            try {
                if (($_POST['action'] ?? '') === 'setup_admin' && $setupRequired) {
                    $authController->createInitialAdmin($_POST);
                    $setupRequired = false;
                    $authError = 'Superadministrador creado. Inicia sesión con tus credenciales.';
                } elseif (($_POST['action'] ?? '') === 'login' && !$setupRequired) {
                    $authenticatedUser = $authController->login($_POST);
                    if ($authenticatedUser === null) {
                        $authError = 'No se pudo iniciar sesión. Verifica tus credenciales o inténtalo más tarde.';
                    } else {
                        session_regenerate_id(true);
                        $_SESSION['user'] = $authenticatedUser;
                        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                        header('Location: ?view=dashboard', true, 303);
                        exit;
                    }
                }
            } catch (InvalidArgumentException | RuntimeException $exception) {
                $authError = $exception->getMessage();
            } catch (PDOException $exception) {
                error_log('[Auth] ' . $exception->getMessage());
                $authError = 'No se pudo completar el acceso. Verifica que la migración de Fundamentos esté instalada.';
            }
        }
    }
    if (cms_current_user() !== null && !$setupRequired) {
        header('Location: ?view=dashboard', true, 303);
        exit;
    }
    $setupRequired = (new AuthModel($pdo))->bootstrapRequired();
    require BASE_PATH . '/app/Views/auth/login.php';
    exit;
}

if ($setupRequired || cms_current_user() === null) {
    header('Location: ?view=login', true, 303);
    exit;
}

$lastActivity = (int) ($_SESSION['last_activity'] ?? time());
if (time() - $lastActivity > 1800) {
    cms_audit($pdo, 'sesion_expirada', 'usuarios', (string) (cms_current_user()['id'] ?? ''));
    $_SESSION = [];
    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    header('Location: ?view=login', true, 303);
    exit;
}
$_SESSION['last_activity'] = time();
$currentUser = cms_current_user();
$currentRoles = $currentUser['roles'] ?? [];
$hasBroadAdvisorAccess = (bool) array_intersect(['superadmin', 'administrador', 'gerencia', 'director_comercial'], $currentRoles);
$advisorScopeUserId = in_array('asesor', $currentRoles, true) && !$hasBroadAdvisorAccess ? (int) $currentUser['id'] : null;

$viewPermissions = [
    'dashboard' => 'dashboard', 'crm' => 'crm', 'campanas' => 'crm', 'alertas' => 'crm', 'cartera' => 'clientes', 'rendimiento' => 'reportes', 'expedientes' => 'clientes', 'recordatorios' => 'crm', 'notificaciones' => 'crm', 'seguimiento' => 'crm', 'tareas' => 'crm', 'agenda' => 'crm', 'pipeline' => 'crm', 'prospeccion' => 'crm', 'soporte' => 'auditoria', 'incidencias' => 'inventario', 'inventario' => 'inventario',
    'clientes' => 'clientes', 'detalle' => 'clientes', 'proyectos' => 'proyectos',
    'ventas' => 'ventas', 'caja' => 'caja', 'cobranzas' => 'caja', 'comisiones' => 'ventas', 'contratos' => 'clientes', 'reportes' => 'reportes',
    'configuracion' => 'configuracion', 'auditoria' => 'auditoria',
];
$permissionModule = $viewPermissions[$requestedView] ?? 'dashboard';
if ($api === null && !cms_has_permission($pdo, $permissionModule, 'ver')) {
    http_response_code(403);
    exit('No tienes permiso para consultar este módulo.');
}

if ($api !== null && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'message' => 'Método no permitido.'], JSON_THROW_ON_ERROR);
    exit;
}

$view = $requestedView;
$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$routes = [
    'dashboard' => BASE_PATH . '/app/Views/dashboard/index.php',
    'crm' => BASE_PATH . '/app/Views/crm/kanban.php',
    'inventario' => BASE_PATH . '/app/Views/inventario/index.php',
    'clientes' => BASE_PATH . '/app/Views/clientes/index.php',
    'detalle' => BASE_PATH . '/app/Views/clientes/detalle.php',
    'proyectos' => BASE_PATH . '/app/Views/proyectos/index.php',
    'ventas' => BASE_PATH . '/app/Views/ventas/index.php',
    'caja' => BASE_PATH . '/app/Views/caja/index.php',
    'cobranzas' => BASE_PATH . '/app/Views/cobranzas/index.php',
    'comisiones' => BASE_PATH . '/app/Views/comisiones/index.php',
    'campanas' => BASE_PATH . '/app/Views/campanas/index.php',
    'alertas' => BASE_PATH . '/app/Views/alertas/index.php',
    'cartera' => BASE_PATH . '/app/Views/cartera/index.php',
    'rendimiento' => BASE_PATH . '/app/Views/rendimiento/index.php',
    'expedientes' => BASE_PATH . '/app/Views/expedientes/index.php',
    'contratos' => BASE_PATH . '/app/Views/contratos/index.php',
    'recordatorios' => BASE_PATH . '/app/Views/recordatorios/index.php',
    'notificaciones' => BASE_PATH . '/app/Views/notificaciones/index.php',
    'seguimiento' => BASE_PATH . '/app/Views/seguimiento/index.php',
    'tareas' => BASE_PATH . '/app/Views/tareas/index.php',
    'agenda' => BASE_PATH . '/app/Views/agenda/index.php',
    'pipeline' => BASE_PATH . '/app/Views/pipeline/index.php',
    'prospeccion' => BASE_PATH . '/app/Views/prospeccion/index.php',
    'soporte' => BASE_PATH . '/app/Views/soporte/index.php',
    'incidencias' => BASE_PATH . '/app/Views/incidencias/index.php',
    'reportes' => BASE_PATH . '/app/Views/reportes/index.php',
    'configuracion' => BASE_PATH . '/app/Views/configuracion/index.php',
    'auditoria' => BASE_PATH . '/app/Views/configuracion/auditoria.php',
];

$viewFile = $routes[$view] ?? BASE_PATH . '/app/Views/layout/placeholder.php';

$salesData = [];
$recentSales = [];
$units = [];
$totalResults = 0;
$clients = [];
$sepUnits = [];
$clientDocs = [];
$leads = [];
$campaigns = [];
$advisors = [];
$leadError = null;
$projectData = ['projects' => [], 'stages' => [], 'statuses' => []];
$inventoryProjects = [];
$inventoryStages = [];
$unitStatuses = [];

$leadFeedback = $_SESSION['lead_feedback'] ?? null;
unset($_SESSION['lead_feedback']);

if ($api !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawBody = file_get_contents('php://input');
    $payload = $rawBody !== false && trim($rawBody) !== '' ? json_decode($rawBody, true) : [];
    if (!is_array($payload)) {
        http_response_code(400);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => 'JSON inválido.'], JSON_THROW_ON_ERROR);
        exit;
    }
    if (!cms_valid_csrf($_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($payload['_csrf'] ?? null))) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => 'La sesión expiró. Recarga la página.'], JSON_THROW_ON_ERROR);
        exit;
    }
    $apiPermissions = [
        'lead_etapa' => ['crm', 'editar'],
        'separacion' => ['separaciones', 'crear'],
        'configuracion' => ['configuracion', 'editar'],
        'proyectos' => ['proyectos', 'editar'],
        'inventario' => ['inventario', 'editar'],
        'ventas' => ['ventas', 'crear'],
        'caja' => ['caja', 'editar'],
        'comisiones' => ['ventas', 'ver'],
        'campanas' => ['crm', 'editar'],
        'recordatorios' => ['crm', 'editar'],
        'documentos' => ['clientes', 'editar'],
        'contactos' => ['clientes', 'editar'],
        'contratos' => ['clientes', 'editar'],
    ];
    if (!isset($apiPermissions[$api])) {
        http_response_code(404);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => 'Endpoint no encontrado.'], JSON_THROW_ON_ERROR);
        exit;
    }
    [$apiModule, $apiAction] = $apiPermissions[$api];
    if (!cms_has_permission($pdo, $apiModule, $apiAction)) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => 'No tienes permiso para esta operación.'], JSON_THROW_ON_ERROR);
        exit;
    }
    if ($api === 'configuracion') {
        header('Content-Type: application/json; charset=utf-8');
        try {
            $userId = (int) cms_current_user()['id'];
            switch ($payload['action'] ?? '') {
                case 'settings':
                    $oldSettings = $configuracionController->index()['settings'];
                    $configuracionController->updateSettings($payload, $userId);
                    cms_audit($pdo, 'configuracion_actualizada', 'configuracion_sistema', null, $oldSettings, array_intersect_key($payload, $oldSettings));
                    $result = ['ok' => true];
                    break;
                case 'catalog_status':
                    $ok = $configuracionController->updateCatalog($payload['id'] ?? null, $payload['active'] ?? null);
                    cms_audit($pdo, 'catalogo_estado_actualizado', 'catalogo_valores', (string) ($payload['id'] ?? ''), null, ['activo' => (bool) ($payload['active'] ?? false)]);
                    $result = ['ok' => $ok];
                    break;
                case 'catalog_create':
                    $idCreated = $configuracionController->createCatalog($payload);
                    cms_audit($pdo, 'catalogo_creado', 'catalogo_valores', (string) $idCreated, null, ['categoria' => $payload['category'] ?? '', 'codigo' => $payload['code'] ?? '', 'etiqueta' => $payload['label'] ?? '']);
                    $result = ['ok' => true, 'id' => $idCreated];
                    break;
                case 'permission_set':
                    $configuracionController->updatePermission($payload);
                    cms_audit($pdo, 'permiso_rol_actualizado', 'rol_permisos', (string) ($payload['role_id'] ?? ''), null, $payload);
                    $result = ['ok' => true];
                    break;
                case 'user_create':
                    $newUserId = $configuracionController->createUser($payload);
                    cms_audit($pdo, 'usuario_creado', 'usuarios', (string) $newUserId, null, ['nombre' => $payload['name'] ?? '', 'email' => $payload['email'] ?? '', 'rol_id' => $payload['role_id'] ?? null]);
                    $result = ['ok' => true, 'id' => $newUserId];
                    break;
                case 'user_status':
                    $userId = filter_var($payload['user_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
                    $active = filter_var($payload['active'] ?? null, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
                    if ($userId === false || $active === null) {
                        throw new InvalidArgumentException('El estado del usuario no es válido.');
                    }
                    $ok = $configuracionController->toggleUserStatus($userId, $active);
                    cms_audit($pdo, 'usuario_estado_actualizado', 'usuarios', (string) $userId, null, ['activo' => $active]);
                    $result = ['ok' => $ok];
                    break;
                case 'profile_update':
                    $updatedUser = $configuracionController->updateProfile($payload, (int) cms_current_user()['id']);
                    $_SESSION['user']['name'] = $updatedUser['name'] ?? $_SESSION['user']['name'];
                    $_SESSION['user']['email'] = $updatedUser['email'] ?? $_SESSION['user']['email'];
                    $_SESSION['user']['avatar'] = $updatedUser['avatar'] ?? ($_SESSION['user']['avatar'] ?? '');
                    cms_audit($pdo, 'perfil_actualizado', 'usuarios', (string) (cms_current_user()['id'] ?? ''), null, ['email' => $updatedUser['email'] ?? null]);
                    $result = ['ok' => true, 'user' => $updatedUser];
                    break;
                default:
                    http_response_code(400);
                    $result = ['ok' => false, 'message' => 'Acción de configuración no válida.'];
            }
            echo json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $exception->getMessage()], JSON_THROW_ON_ERROR);
        } catch (PDOException $exception) {
            error_log('[Configuracion] ' . $exception->getMessage());
            http_response_code(409);
            $message = 'No se pudo guardar. Verifica que el valor no exista ya.';
            if (in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true) || (isset($_SERVER['HTTP_HOST']) && strpos((string) $_SERVER['HTTP_HOST'], 'localhost') !== false)) {
                $message = 'No se pudo guardar. Error de base de datos: ' . $exception->getMessage();
            }
            echo json_encode(['ok' => false, 'message' => $message], JSON_THROW_ON_ERROR);
        }
        exit;
    }

    if ($api === 'ventas') {
        require_once BASE_PATH . '/app/Models/VentaModel.php';
        require_once BASE_PATH . '/app/Controllers/VentaController.php';
        $ventaController = new VentaController(new VentaModel($pdo));
        header('Content-Type: application/json; charset=utf-8');
        try {
            switch ($payload['action'] ?? '') {
                case 'create':
                    $saleId = $ventaController->registrar($payload);
                    cms_audit($pdo, 'venta_registrada', 'ventas', (string) $saleId, null, ['cliente_id' => (int) ($payload['cliente_id'] ?? 0), 'unidad_id' => (int) ($payload['unidad_id'] ?? 0)]);
                    $result = ['ok' => true, 'id' => $saleId];
                    break;
                default:
                    http_response_code(400);
                    $result = ['ok' => false, 'message' => 'Acción de ventas no válida.'];
            }
            echo json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $exception->getMessage()], JSON_THROW_ON_ERROR);
        } catch (PDOException $exception) {
            error_log('[Ventas] ' . $exception->getMessage());
            http_response_code(409);
            echo json_encode(['ok' => false, 'message' => 'No se pudo registrar la venta. Verifica la unidad, cliente y cuotas.'], JSON_THROW_ON_ERROR);
        }
        exit;
    }

    if ($api === 'recordatorios') {
        require_once BASE_PATH . '/app/Models/RecordatorioModel.php';
        require_once BASE_PATH . '/app/Controllers/RecordatorioController.php';
        $recordatorioController = new RecordatorioController(new RecordatorioModel($pdo));
        header('Content-Type: application/json; charset=utf-8');
        try {
            switch ($payload['action'] ?? '') {
                case 'create':
                    $recordatorioId = $recordatorioController->crear($payload);
                    cms_audit($pdo, 'recordatorio_creado', 'recordatorios', (string) $recordatorioId, null, ['titulo' => (string) ($payload['titulo'] ?? '')]);
                    $result = ['ok' => true, 'id' => $recordatorioId];
                    break;
                default:
                    http_response_code(400);
                    $result = ['ok' => false, 'message' => 'Acción de recordatorios no válida.'];
            }
            echo json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $exception->getMessage()], JSON_THROW_ON_ERROR);
        } catch (PDOException $exception) {
            error_log('[Recordatorios] ' . $exception->getMessage());
            http_response_code(409);
            echo json_encode(['ok' => false, 'message' => 'No se pudo guardar el recordatorio.'], JSON_THROW_ON_ERROR);
        }
        exit;
    }

    if ($api === 'contactos') {
        require_once BASE_PATH . '/app/Models/ClienteModel.php';
        require_once BASE_PATH . '/app/Controllers/ClienteController.php';
        $clienteController = new ClienteController(new ClienteModel($pdo));
        header('Content-Type: application/json; charset=utf-8');
        try {
            switch ($payload['action'] ?? '') {
                case 'create':
                    $contactoId = $clienteController->guardarContacto($payload);
                    cms_audit($pdo, 'contacto_creado', 'contactos', (string) $contactoId, null, ['cliente_id' => (int) ($payload['cliente_id'] ?? 0), 'tipo' => (string) ($payload['tipo_contacto'] ?? $payload['tipo'] ?? 'Celular')]);
                    $result = ['ok' => true, 'id' => $contactoId, 'contactos' => $clienteController->contactos((int) ($payload['cliente_id'] ?? 0))];
                    break;
                case 'list':
                    $result = ['ok' => true, 'contactos' => $clienteController->contactos((int) ($payload['cliente_id'] ?? 0))];
                    break;
                default:
                    http_response_code(400);
                    $result = ['ok' => false, 'message' => 'Acción de contactos no válida.'];
            }
            echo json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $exception->getMessage()], JSON_THROW_ON_ERROR);
        } catch (PDOException $exception) {
            error_log('[Contactos] ' . $exception->getMessage());
            http_response_code(409);
            echo json_encode(['ok' => false, 'message' => 'No se pudo guardar el contacto.'], JSON_THROW_ON_ERROR);
        }
        exit;
    }

    if ($api === 'documentos') {
        require_once BASE_PATH . '/app/Models/DocumentoModel.php';
        require_once BASE_PATH . '/app/Controllers/DocumentoController.php';
        $documentoController = new DocumentoController(new DocumentoModel($pdo));
        header('Content-Type: application/json; charset=utf-8');
        try {
            switch ($payload['action'] ?? '') {
                case 'create':
                    $documentId = $documentoController->crear($payload);
                    cms_audit($pdo, 'documento_creado', 'documentos', (string) $documentId, null, ['cliente_id' => (int) ($payload['cliente_id'] ?? 0), 'nombre' => (string) ($payload['nombre'] ?? '')]);
                    $result = ['ok' => true, 'id' => $documentId];
                    break;
                case 'list':
                    $result = ['ok' => true, 'documents' => $documentoController->index((int) ($payload['cliente_id'] ?? 0))['documents']];
                    break;
                default:
                    http_response_code(400);
                    $result = ['ok' => false, 'message' => 'Acción de documentos no válida.'];
            }
            echo json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $exception->getMessage()], JSON_THROW_ON_ERROR);
        } catch (PDOException $exception) {
            error_log('[Documentos] ' . $exception->getMessage());
            http_response_code(409);
            echo json_encode(['ok' => false, 'message' => 'No se pudo guardar el documento.'], JSON_THROW_ON_ERROR);
        }
        exit;
    }

    if ($api === 'contratos') {
        require_once BASE_PATH . '/app/Models/ContratoModel.php';
        require_once BASE_PATH . '/app/Controllers/ContratoController.php';
        $contratoController = new ContratoController(new ContratoModel($pdo));
        header('Content-Type: application/json; charset=utf-8');
        try {
            switch ($payload['action'] ?? '') {
                case 'create':
                    $contractId = $contratoController->crear($payload);
                    cms_audit($pdo, 'contrato_creado', 'documentos', (string) $contractId, null, ['cliente_id' => (int) ($payload['cliente_id'] ?? 0), 'nombre' => (string) ($payload['nombre'] ?? '')]);
                    $result = ['ok' => true, 'id' => $contractId];
                    break;
                default:
                    http_response_code(400);
                    $result = ['ok' => false, 'message' => 'Acción de contratos no válida.'];
            }
            echo json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $exception->getMessage()], JSON_THROW_ON_ERROR);
        } catch (PDOException $exception) {
            error_log('[Contratos] ' . $exception->getMessage());
            http_response_code(409);
            echo json_encode(['ok' => false, 'message' => 'No se pudo guardar el contrato.'], JSON_THROW_ON_ERROR);
        }
        exit;
    }

    if ($api === 'caja') {
        require_once BASE_PATH . '/app/Models/CajaModel.php';
        require_once BASE_PATH . '/app/Controllers/CajaController.php';
        $cajaController = new CajaController(new CajaModel($pdo));
        header('Content-Type: application/json; charset=utf-8');
        try {
            switch ($payload['action'] ?? '') {
                case 'mark_paid':
                    $paid = $cajaController->registrarPago($payload);
                    cms_audit($pdo, 'cuota_cobrada', 'cuotas', (string) ($payload['cuota_id'] ?? 0), null, ['cuota_id' => (int) ($payload['cuota_id'] ?? 0)]);
                    $result = ['ok' => $paid];
                    break;
                default:
                    http_response_code(400);
                    $result = ['ok' => false, 'message' => 'Acción de caja no válida.'];
            }
            echo json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $exception->getMessage()], JSON_THROW_ON_ERROR);
        } catch (PDOException $exception) {
            error_log('[Caja] ' . $exception->getMessage());
            http_response_code(409);
            echo json_encode(['ok' => false, 'message' => 'No se pudo registrar el cobro.'], JSON_THROW_ON_ERROR);
        }
        exit;
    }

    if (in_array($api, ['proyectos', 'inventario'], true)) {
        require_once BASE_PATH . '/app/Models/ProyectoModel.php';
        require_once BASE_PATH . '/app/Controllers/ProyectoController.php';
        require_once BASE_PATH . '/app/Models/InventarioModel.php';
        require_once BASE_PATH . '/app/Controllers/InventarioController.php';
        $proyectoModel = new ProyectoModel($pdo);
        $proyectoController = new ProyectoController($proyectoModel);
        $inventarioModel = new InventarioModel($pdo);
        $inventarioController = new InventarioController($inventarioModel);
        header('Content-Type: application/json; charset=utf-8');
        try {
            if ($api === 'proyectos') {
                switch ($payload['action'] ?? '') {
                    case 'project_save':
                        $projectId = isset($payload['id']) ? (int) $payload['id'] : null;
                        $oldProject = $projectId === null ? null : $proyectoModel->getProject($projectId);
                        $savedId = $proyectoController->saveProject($payload);
                        cms_audit($pdo, $oldProject === null ? 'proyecto_creado' : 'proyecto_actualizado', 'proyectos', (string) $savedId, $oldProject, $proyectoModel->getProject($savedId));
                        $result = ['ok' => true, 'id' => $savedId];
                        break;
                    case 'stage_save':
                        $stageId = isset($payload['id']) ? (int) $payload['id'] : null;
                        $oldStage = null;
                        if ($stageId !== null) {
                            $stageQuery = $pdo->prepare('SELECT id, id_proyecto AS projectId, nombre AS name, orden AS position, activo AS active FROM etapas_proyecto WHERE id = :id');
                            $stageQuery->execute(['id' => $stageId]);
                            $oldStage = $stageQuery->fetch() ?: null;
                        }
                        $savedId = $proyectoController->saveStage($payload);
                        cms_audit($pdo, $oldStage === null ? 'etapa_creada' : 'etapa_actualizada', 'etapas_proyecto', (string) $savedId, $oldStage, ['projectId' => (int) ($payload['project_id'] ?? 0), 'name' => $payload['name'] ?? '', 'position' => (int) ($payload['position'] ?? 1)]);
                        $result = ['ok' => true, 'id' => $savedId];
                        break;
                    case 'stage_status':
                        $proyectoController->setStageActive($payload['id'] ?? null, $payload['active'] ?? null);
                        cms_audit($pdo, 'etapa_estado_actualizado', 'etapas_proyecto', (string) ($payload['id'] ?? ''), null, ['active' => filter_var($payload['active'] ?? false, FILTER_VALIDATE_BOOLEAN)]);
                        $result = ['ok' => true];
                        break;
                    default:
                        http_response_code(400);
                        $result = ['ok' => false, 'message' => 'Acción de proyecto no válida.'];
                }
            } else {
                switch ($payload['action'] ?? '') {
                    case 'unit_save':
                        $unitId = isset($payload['id']) ? (int) $payload['id'] : null;
                        $oldUnit = $unitId === null ? null : $inventarioModel->getUnit($unitId);
                        $savedId = $inventarioController->saveUnit($payload);
                        cms_audit($pdo, $oldUnit === null ? 'unidad_creada' : 'unidad_actualizada', 'unidades', (string) $savedId, $oldUnit, $inventarioModel->getUnit($savedId));
                        $result = ['ok' => true, 'id' => $savedId];
                        break;
                    case 'unit_archive':
                        $oldUnit = $inventarioModel->getUnit((int) ($payload['id'] ?? 0));
                        $inventarioController->archiveUnit($payload['id'] ?? null);
                        cms_audit($pdo, 'unidad_archivada', 'unidades', (string) ($payload['id'] ?? ''), $oldUnit, ['activo' => false]);
                        $result = ['ok' => true];
                        break;
                    default:
                        http_response_code(400);
                        $result = ['ok' => false, 'message' => 'Acción de inventario no válida.'];
                }
            }
            echo json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $exception->getMessage()], JSON_THROW_ON_ERROR);
        } catch (PDOException $exception) {
            error_log('[Fase2] ' . $exception->getMessage());
            http_response_code(409);
            echo json_encode(['ok' => false, 'message' => 'No se pudo guardar. Verifica códigos duplicados y relaciones existentes.'], JSON_THROW_ON_ERROR);
        }
        exit;
    }
}

if ($api !== null && $_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($api === 'lead_etapa') {
        $leadId = filter_var($payload['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $stage = isset($payload['stage']) ? trim((string) $payload['stage']) : '';

        if ($leadId === false || $stage === '') {
            http_response_code(400);
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => false], JSON_THROW_ON_ERROR);
            exit;
        }

        $oldStageQuery = $pdo->prepare('SELECT etapa FROM leads WHERE id = :id');
        $oldStageQuery->execute(['id' => $leadId]);
        $oldStage = $oldStageQuery->fetchColumn();
        require_once BASE_PATH . '/app/Models/LeadModel.php';
        $leadModel = new LeadModel($pdo);
        $updated = $leadModel->updateStage($leadId, $stage, $advisorScopeUserId);
        if ($updated) {
            cms_audit($pdo, 'lead_etapa_actualizada', 'leads', (string) $leadId, ['etapa' => $oldStage], ['etapa' => $stage]);
        }
        echo json_encode(['ok' => $updated], JSON_THROW_ON_ERROR);
        exit;
    }

    if ($api === 'separacion') {
        require_once BASE_PATH . '/app/Models/SeparacionModel.php';
        require_once BASE_PATH . '/app/Controllers/SeparacionController.php';
        $controller = new SeparacionController(new SeparacionModel($pdo));
        header('Content-Type: application/json; charset=utf-8');
        try {
            $action = $payload['action'] ?? 'create';
            if ($action !== 'create') {
                throw new InvalidArgumentException('Acción de separación no válida.');
            }
            $ok = $controller->registrar([
                'id_cliente' => (int) ($payload['cliente_id'] ?? 0),
                'id_unidad' => (int) ($payload['unidad_id'] ?? 0),
                'monto' => (float) ($payload['monto'] ?? 0),
                'metodo_pago' => (string) ($payload['metodo_pago'] ?? 'Contado'),
                'fecha_vencimiento' => (string) ($payload['fecha_vencimiento'] ?? date('Y-m-d')),
                'observaciones' => (string) ($payload['observaciones'] ?? ''),
            ]);
            cms_audit($pdo, 'separacion_registrada', 'separaciones', null, null, ['cliente_id' => (int) ($payload['cliente_id'] ?? 0), 'unidad_id' => (int) ($payload['unidad_id'] ?? 0)]);
            echo json_encode(['ok' => $ok], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        } catch (InvalidArgumentException $exception) {
            http_response_code(422);
            echo json_encode(['ok' => false, 'message' => $exception->getMessage()], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        } catch (PDOException $exception) {
            error_log('[Separacion] ' . $exception->getMessage());
            http_response_code(409);
            echo json_encode(['ok' => false, 'message' => 'No se pudo registrar la separación.'], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
        }
        exit;
    }
}

switch ($view) {
    case 'dashboard':
        require_once BASE_PATH . '/app/Models/DashboardModel.php';
        require_once BASE_PATH . '/app/Controllers/DashboardController.php';
        $dashboardController = new DashboardController(new DashboardModel($pdo));
        $dashboardData = $dashboardController->index();
        $salesData = $dashboardData['salesData'];
        $recentSales = $dashboardData['recentSales'];
        $dashboardAlerts = $dashboardData['alerts'];
        break;

    case 'crm':
        require_once BASE_PATH . '/app/Models/LeadModel.php';
        require_once BASE_PATH . '/app/Controllers/CrmController.php';
        require_once BASE_PATH . '/app/Controllers/LeadController.php';

        $leadController = new LeadController(new LeadModel($pdo));
        $crmController = new CrmController(new LeadModel($pdo));

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $contentType = strtolower($_SERVER['CONTENT_TYPE'] ?? '');
            $isJsonRequest = str_contains($contentType, 'application/json');
            $payload = $_POST;

            $respondJson = static function (int $statusCode, array $response): never {
                http_response_code($statusCode);
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($response, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR);
                exit;
            };

            if (!cms_has_permission($pdo, 'crm', 'editar')) {
                if ($isJsonRequest) {
                    $respondJson(403, ['message' => 'No tienes permiso para modificar leads.']);
                }
                $_SESSION['lead_feedback'] = 'No tienes permiso para modificar leads.';
                header('Location: ?view=crm', true, 303);
                exit;
            }

            if ($isJsonRequest) {
                try {
                    $payload = json_decode(file_get_contents('php://input'), true, 512, JSON_THROW_ON_ERROR);
                } catch (JsonException) {
                    $respondJson(400, ['message' => 'La solicitud no contiene JSON válido.']);
                }
            }

            $submittedToken = $isJsonRequest
                ? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '')
                : ($payload['_csrf'] ?? '');

            if (!is_string($submittedToken) || !hash_equals($csrfToken, $submittedToken)) {
                if ($isJsonRequest) {
                    $respondJson(403, ['message' => 'La sesión expiró. Recarga la página.']);
                }
                $_SESSION['lead_feedback'] = 'La sesión expiró. Recarga la página e inténtalo de nuevo.';
                header('Location: ?view=crm', true, 303);
                exit;
            }

            $action = $payload['action'] ?? '';
            if ($isJsonRequest && $action === 'update_status') {
                try {
                    $oldStatusQuery = $pdo->prepare('SELECT etapa FROM leads WHERE id = :id');
                    $oldStatusQuery->execute(['id' => (int) ($payload['id'] ?? 0)]);
                    $oldStatus = $oldStatusQuery->fetchColumn();
                    $updated = $leadController->changeStatus($payload['id'] ?? null, $payload['status'] ?? null, $advisorScopeUserId);
                    if (!$updated) {
                        $respondJson(404, ['message' => 'No se encontró ese lead.']);
                    }
                    cms_audit($pdo, 'lead_estado_actualizado', 'leads', (string) $payload['id'], ['estado' => $oldStatus], ['estado' => $payload['status']]);
                    $respondJson(200, ['success' => true]);
                } catch (InvalidArgumentException $exception) {
                    $respondJson(422, ['message' => $exception->getMessage()]);
                } catch (PDOException $exception) {
                    error_log('[LeadController] ' . $exception->getMessage());
                    $respondJson(500, ['message' => 'No se pudo guardar el estado.']);
                }
            }

            if (!$isJsonRequest && $action === 'create') {
                try {
                    $newLeadId = $leadController->create($payload, $advisorScopeUserId);
                    cms_audit($pdo, 'lead_creado', 'leads', (string) $newLeadId, null, ['nombre' => $payload['name'] ?? '']);
                    $_SESSION['lead_feedback'] = 'Lead creado correctamente.';
                } catch (InvalidArgumentException $exception) {
                    $_SESSION['lead_feedback'] = $exception->getMessage();
                } catch (PDOException $exception) {
                    error_log('[LeadController] ' . $exception->getMessage());
                    $_SESSION['lead_feedback'] = 'No se pudo guardar el lead. Verifica campaña y asesor.';
                }
                header('Location: ?view=crm', true, 303);
                exit;
            }

            if ($isJsonRequest) {
                $respondJson(400, ['message' => 'La acción solicitada no es válida.']);
            }
            $_SESSION['lead_feedback'] = 'La acción solicitada no es válida.';
            header('Location: ?view=crm', true, 303);
            exit;
        }

        try {
            $crmData = $crmController->index($advisorScopeUserId);
            $leads = $crmData['leads'];
            $campaigns = $crmData['campaigns'];
            $advisors = $crmData['advisors'];
        } catch (PDOException $exception) {
            error_log('[CrmController] ' . $exception->getMessage());
            $leadError = 'No fue posible cargar los datos del CRM.';
        }
        break;

    case 'inventario':
        require_once BASE_PATH . '/app/Models/InventarioModel.php';
        require_once BASE_PATH . '/app/Controllers/InventarioController.php';
        require_once BASE_PATH . '/app/Models/ProyectoModel.php';
        require_once BASE_PATH . '/app/Controllers/ProyectoController.php';
        $inventarioController = new InventarioController(new InventarioModel($pdo));
        $inventarioData = $inventarioController->index();
        $units = $inventarioData['units'];
        $totalResults = $inventarioData['totalResults'];
        $unitStatuses = $inventarioData['statuses'];
        $projectData = (new ProyectoController(new ProyectoModel($pdo)))->index();
        $inventoryProjects = array_map(static fn (array $project): array => ['id' => (int) $project['id'], 'name' => (string) $project['name'], 'status' => (string) $project['status']], $projectData['projects']);
        $inventoryStages = array_values(array_filter($projectData['stages'], static fn (array $stage): bool => (int) $stage['active'] === 1));
        $inventoryProjects = array_values(array_filter($inventoryProjects, static fn (array $project): bool => $project['status'] !== 'Archivado'));
        $unitStatuses = array_values($unitStatuses);
        break;

    case 'proyectos':
        require_once BASE_PATH . '/app/Models/ProyectoModel.php';
        require_once BASE_PATH . '/app/Controllers/ProyectoController.php';
        $projectData = (new ProyectoController(new ProyectoModel($pdo)))->index();
        break;

    case 'clientes':
        require_once BASE_PATH . '/app/Models/ClienteModel.php';
        require_once BASE_PATH . '/app/Controllers/ClienteController.php';
        $clienteController = new ClienteController(new ClienteModel($pdo));
        $clientesData = $clienteController->index($advisorScopeUserId);
        $clients = $clientesData['clients'];
        break;

    case 'ventas':
        require_once BASE_PATH . '/app/Models/VentaModel.php';
        require_once BASE_PATH . '/app/Controllers/VentaController.php';
        $ventaController = new VentaController(new VentaModel($pdo));
        $ventasData = $ventaController->index();
        $ventas = $ventasData['sales'];
        $saleClients = $ventasData['clients'];
        $saleUnits = $ventasData['units'];
        break;

    case 'contratos':
        require_once BASE_PATH . '/app/Models/ContratoModel.php';
        require_once BASE_PATH . '/app/Controllers/ContratoController.php';
        $contratoController = new ContratoController(new ContratoModel($pdo));
        $contratosData = $contratoController->index();
        $contratos = $contratosData['contracts'];
        $contratosClientes = $pdo->query('SELECT id, nombre FROM clientes ORDER BY nombre')->fetchAll(PDO::FETCH_ASSOC);
        break;

    case 'caja':
        require_once BASE_PATH . '/app/Models/CajaModel.php';
        require_once BASE_PATH . '/app/Controllers/CajaController.php';
        $cajaController = new CajaController(new CajaModel($pdo));
        $cajaData = $cajaController->index();
        $cajaCuotas = $cajaData['cuotas'];
        $cajaSummary = $cajaData['summary'];
        break;

    case 'cobranzas':
        require_once BASE_PATH . '/app/Models/CobranzaModel.php';
        require_once BASE_PATH . '/app/Controllers/CobranzaController.php';
        $cobranzaController = new CobranzaController(new CobranzaModel($pdo));
        $cobranzaData = $cobranzaController->index();
        $cobranzasResumen = $cobranzaData['resumen'];
        $cobranzas = $cobranzaData['cobranzas'];
        break;

    case 'comisiones':
        require_once BASE_PATH . '/app/Models/ComisionModel.php';
        require_once BASE_PATH . '/app/Controllers/ComisionController.php';
        $comisionController = new ComisionController(new ComisionModel($pdo));
        $comisionesData = $comisionController->index();
        $comisionesResumen = $comisionesData['resumen'];
        $comisiones = $comisionesData['comisiones'];
        break;

    case 'campanas':
        require_once BASE_PATH . '/app/Models/CampanaModel.php';
        require_once BASE_PATH . '/app/Controllers/CampanaController.php';
        $campanaController = new CampanaController(new CampanaModel($pdo));
        $campanasData = $campanaController->index();
        $campanasResumen = $campanasData['resumen'];
        $campanas = $campanasData['campanas'];
        break;

    case 'alertas':
        require_once BASE_PATH . '/app/Models/AlertaModel.php';
        require_once BASE_PATH . '/app/Controllers/AlertaController.php';
        $alertaController = new AlertaController(new AlertaModel($pdo));
        $alertasData = $alertaController->index();
        $alertasResumen = $alertasData['resumen'];
        $alertas = $alertasData['alertas'];
        break;

    case 'cartera':
        require_once BASE_PATH . '/app/Models/CarteraModel.php';
        require_once BASE_PATH . '/app/Controllers/CarteraController.php';
        $carteraController = new CarteraController(new CarteraModel($pdo));
        $carteraData = $carteraController->index();
        $carteraResumen = $carteraData['resumen'];
        $cartera = $carteraData['cartera'];
        break;

    case 'rendimiento':
        require_once BASE_PATH . '/app/Models/RendimientoModel.php';
        require_once BASE_PATH . '/app/Controllers/RendimientoController.php';
        $rendimientoController = new RendimientoController(new RendimientoModel($pdo));
        $rendimientoData = $rendimientoController->index();
        $rendimientoResumen = $rendimientoData['resumen'];
        $rendimiento = $rendimientoData['rendimiento'];
        break;

    case 'expedientes':
        require_once BASE_PATH . '/app/Models/ExpedienteModel.php';
        require_once BASE_PATH . '/app/Controllers/ExpedienteController.php';
        $expedienteController = new ExpedienteController(new ExpedienteModel($pdo));
        $expedienteData = $expedienteController->index();
        $expedientesResumen = $expedienteData['resumen'];
        $expedientes = $expedienteData['expedientes'];
        break;

    case 'recordatorios':
        require_once BASE_PATH . '/app/Models/RecordatorioModel.php';
        require_once BASE_PATH . '/app/Controllers/RecordatorioController.php';
        $recordatorioController = new RecordatorioController(new RecordatorioModel($pdo));
        $recordatoriosData = $recordatorioController->index();
        $recordatorios = $recordatoriosData['recordatorios'];
        break;
    case 'notificaciones':
        require_once BASE_PATH . '/app/Models/NotificacionModel.php';
        require_once BASE_PATH . '/app/Controllers/NotificacionController.php';
        $notificacionController = new NotificacionController(new NotificacionModel($pdo));
        $notificacionesData = $notificacionController->index();
        $notificacionesResumen = $notificacionesData['resumen'];
        $notificaciones = $notificacionesData['notificaciones'];
        break;
    case 'seguimiento':
        require_once BASE_PATH . '/app/Models/SeguimientoModel.php';
        require_once BASE_PATH . '/app/Controllers/SeguimientoController.php';
        $seguimientoController = new SeguimientoController(new SeguimientoModel($pdo));
        $seguimientoData = $seguimientoController->index();
        $seguimientoResumen = $seguimientoData['resumen'];
        $seguimiento = $seguimientoData['seguimiento'];
        break;
    case 'tareas':
        require_once BASE_PATH . '/app/Models/TareaModel.php';
        require_once BASE_PATH . '/app/Controllers/TareaController.php';
        $tareaController = new TareaController(new TareaModel($pdo));
        $tareasData = $tareaController->index();
        $tareasResumen = $tareasData['resumen'];
        $tareas = $tareasData['tareas'];
        break;
    case 'agenda':
        require_once BASE_PATH . '/app/Models/AgendaModel.php';
        require_once BASE_PATH . '/app/Controllers/AgendaController.php';
        $agendaController = new AgendaController(new AgendaModel($pdo));
        $agendaData = $agendaController->index();
        $agendaResumen = $agendaData['resumen'];
        $agenda = $agendaData['agenda'];
        break;
    case 'pipeline':
        require_once BASE_PATH . '/app/Models/PipelineModel.php';
        require_once BASE_PATH . '/app/Controllers/PipelineController.php';
        $pipelineController = new PipelineController(new PipelineModel($pdo));
        $pipelineData = $pipelineController->index();
        $pipelineResumen = $pipelineData['resumen'];
        $pipeline = $pipelineData['pipeline'];
        break;
    case 'prospeccion':
        require_once BASE_PATH . '/app/Models/ProspeccionModel.php';
        require_once BASE_PATH . '/app/Controllers/ProspeccionController.php';
        $prospeccionController = new ProspeccionController(new ProspeccionModel($pdo));
        $prospeccionData = $prospeccionController->index();
        $prospeccionResumen = $prospeccionData['resumen'];
        $prospeccion = $prospeccionData['prospeccion'];
        break;
    case 'soporte':
        require_once BASE_PATH . '/app/Models/SoporteModel.php';
        require_once BASE_PATH . '/app/Controllers/SoporteController.php';
        $soporteController = new SoporteController(new SoporteModel($pdo));
        $soporteData = $soporteController->index();
        $soporteResumen = $soporteData['resumen'];
        $soporteEventos = $soporteData['eventos'];
        break;

    case 'incidencias':
        require_once BASE_PATH . '/app/Models/IncidenciaModel.php';
        require_once BASE_PATH . '/app/Controllers/IncidenciaController.php';
        $incidenciaController = new IncidenciaController(new IncidenciaModel($pdo));
        $incidenciaData = $incidenciaController->index();
        $incidenciasResumen = $incidenciaData['resumen'];
        $incidencias = $incidenciaData['incidencias'];
        break;

    case 'reportes':
        require_once BASE_PATH . '/app/Models/ReportesModel.php';
        require_once BASE_PATH . '/app/Controllers/ReportesController.php';
        $reportesController = new ReportesController(new ReportesModel($pdo));
        $reportesData = $reportesController->index();
        $reportesResumen = $reportesData['resumen'];
        $reportesVentasMes = $reportesData['ventas_mes'];
        $reportesInventario = $reportesData['inventario'];
        $reportesTopProyectos = $reportesData['top_proyectos'];
        break;

    case 'detalle':
        require_once BASE_PATH . '/app/Models/ClienteModel.php';
        require_once BASE_PATH . '/app/Controllers/ClienteController.php';
        $clienteController = new ClienteController(new ClienteModel($pdo));
        $detalleData = $clienteController->detalle($id, $advisorScopeUserId);
        $clients = $detalleData['clients'];
        $clientDocs = $detalleData['clientDocs'];
        $clientContacts = $detalleData['clientContacts'];
        break;

    case 'configuracion':
        $canViewAudit = cms_has_permission($pdo, 'auditoria', 'ver');
        $configData = $configuracionController->index($canViewAudit);
        break;

    case 'auditoria':
        $auditLogs = $configuracionController->auditLogs();
        break;

    default:
        break;
}

require_once BASE_PATH . '/app/Models/SeparacionModel.php';
require_once BASE_PATH . '/app/Controllers/SeparacionController.php';
$separacionController = new SeparacionController(new SeparacionModel($pdo));
$sepUnits = $separacionController->index()['sepUnits'];

require BASE_PATH . '/app/Views/layout/header.php';
require $viewFile;
?>

<script>
    const salesData = <?= json_encode($salesData ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const recentSales = <?= json_encode($recentSales ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const dashboardAlerts = <?= json_encode($dashboardAlerts ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const units = <?= json_encode($units ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const totalResults = <?= (int) ($totalResults ?? 0) ?>;
    const leads = <?= json_encode($leads ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const leadError = <?= json_encode($leadError ?? null, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const leadCsrfToken = <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const projectRecords = <?= json_encode($projectData['projects'] ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const projectStageRecords = <?= json_encode($projectData['stages'] ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const projectStatusOptions = <?= json_encode($projectData['statuses'] ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const inventoryProjectOptions = <?= json_encode($inventoryProjects, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const inventoryStageOptions = <?= json_encode($inventoryStages, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const inventoryStatusOptions = <?= json_encode($unitStatuses, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const inventoryCsrfToken = <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const clients = <?= json_encode($clients ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const clientContacts = <?= json_encode($clientContacts ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const saleUnits = <?= json_encode($saleUnits ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const saleClients = <?= json_encode($saleClients ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const saleCsrfToken = <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const cajaCuotas = <?= json_encode($cajaCuotas ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const cajaSummary = <?= json_encode($cajaSummary ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const cajaCsrfToken = <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const comisionesResumen = <?= json_encode($comisionesResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const comisiones = <?= json_encode($comisiones ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const campanasResumen = <?= json_encode($campanasResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const campanas = <?= json_encode($campanas ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const alertasResumen = <?= json_encode($alertasResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const alertas = <?= json_encode($alertas ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const carteraResumen = <?= json_encode($carteraResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const cartera = <?= json_encode($cartera ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const rendimientoResumen = <?= json_encode($rendimientoResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const rendimiento = <?= json_encode($rendimiento ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const expedientesResumen = <?= json_encode($expedientesResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const expedientes = <?= json_encode($expedientes ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const contratos = <?= json_encode($contratos ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const contratosClientes = <?= json_encode($contratosClientes ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const contratosCsrfToken = <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const sepUnits = <?= json_encode($sepUnits ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const recordatorios = <?= json_encode($recordatorios ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const recordatoriosCsrfToken = <?= json_encode($csrfToken, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const reportesResumen = <?= json_encode($reportesResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const reportesVentasMes = <?= json_encode($reportesVentasMes ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const reportesInventario = <?= json_encode($reportesInventario ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const reportesTopProyectos = <?= json_encode($reportesTopProyectos ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const soporteResumen = <?= json_encode($soporteResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const soporteEventos = <?= json_encode($soporteEventos ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const incidenciasResumen = <?= json_encode($incidenciasResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const incidencias = <?= json_encode($incidencias ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const seguimientoResumen = <?= json_encode($seguimientoResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const seguimiento = <?= json_encode($seguimiento ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const tareasResumen = <?= json_encode($tareasResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const tareas = <?= json_encode($tareas ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const agendaResumen = <?= json_encode($agendaResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const agenda = <?= json_encode($agenda ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const pipelineResumen = <?= json_encode($pipelineResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const pipeline = <?= json_encode($pipeline ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const prospeccionResumen = <?= json_encode($prospeccionResumen ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    const prospeccion = <?= json_encode($prospeccion ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
    window.clientDocs = <?= json_encode($clientDocs ?? [], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR) ?>;
</script>

<?php
require BASE_PATH . '/app/Views/layout/footer.php';
