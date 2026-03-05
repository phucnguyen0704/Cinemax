<?php

/**
 * File test API cho HallController, HallStatusController, SeatTypeController
 * 
 * Cách sử dụng:
 * - GET: test_api.php?controller=halls&action=getAll
 * - GET: test_api.php?controller=halls&action=getById&id=1
 * - POST: test_api.php?controller=halls&action=create (với POST data)
 */

// Tắt hiển thị lỗi để tránh output HTML trước JSON
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Bắt đầu output buffering để bắt mọi output không mong muốn
ob_start();

// Bắt đầu session và set header ngay
session_start();
header('Content-Type: application/json; charset=utf-8');

// Xử lý lỗi
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (error_reporting() === 0) {
        return false;
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
}, E_ALL);
try {
    require_once __DIR__ . '/../../../config/dbConfig.php';
    require_once __DIR__ . '/../../../models/Hall.php';
    require_once __DIR__ . '/../../../models/HallStatus.php';
    require_once __DIR__ . '/../../../models/SeatType.php';
    require_once __DIR__ . '/../../../models/Cinema.php';
    require_once __DIR__ . '/../../../models/Seat.php';
    require_once __DIR__ . '/../../../services/HallService.php';
    require_once __DIR__ . '/../../../services/HallStatusService.php';
    require_once __DIR__ . '/../../../services/SeatTypeService.php';
    require_once __DIR__ . '/../../../services/CinemaService.php';
    require_once __DIR__ . '/../../../services/SeatService.php';
    require_once __DIR__ . '/../../../controllers/HallController.php';
    require_once __DIR__ . '/../../../controllers/HallStatusController.php';
    require_once __DIR__ . '/../../../controllers/SeatTypeController.php';
    require_once __DIR__ . '/../../../controllers/CinemaController.php';
    require_once __DIR__ . '/../../../controllers/SeatController.php';
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'data' => null,
        'message' => 'Lỗi khi load files: ' . $e->getMessage(),
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
} catch (Error $e) {
    ob_end_clean();
    echo json_encode([
        'success' => false,
        'data' => null,
        'message' => 'Lỗi khi load files: ' . $e->getMessage(),
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Xóa output buffer nếu có output không mong muốn
ob_end_clean();

// Khởi tạo kết nối và controllers
try {
    $conn = getDBConnection();

    $hallModel = new Hall($conn);
    $hallStatusModel = new HallStatus($conn);
    $seatTypeModel = new SeatType($conn);
    $cinemaModel = new Cinema($conn);
    $seatModel = new Seat($conn);

    $hallService = new HallService($hallModel);
    $hallStatusService = new HallStatusService($hallStatusModel);
    $seatTypeService = new SeatTypeService($seatTypeModel);
    $cinemaService = new CinemaService($cinemaModel);
    $seatService = new SeatService($seatModel);

    $hallController = new HallController($hallService, $hallStatusService);
    $hallStatusController = new HallStatusController($hallStatusService);
    $seatTypeController = new SeatTypeController($seatTypeService);
    $cinemaController = new CinemaController($cinemaService);
    $seatController = new SeatController($seatService);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'data' => null,
        'message' => 'Lỗi khi khởi tạo: ' . $e->getMessage(),
        'error' => $e->getMessage()
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Lấy tham số
$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? '';
$method = $_SERVER['REQUEST_METHOD'];

$response = [
    'success' => false,
    'data' => null,
    'message' => '',
    'error' => null
];

try {
    // Xử lý theo controller
    switch ($controller) {
        case 'halls':
            handleHallRequests($hallController, $action, $method, $response, $hallService);
            break;

        case 'hall_status':
        case 'hallstatus':
            handleHallStatusRequests($hallStatusController, $action, $method, $response, $hallStatusService);
            break;

        case 'seat_types':
        case 'seattypes':
            handleSeatTypeRequests($seatTypeController, $action, $method, $response, $seatTypeService);
            break;

        case 'cinemas':
        case 'theaters':
            handleCinemaRequests($cinemaController, $action, $method, $response, $cinemaService);
            break;

        case 'seats':
            handleSeatRequests($seatController, $action, $method, $response, $seatService);
            break;

        default:
            // Hiển thị danh sách API có sẵn
            showApiList();
            break;
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['error'] = $e->getMessage();
    $response['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
} catch (Error $e) {
    $response['success'] = false;
    $response['error'] = $e->getMessage();
    $response['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

function handleHallRequests($controller, $action, $method, &$response, $hallService = null)
{
    try {
        switch ($action) {
            case 'getAll':
                if ($method === 'GET') {
                    $cinemaId = $_GET['cinema_id'] ?? null;
                    if ($cinemaId) {
                        $_GET['cinema_id'] = $cinemaId;
                    }
                    $halls = $controller->getAllHalls();
                    $response['success'] = true;
                    $response['data'] = $halls;
                    $response['message'] = 'Lấy danh sách phòng chiếu thành công';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'getById':
                if ($method === 'GET') {
                    $id = $_GET['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }
                    $hall = $controller->getHallById($id);
                    $response['success'] = true;
                    $response['data'] = $hall;
                    $response['message'] = $hall ? 'Lấy thông tin phòng chiếu thành công' : 'Không tìm thấy phòng chiếu';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'getStatuses':
                if ($method === 'GET') {
                    $statuses = $controller->getAllStatuses();
                    $response['success'] = true;
                    $response['data'] = $statuses;
                    $response['message'] = 'Lấy danh sách trạng thái phòng thành công';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'getCinemas':
                if ($method === 'GET') {
                    $cinemas = $controller->getAllCinemas();
                    $response['success'] = true;
                    $response['data'] = $cinemas;
                    $response['message'] = 'Lấy danh sách rạp thành công';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'create':
                if ($method === 'POST') {
                    $cinemaId = $_POST['cinema_id'] ?? null;
                    $name = $_POST['name'] ?? '';
                    $statusId = $_POST['status_id'] ?? null;

                    if (!$cinemaId || !$name || !$statusId) {
                        throw new Exception('Vui lòng điền đầy đủ thông tin (cinema_id, name, status_id)');
                    }

                    // Gọi service trực tiếp để tránh redirect
                    if (!$hallService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $hallService->createHall($cinemaId, $name, $statusId);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Tạo phòng chiếu thành công';
                        $response['data'] = [
                            'cinema_id' => $cinemaId,
                            'name' => $name,
                            'status_id' => $statusId
                        ];
                    } else {
                        throw new Exception('Không thể tạo phòng chiếu');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'update':
                if ($method === 'POST') {
                    $id = $_GET['id'] ?? $_POST['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }

                    $cinemaId = $_POST['cinema_id'] ?? null;
                    $name = $_POST['name'] ?? '';
                    $statusId = $_POST['status_id'] ?? null;

                    if (!$cinemaId || !$name || !$statusId) {
                        throw new Exception('Vui lòng điền đầy đủ thông tin (cinema_id, name, status_id)');
                    }

                    // Gọi service trực tiếp để tránh redirect
                    if (!$hallService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $hallService->updateHall($id, $cinemaId, $name, $statusId);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Cập nhật phòng chiếu thành công';
                        $response['data'] = [
                            'id' => $id,
                            'cinema_id' => $cinemaId,
                            'name' => $name,
                            'status_id' => $statusId
                        ];
                    } else {
                        throw new Exception('Không thể cập nhật phòng chiếu');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'delete':
                if ($method === 'POST' || $method === 'DELETE') {
                    $id = $_GET['id'] ?? $_POST['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }

                    // Gọi service trực tiếp để tránh redirect
                    if (!$hallService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $hallService->deleteHall($id);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Xóa phòng chiếu thành công';
                        $response['data'] = ['id' => $id];
                    } else {
                        throw new Exception('Không thể xóa phòng chiếu');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST hoặc DELETE');
                }
                break;

            default:
                throw new Exception('Action không hợp lệ. Các action hợp lệ: getAll, getById, getStatuses, getCinemas, create, update, delete');
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['error'] = $e->getMessage();
        $response['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
    }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function handleHallStatusRequests($controller, $action, $method, &$response, $hallStatusService = null)
{
    try {
        switch ($action) {
            case 'getAll':
                if ($method === 'GET') {
                    $statuses = $controller->getAllStatuses();
                    $response['success'] = true;
                    $response['data'] = $statuses;
                    $response['message'] = 'Lấy danh sách trạng thái phòng thành công';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'getById':
                if ($method === 'GET') {
                    $id = $_GET['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }
                    $status = $controller->getStatusById($id);
                    $response['success'] = true;
                    $response['data'] = $status;
                    $response['message'] = $status ? 'Lấy thông tin trạng thái thành công' : 'Không tìm thấy trạng thái';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'create':
                if ($method === 'POST') {
                    $statusName = $_POST['status_name'] ?? '';

                    if (empty($statusName)) {
                        throw new Exception('Vui lòng nhập tên trạng thái');
                    }

                    // Gọi service trực tiếp để tránh redirect
                    if (!$hallStatusService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $hallStatusService->createStatus($statusName);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Tạo trạng thái phòng thành công';
                        $response['data'] = [
                            'status_name' => $statusName
                        ];
                    } else {
                        throw new Exception('Không thể tạo trạng thái phòng');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'update':
                if ($method === 'POST') {
                    $id = $_GET['id'] ?? $_POST['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }

                    $statusName = $_POST['status_name'] ?? '';

                    if (empty($statusName)) {
                        throw new Exception('Vui lòng nhập tên trạng thái');
                    }

                    // Gọi service trực tiếp để tránh redirect
                    if (!$hallStatusService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $hallStatusService->updateStatus($id, $statusName);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Cập nhật trạng thái phòng thành công';
                        $response['data'] = [
                            'id' => $id,
                            'status_name' => $statusName
                        ];
                    } else {
                        throw new Exception('Không thể cập nhật trạng thái phòng');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'delete':
                if ($method === 'POST' || $method === 'DELETE') {
                    $id = $_GET['id'] ?? $_POST['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }

                    // Gọi service trực tiếp để tránh redirect
                    if (!$hallStatusService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $hallStatusService->deleteStatus($id);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Xóa trạng thái phòng thành công';
                        $response['data'] = ['id' => $id];
                    } else {
                        throw new Exception('Không thể xóa trạng thái phòng');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST hoặc DELETE');
                }
                break;

            default:
                throw new Exception('Action không hợp lệ. Các action hợp lệ: getAll, getById, create, update, delete');
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['error'] = $e->getMessage();
        $response['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
    }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function handleSeatTypeRequests($controller, $action, $method, &$response, $seatTypeService = null)
{
    try {
        switch ($action) {
            case 'getAll':
                if ($method === 'GET') {
                    $seatTypes = $controller->getAllSeatTypes();
                    $response['success'] = true;
                    $response['data'] = $seatTypes;
                    $response['message'] = 'Lấy danh sách loại ghế thành công';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'getById':
                if ($method === 'GET') {
                    $id = $_GET['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }
                    $seatType = $controller->getSeatTypeById($id);
                    $response['success'] = true;
                    $response['data'] = $seatType;
                    $response['message'] = $seatType ? 'Lấy thông tin loại ghế thành công' : 'Không tìm thấy loại ghế';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'create':
                if ($method === 'POST') {
                    $typeName = $_POST['type_name'] ?? '';
                    $priceSurcharge = $_POST['price_surcharge'] ?? 0;

                    if (empty($typeName)) {
                        throw new Exception('Vui lòng nhập tên loại ghế');
                    }

                    // Tính hệ số giá từ phụ thu (giống như trong controller)
                    $basePrice = 100000; // Giá gốc mặc định
                    $priceMultiplier = ($basePrice + $priceSurcharge) / $basePrice;

                    // Gọi service trực tiếp để tránh redirect
                    if (!$seatTypeService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $seatTypeService->createSeatType($typeName, $priceMultiplier);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Tạo loại ghế thành công';
                        $response['data'] = [
                            'type_name' => $typeName,
                            'price_surcharge' => $priceSurcharge
                        ];
                    } else {
                        throw new Exception('Không thể tạo loại ghế');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'update':
                if ($method === 'POST') {
                    $id = $_GET['id'] ?? $_POST['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }

                    $typeName = $_POST['type_name'] ?? '';
                    $priceSurcharge = $_POST['price_surcharge'] ?? 0;

                    if (empty($typeName)) {
                        throw new Exception('Vui lòng nhập tên loại ghế');
                    }

                    // Tính hệ số giá từ phụ thu (giống như trong controller)
                    $basePrice = 100000; // Giá gốc mặc định
                    $priceMultiplier = ($basePrice + $priceSurcharge) / $basePrice;

                    // Gọi service trực tiếp để tránh redirect
                    if (!$seatTypeService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $seatTypeService->updateSeatType($id, $typeName, $priceMultiplier);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Cập nhật loại ghế thành công';
                        $response['data'] = [
                            'id' => $id,
                            'type_name' => $typeName,
                            'price_surcharge' => $priceSurcharge
                        ];
                    } else {
                        throw new Exception('Không thể cập nhật loại ghế');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'delete':
                if ($method === 'POST' || $method === 'DELETE') {
                    $id = $_GET['id'] ?? $_POST['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }

                    // Gọi service trực tiếp để tránh redirect
                    if (!$seatTypeService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $seatTypeService->deleteSeatType($id);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Xóa loại ghế thành công';
                        $response['data'] = ['id' => $id];
                    } else {
                        throw new Exception('Không thể xóa loại ghế');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST hoặc DELETE');
                }
                break;

            default:
                throw new Exception('Action không hợp lệ. Các action hợp lệ: getAll, getById, create, update, delete');
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['error'] = $e->getMessage();
        $response['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
    }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function handleCinemaRequests($controller, $action, $method, &$response, $cinemaService = null)
{
    try {
        switch ($action) {
            case 'getAll':
                if ($method === 'GET') {
                    $cinemas = $controller->getAllCinemas();
                    $response['success'] = true;
                    $response['data'] = $cinemas;
                    $response['message'] = 'Lấy danh sách rạp chiếu thành công';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'getById':
                if ($method === 'GET') {
                    $id = $_GET['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }
                    $cinema = $controller->getCinemaById($id);
                    $response['success'] = true;
                    $response['data'] = $cinema;
                    $response['message'] = $cinema ? 'Lấy thông tin rạp chiếu thành công' : 'Không tìm thấy rạp chiếu';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'getLocations':
                if ($method === 'GET') {
                    $locations = $controller->getAllLocations();
                    $response['success'] = true;
                    $response['data'] = $locations;
                    $response['message'] = 'Lấy danh sách địa điểm thành công';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'getStatuses':
                if ($method === 'GET') {
                    $statuses = $controller->getAllCinemaStatuses();
                    $response['success'] = true;
                    $response['data'] = $statuses;
                    $response['message'] = 'Lấy danh sách trạng thái rạp thành công';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'create':
                if ($method === 'POST') {
                    $name = $_POST['name'] ?? '';
                    $address = $_POST['address'] ?? '';
                    $locationId = $_POST['location_id'] ?? null;
                    $statusId = $_POST['status_id'] ?? null;

                    if (!$name || !$address || !$locationId || !$statusId) {
                        throw new Exception('Vui lòng điền đầy đủ thông tin (name, address, location_id, status_id)');
                    }

                    // Gọi service trực tiếp để tránh redirect
                    if (!$cinemaService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $cinemaService->createCinema($name, $address, $locationId, $statusId);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Tạo rạp chiếu thành công';
                        $response['data'] = [
                            'name' => $name,
                            'address' => $address,
                            'location_id' => $locationId,
                            'status_id' => $statusId,
                            'id' => $result
                        ];
                    } else {
                        throw new Exception('Không thể tạo rạp chiếu');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'update':
                if ($method === 'POST') {
                    $id = $_GET['id'] ?? $_POST['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }

                    $name = $_POST['name'] ?? '';
                    $address = $_POST['address'] ?? '';
                    $locationId = $_POST['location_id'] ?? null;
                    $statusId = $_POST['status_id'] ?? null;

                    if (!$name || !$address || !$locationId || !$statusId) {
                        throw new Exception('Vui lòng điền đầy đủ thông tin (name, address, location_id, status_id)');
                    }

                    // Gọi service trực tiếp để tránh redirect
                    if (!$cinemaService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $cinemaService->updateCinema($id, $name, $address, $locationId, $statusId);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Cập nhật rạp chiếu thành công';
                        $response['data'] = [
                            'id' => $id,
                            'name' => $name,
                            'address' => $address,
                            'location_id' => $locationId,
                            'status_id' => $statusId
                        ];
                    } else {
                        throw new Exception('Không thể cập nhật rạp chiếu');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'delete':
                if ($method === 'POST' || $method === 'DELETE') {
                    $id = $_GET['id'] ?? $_POST['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }

                    // Gọi service trực tiếp để tránh redirect
                    if (!$cinemaService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $cinemaService->deleteCinema($id);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Xóa rạp chiếu thành công';
                        $response['data'] = ['id' => $id];
                    } else {
                        throw new Exception('Không thể xóa rạp chiếu');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST hoặc DELETE');
                }
                break;

            default:
                throw new Exception('Action không hợp lệ. Các action hợp lệ: getAll, getById, getLocations, getStatuses, create, update, delete');
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['error'] = $e->getMessage();
        $response['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
    }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function handleSeatRequests($controller, $action, $method, &$response, $seatService = null)
{
    try {
        switch ($action) {
            case 'getByHall':
                if ($method === 'GET') {
                    $hallId = $_GET['hall_id'] ?? null;
                    if (!$hallId) {
                        throw new Exception('Thiếu tham số hall_id');
                    }
                    $seats = $controller->getSeatsByHall($hallId);
                    $response['success'] = true;
                    $response['data'] = $seats;
                    $response['message'] = 'Lấy danh sách ghế thành công';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'getById':
                if ($method === 'GET') {
                    $id = $_GET['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }
                    $seat = $controller->getSeatById($id);
                    $response['success'] = true;
                    $response['data'] = $seat;
                    $response['message'] = $seat ? 'Lấy thông tin ghế thành công' : 'Không tìm thấy ghế';
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng GET');
                }
                break;

            case 'create':
                if ($method === 'POST') {
                    $hallId = $_POST['hall_id'] ?? null;
                    $seatTypeId = $_POST['seat_type_id'] ?? null;
                    $rowName = $_POST['row_name'] ?? '';
                    $seatNumber = $_POST['seat_number'] ?? null;

                    if (!$hallId || !$seatTypeId || !$rowName || !$seatNumber) {
                        throw new Exception('Vui lòng điền đầy đủ thông tin (hall_id, seat_type_id, row_name, seat_number)');
                    }

                    if (!$seatService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $seatService->createSeat($hallId, $seatTypeId, $rowName, $seatNumber);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Tạo ghế thành công';
                        $response['data'] = [
                            'id' => $result,
                            'hall_id' => $hallId,
                            'seat_type_id' => $seatTypeId,
                            'row_name' => $rowName,
                            'seat_number' => $seatNumber
                        ];
                    } else {
                        throw new Exception('Không thể tạo ghế');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'update':
                if ($method === 'POST') {
                    $id = $_GET['id'] ?? $_POST['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }

                    $seatTypeId = $_POST['seat_type_id'] ?? null;
                    if (!$seatTypeId) {
                        throw new Exception('Vui lòng chọn loại ghế');
                    }

                    if (!$seatService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $seatService->updateSeat($id, $seatTypeId);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Cập nhật ghế thành công';
                        $response['data'] = [
                            'id' => $id,
                            'seat_type_id' => $seatTypeId
                        ];
                    } else {
                        throw new Exception('Không thể cập nhật ghế');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'delete':
                if ($method === 'POST' || $method === 'DELETE') {
                    $id = $_GET['id'] ?? $_POST['id'] ?? null;
                    if (!$id) {
                        throw new Exception('Thiếu tham số id');
                    }

                    if (!$seatService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $seatService->deleteSeat($id);

                    if ($result) {
                        $response['success'] = true;
                        $response['message'] = 'Xóa ghế thành công';
                        $response['data'] = ['id' => $id];
                    } else {
                        throw new Exception('Không thể xóa ghế');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST hoặc DELETE');
                }
                break;

            case 'deleteAll':
                if ($method === 'POST') {
                    $hallId = $_GET['hall_id'] ?? $_POST['hall_id'] ?? null;
                    if (!$hallId) {
                        throw new Exception('Thiếu tham số hall_id');
                    }

                    if (!$seatService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $seatService->deleteAllSeatsByHall($hallId);

                    if ($result !== false) {
                        $response['success'] = true;
                        $response['message'] = 'Xóa sơ đồ ghế thành công';
                        $response['data'] = ['hall_id' => $hallId, 'deleted_count' => $result];
                    } else {
                        throw new Exception('Không thể xóa sơ đồ ghế');
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            case 'createBulk':
                if ($method === 'POST') {
                    $hallId = $_POST['hall_id'] ?? null;
                    $seats = json_decode($_POST['seats'] ?? '[]', true);

                    if (!$hallId || empty($seats)) {
                        throw new Exception('Vui lòng điền đầy đủ thông tin (hall_id, seats)');
                    }

                    if (!$seatService) {
                        throw new Exception('Service không được khởi tạo');
                    }
                    $result = $seatService->createBulkSeats($hallId, $seats);

                    $response['success'] = true;
                    $response['message'] = "Tạo sơ đồ ghế thành công ({$result['success_count']} ghế)";
                    $response['data'] = $result;
                    if (!empty($result['errors'])) {
                        $response['warnings'] = $result['errors'];
                    }
                } else {
                    throw new Exception('Method không hợp lệ. Sử dụng POST');
                }
                break;

            default:
                throw new Exception('Action không hợp lệ. Các action hợp lệ: getByHall, getById, create, update, delete, deleteAll, createBulk');
        }
    } catch (Exception $e) {
        $response['success'] = false;
        $response['error'] = $e->getMessage();
        $response['message'] = 'Có lỗi xảy ra: ' . $e->getMessage();
    }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

function showApiList()
{
    $apiList = [
        'title' => 'Danh sách API Test',
        'description' => 'Sử dụng: test_api.php?controller={controller}&action={action}&id={id}',
        'controllers' => [
            'halls' => [
                'description' => 'Quản lý phòng chiếu',
                'actions' => [
                    'getAll' => 'GET - Lấy danh sách phòng chiếu (có thể thêm ?cinema_id=1)',
                    'getById' => 'GET - Lấy phòng chiếu theo ID (?id=1)',
                    'getStatuses' => 'GET - Lấy danh sách trạng thái phòng',
                    'getCinemas' => 'GET - Lấy danh sách rạp',
                    'create' => 'POST - Tạo phòng chiếu mới (POST: cinema_id, name, status_id)',
                    'update' => 'POST - Cập nhật phòng chiếu (?id=1, POST: cinema_id, name, status_id)',
                    'delete' => 'POST/DELETE - Xóa phòng chiếu (?id=1)'
                ]
            ],
            'hall_status' => [
                'description' => 'Quản lý trạng thái phòng',
                'actions' => [
                    'getAll' => 'GET - Lấy danh sách trạng thái',
                    'getById' => 'GET - Lấy trạng thái theo ID (?id=1)',
                    'create' => 'POST - Tạo trạng thái mới (POST: status_name)',
                    'update' => 'POST - Cập nhật trạng thái (?id=1, POST: status_name)',
                    'delete' => 'POST/DELETE - Xóa trạng thái (?id=1)'
                ]
            ],
            'seat_types' => [
                'description' => 'Quản lý loại ghế',
                'actions' => [
                    'getAll' => 'GET - Lấy danh sách loại ghế',
                    'getById' => 'GET - Lấy loại ghế theo ID (?id=1)',
                    'create' => 'POST - Tạo loại ghế mới (POST: type_name, price_surcharge)',
                    'update' => 'POST - Cập nhật loại ghế (?id=1, POST: type_name, price_surcharge)',
                    'delete' => 'POST/DELETE - Xóa loại ghế (?id=1)'
                ]
            ]
        ],
        'examples' => [
            'GET - Lấy tất cả phòng chiếu' => 'test_api.php?controller=halls&action=getAll',
            'GET - Lấy phòng chiếu theo ID' => 'test_api.php?controller=halls&action=getById&id=1',
            'GET - Lấy phòng chiếu theo rạp' => 'test_api.php?controller=halls&action=getAll&cinema_id=1',
            'GET - Lấy tất cả trạng thái' => 'test_api.php?controller=hall_status&action=getAll',
            'GET - Lấy tất cả loại ghế' => 'test_api.php?controller=seat_types&action=getAll'
        ]
    ];

    echo json_encode($apiList, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
