<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Initialize database
    $dbPath = __DIR__ . '/inventory.db';
    $db = new PDO('sqlite:' . $dbPath);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table if it doesn't exist
    $db->exec("
        CREATE TABLE IF NOT EXISTS inventory (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            device_name TEXT NOT NULL,
            device_serial_number TEXT UNIQUE,
            device_model TEXT,
            location TEXT,
            assigned_user TEXT,
            device_phone_number TEXT,
            carrier TEXT,
            department TEXT,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // GET = Load records
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $search = isset($_GET['search']) ? '%' . $_GET['search'] . '%' : null;
        
        if ($search) {
            $stmt = $db->prepare("
                SELECT * FROM inventory
                WHERE device_name LIKE ? 
                OR device_serial_number LIKE ?
                OR device_model LIKE ?
                OR location LIKE ?
                OR assigned_user LIKE ?
                OR device_phone_number LIKE ?
                OR carrier LIKE ?
                OR department LIKE ?
                OR notes LIKE ?
                ORDER BY updated_at DESC
            ");
            $stmt->execute(array_fill(0, 9, $search));
        } else {
            $stmt = $db->query("SELECT * FROM inventory ORDER BY updated_at DESC");
        }

        echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
        exit;
    }

    // POST = Save record (create or update)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data) {
            http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'No data received'
            ]);
            exit;
        }

        $id = $data['id'] ?? '';

        // UPDATE existing record
        if (!empty($id)) {
            $stmt = $db->prepare("
                UPDATE inventory SET
                    device_name = ?,
                    device_serial_number = ?,
                    device_model = ?,
                    location = ?,
                    assigned_user = ?,
                    device_phone_number = ?,
                    carrier = ?,
                    department = ?,
                    notes = ?,
                    updated_at = CURRENT_TIMESTAMP
                WHERE id = ?
            ");

            $stmt->execute([
                $data['device_name'] ?? '',
                $data['device_serial_number'] ?? '',
                $data['device_model'] ?? '',
                $data['location'] ?? '',
                $data['assigned_user'] ?? '',
                $data['device_phone_number'] ?? '',
                $data['carrier'] ?? '',
                $data['department'] ?? '',
                $data['notes'] ?? '',
                $id
            ]);

            echo json_encode(['success' => true, 'message' => 'Record updated']);

        } else {
            // INSERT new record
            $stmt = $db->prepare("
                INSERT INTO inventory (
                    device_name,
                    device_serial_number,
                    device_model,
                    location,
                    assigned_user,
                    device_phone_number,
                    carrier,
                    department,
                    notes
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $data['device_name'] ?? '',
                $data['device_serial_number'] ?? '',
                $data['device_model'] ?? '',
                $data['location'] ?? '',
                $data['assigned_user'] ?? '',
                $data['device_phone_number'] ?? '',
                $data['carrier'] ?? '',
                $data['department'] ?? '',
                $data['notes'] ?? ''
            ]);

            echo json_encode(['success' => true, 'message' => 'Record created']);
        }
        exit;
    }

    // DELETE = Remove record
    if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $data['id'] ?? '';

        if (empty($id)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'No ID provided']);
            exit;
        }

        $stmt = $db->prepare("DELETE FROM inventory WHERE id = ?");
        $stmt->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Record deleted']);
        exit;
    }

    // PUT = Import CSV
    if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['records']) || !is_array($data['records'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid records']);
            exit;
        }

        $stmt = $db->prepare("
            INSERT OR REPLACE INTO inventory (
                device_name,
                device_serial_number,
                device_model,
                location,
                assigned_user,
                device_phone_number,
                carrier,
                department,
                notes
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $imported = 0;
        foreach ($data['records'] as $record) {
            try {
                $stmt->execute([
                    $record['device_name'] ?? '',
                    $record['device_serial_number'] ?? '',
                    $record['device_model'] ?? '',
                    $record['location'] ?? '',
                    $record['assigned_user'] ?? '',
                    $record['device_phone_number'] ?? '',
                    $record['carrier'] ?? '',
                    $record['department'] ?? '',
                    $record['notes'] ?? ''
                ]);
                $imported++;
            } catch (Exception $e) {
                // Skip duplicate serials or invalid records
            }
        }

        echo json_encode(['success' => true, 'imported' => $imported]);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
