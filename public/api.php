<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/../config/dbConfig.php';

// Models
require_once __DIR__ . '/../models/Cinema.php';
require_once __DIR__ . '/../models/Hall.php';
require_once __DIR__ . '/../models/HallStatus.php';
require_once __DIR__ . '/../models/SeatType.php';
require_once __DIR__ . '/../models/Seat.php';

// Services
require_once __DIR__ . '/../services/CinemaService.php';
require_once __DIR__ . '/../services/HallService.php';
require_once __DIR__ . '/../services/HallStatusService.php';
require_once __DIR__ . '/../services/SeatTypeService.php';
require_once __DIR__ . '/../services/SeatService.php';

function json_response(bool $success, $data = null, ?string $error = null, int $statusCode = 200): void
{
    http_response_code($statusCode);
    echo json_encode([
        'success' => $success,
        'data'    => $data,
        'error'   => $error,
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $conn = getDBConnection();
} catch (Throwable $e) {
    json_response(false, null, 'Không kết nối được database: ' . $e->getMessage(), 500);
}

// Khởi tạo services
$cinemaService     = new CinemaService(new Cinema($conn));
$hallService       = new HallService(new Hall($conn));
$hallStatusService = new HallStatusService(new HallStatus($conn));
$seatTypeService   = new SeatTypeService(new SeatType($conn));
$seatService       = new SeatService(new Seat($conn));

$controller = $_GET['controller'] ?? '';
$action     = $_GET['action'] ?? '';

try {
    switch ($controller) {
        // ======================
        // CINEMAS
        // ======================
        case 'cinemas':
            switch ($action) {
                case 'getAll':
                    // Admin cần thấy cả rạp đang hoạt động và đã đóng
                    $cinemas = $cinemaService->getAllCinemas(true);
                    json_response(true, $cinemas);

                case 'getById':
                    $id = $_GET['id'] ?? null;
                    $cinema = $cinemaService->getCinemaById($id);
                    json_response(true, $cinema);

                case 'getLocations':
                    $locations = $cinemaService->getAllLocations();
                    json_response(true, $locations);

                case 'getStatuses':
                    $statuses = $cinemaService->getAllCinemaStatuses();
                    json_response(true, $statuses);

                case 'create':
                    $name       = $_POST['name'] ?? '';
                    $address    = $_POST['address'] ?? '';
                    $locationId = $_POST['location_id'] ?? null;
                    $statusId   = $_POST['status_id'] ?? 1;

                    $newId = $cinemaService->createCinema($name, $address, $locationId, $statusId);
                    json_response(true, ['id' => $newId]);

                case 'update':
                    $id         = $_GET['id'] ?? ($_POST['cinema_id'] ?? null);
                    $name       = $_POST['name'] ?? '';
                    $address    = $_POST['address'] ?? '';
                    $locationId = $_POST['location_id'] ?? null;
                    $statusId   = $_POST['status_id'] ?? 1;

                    $ok = $cinemaService->updateCinema((int)$id, $name, $address, $locationId, $statusId);
                    json_response(true, ['updated' => (bool)$ok]);

                case 'delete':
                    $id = $_GET['id'] ?? null;
                    $ok = $cinemaService->deleteCinema($id);
                    json_response(true, ['deleted' => (bool)$ok]);

                case 'setStatus':
                    $id       = $_POST['cinema_id'] ?? null;
                    $statusId = $_POST['status_id'] ?? null;
                    if ($id === null || $id === '' || $statusId === null || $statusId === '') {
                        throw new InvalidArgumentException("Thiếu cinema_id hoặc status_id.");
                    }
                    $status = (int)$statusId;
                    if (!in_array($status, [0, 1], true)) {
                        throw new InvalidArgumentException("Status không hợp lệ.");
                    }

                    // Cập nhật status rạp và đồng bộ phòng chiếu
                    if ($status === 0) {
                        // Đóng rạp: dùng deleteCinema để soft-close và đóng luôn các phòng
                        $cinemaService->deleteCinema($id);
                    } else {
                        // Mở rạp: chỉ cần mở lại status cho rạp và tất cả phòng chiếu thuộc rạp
                        $stmt = $conn->prepare("UPDATE cinemas SET status = 1 WHERE cinema_id = ?");
                        if (!$stmt) {
                            throw new Exception("SQL Prepare Error (cinemas): " . $conn->error);
                        }
                        $cinemaIdInt = (int)$id;
                        $stmt->bind_param("i", $cinemaIdInt);
                        if (!$stmt->execute()) {
                            throw new Exception("SQL Execute Error (cinemas): " . $stmt->error);
                        }

                        $hallStmt = $conn->prepare("UPDATE halls SET status = 1 WHERE cinema_id = ?");
                        if ($hallStmt) {
                            $hallStmt->bind_param("i", $cinemaIdInt);
                            $hallStmt->execute();
                        }
                    }
                    json_response(true, ['status' => $status]);

                default:
                    json_response(false, null, 'Action không hợp lệ cho cinemas', 400);
            }

        // ======================
        // HALLS (phòng chiếu)
        // ======================
        case 'halls':
            switch ($action) {
                case 'getAll':
                    $cinemaId = $_GET['cinema_id'] ?? null;
                    $halls    = $hallService->getAllHalls($cinemaId);
                    // Bổ sung SeatCount cho từng phòng (api.js/halls-admin.js có thể cần)
                    foreach ($halls as &$hall) {
                        $hallId            = $hall['hall_id'] ?? $hall['HallID'] ?? null;
                        $hall['SeatCount'] = $hallId ? $hallService->getSeatCount($hallId) : 0;
                    }
                    unset($hall);
                    json_response(true, $halls);

                case 'getById':
                    $id   = $_GET['id'] ?? null;
                    $hall = $hallService->getHallById($id);
                    json_response(true, $hall);

                case 'getStatuses':
                    $statuses = $hallStatusService->getAllStatuses();
                    json_response(true, $statuses);

                case 'getCinemas':
                    // Dùng lại query helper trong HallController: chỉ lấy rạp đang hoạt động
                    $sql      = "SELECT cinema_id as CinemaID, name as Name FROM cinemas WHERE status = 1 ORDER BY name";
                    $result   = $conn->query($sql);
                    $cinemas  = [];
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $cinemas[] = $row;
                        }
                    }
                    json_response(true, $cinemas);

                case 'create':
                    $cinemaId = $_POST['cinema_id'] ?? null;
                    $name     = $_POST['name'] ?? '';
                    $statusId = $_POST['status_id'] ?? 1;
                    $ok       = $hallService->createHall($cinemaId, $name, $statusId);
                    json_response(true, ['created' => (bool)$ok]);

                case 'update':
                    $id       = $_GET['id'] ?? ($_POST['hall_id'] ?? null);
                    $cinemaId = $_POST['cinema_id'] ?? null;
                    $name     = $_POST['name'] ?? '';
                    $statusId = $_POST['status_id'] ?? 1;
                    $ok       = $hallService->updateHall((int)$id, $cinemaId, $name, $statusId);
                    json_response(true, ['updated' => (bool)$ok]);

                case 'delete':
                    $id = $_GET['id'] ?? null;
                    $ok = $hallService->deleteHall($id);
                    json_response(true, ['deleted' => (bool)$ok]);

                default:
                    json_response(false, null, 'Action không hợp lệ cho halls', 400);
            }

        // ======================
        // HALL_STATUS (loại trạng thái phòng)
        // ======================
        case 'hall_status':
            switch ($action) {
                case 'getAll':
                    $statuses = $hallStatusService->getAllStatuses();
                    json_response(true, $statuses);

                case 'getById':
                    $id      = $_GET['id'] ?? null;
                    $all     = $hallStatusService->getAllStatuses();
                    $found   = null;
                    foreach ($all as $st) {
                        if ((string)($st['StatusID'] ?? '') === (string)$id) {
                            $found = $st;
                            break;
                        }
                    }
                    json_response(true, $found);

                default:
                    json_response(false, null, 'Action không hợp lệ cho hall_status', 400);
            }

        // ======================
        // SEAT_TYPES (loại ghế)
        // ======================
        case 'seat_types':
            switch ($action) {
                case 'getAll':
                    $types = $seatTypeService->getAllSeatTypes();
                    json_response(true, $types);

                case 'getById':
                    $id   = $_GET['id'] ?? null;
                    $type = $seatTypeService->getSeatTypeById($id);
                    json_response(true, $type);

                case 'create':
                    $typeName       = $_POST['type_name'] ?? '';
                    $priceSurcharge = (float)($_POST['price_surcharge'] ?? 0);
                    $basePrice      = 100000;
                    $priceMultiplier = ($basePrice + $priceSurcharge) / $basePrice;
                    $ok             = $seatTypeService->createSeatType($typeName, $priceMultiplier);
                    json_response(true, ['created' => (bool)$ok]);

                case 'update':
                    $id             = $_GET['id'] ?? ($_POST['seat_type_id'] ?? null);
                    $typeName       = $_POST['type_name'] ?? '';
                    $priceSurcharge = (float)($_POST['price_surcharge'] ?? 0);
                    $basePrice      = 100000;
                    $priceMultiplier = ($basePrice + $priceSurcharge) / $basePrice;
                    $ok             = $seatTypeService->updateSeatType((int)$id, $typeName, $priceMultiplier);
                    json_response(true, ['updated' => (bool)$ok]);

                case 'delete':
                    $id = $_GET['id'] ?? null;
                    $ok = $seatTypeService->deleteSeatType($id);
                    json_response(true, ['deleted' => (bool)$ok]);

                default:
                    json_response(false, null, 'Action không hợp lệ cho seat_types', 400);
            }

        // ======================
        // SEATS (ghế)
        // ======================
        case 'seats':
            switch ($action) {
                case 'getByHall':
                    $hallId = $_GET['hall_id'] ?? null;
                    $seats  = $seatService->getSeatsByHall($hallId);
                    json_response(true, $seats);

                case 'getById':
                    $id   = $_GET['id'] ?? null;
                    $seat = $seatService->getSeatById($id);
                    json_response(true, $seat);

                case 'create':
                    $hallId     = $_POST['hall_id'] ?? null;
                    $seatTypeId = $_POST['seat_type_id'] ?? null;
                    $rowName    = $_POST['row_name'] ?? '';
                    $seatNumber = $_POST['seat_number'] ?? null;
                    $newId      = $seatService->createSeat($hallId, $seatTypeId, $rowName, $seatNumber);
                    json_response(true, ['id' => $newId]);

                case 'update':
                    $id         = $_GET['id'] ?? ($_POST['seat_id'] ?? null);
                    $seatTypeId = $_POST['seat_type_id'] ?? null;
                    $ok         = $seatService->updateSeat((int)$id, $seatTypeId);
                    json_response(true, ['updated' => (bool)$ok]);

                case 'delete':
                    $id = $_GET['id'] ?? null;
                    $ok = $seatService->deleteSeat($id);
                    json_response(true, ['deleted' => (bool)$ok]);

                case 'deleteAll':
                    $hallId = $_POST['hall_id'] ?? ($_GET['hall_id'] ?? null);
                    $count  = $seatService->deleteAllSeatsByHall($hallId);
                    json_response(true, ['deleted' => $count]);

                case 'createBulk':
                    $hallId = $_POST['hall_id'] ?? null;
                    $seats  = json_decode($_POST['seats'] ?? '[]', true);
                    $result = $seatService->createBulkSeats($hallId, $seats);
                    json_response(true, $result);

                default:
                    json_response(false, null, 'Action không hợp lệ cho seats', 400);
            }

        default:
            json_response(false, null, 'Controller không hợp lệ', 400);
    }
} catch (InvalidArgumentException $e) {
    json_response(false, null, $e->getMessage(), 400);
} catch (Exception $e) {
    json_response(false, null, $e->getMessage(), 500);
}

