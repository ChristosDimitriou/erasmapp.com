<?php
header("Content-Type: application/json; charset=UTF-8");

// Σύνδεση με DB
$servername = "localhost";
$username = "root";
$password = "";
$database = "erasmapp_db";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $database, $port);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

// Παίρνουμε το HTTP method
$method = $_SERVER['REQUEST_METHOD'];

// Παίρνουμε το id αν υπάρχει (πχ: universities_api.php?id=3)
$id = isset($_GET['id']) ? intval($_GET['id']) : null;

// Λειτουργία ανάλογα το HTTP method
switch ($method) {
    case 'GET':
        if ($id) {
            // i) Εμφάνιση στοιχείων συγκεκριμένου πανεπιστημίου
            $stmt = $conn->prepare("SELECT * FROM universities WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $university = $result->fetch_assoc();
                echo json_encode($university);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "University not found"]);
            }
            $stmt->close();
        } else {
            // ii) Εμφάνιση όλων των συνεργαζόμενων πανεπιστημίων
            $result = $conn->query("SELECT * FROM universities");
            $universities = [];
            while ($row = $result->fetch_assoc()) {
                $universities[] = $row;
            }
            echo json_encode($universities);
        }
        break;

    case 'POST':
        // iv) Προσθήκη νέου συνεργαζόμενου πανεπιστημίου
        // Παίρνουμε JSON δεδομένα από το σώμα του αιτήματος
        $data = json_decode(file_get_contents("php://input"), true);

        if (!isset($data['name']) || !isset($data['country'])) {
            http_response_code(400);
            echo json_encode(["error" => "Missing required fields"]);
            exit();
        }

        $name = $data['name'];
        $country = $data['country'];
        $city = isset($data['city']) ? $data['city'] : null;
        $website = isset($data['website']) ? $data['website'] : null;

        $stmt = $conn->prepare("INSERT INTO universities (name, country, city, website) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $country, $city, $website);

        if ($stmt->execute()) {
            http_response_code(201);
            echo json_encode(["message" => "University added", "id" => $stmt->insert_id]);
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to add university"]);
        }
        $stmt->close();
        break;

    case 'PUT':
        // iii) Τροποποίηση στοιχείων πανεπιστημίου βάσει id
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing university id"]);
            exit();
        }

        $data = json_decode(file_get_contents("php://input"), true);

        // Προετοιμασία δυναμική, ανάλογα με το τι πεδία υπάρχουν
        $fields = [];
        $types = "";
        $params = [];

        foreach (['name', 'country', 'city', 'website'] as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $types .= "s";
                $params[] = $data[$field];
            }
        }

        if (count($fields) === 0) {
            http_response_code(400);
            echo json_encode(["error" => "No fields to update"]);
            exit();
        }

        $sql = "UPDATE universities SET " . implode(", ", $fields) . " WHERE id = ?";
        $types .= "i";
        $params[] = $id;

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["message" => "University updated"]);
            } else {
                echo json_encode(["message" => "No changes made or university not found"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to update university"]);
        }
        $stmt->close();
        break;

    case 'DELETE':
        // v) Διαγραφή πανεπιστημίου βάσει id
        if (!$id) {
            http_response_code(400);
            echo json_encode(["error" => "Missing university id"]);
            exit();
        }

        $stmt = $conn->prepare("DELETE FROM universities WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                echo json_encode(["message" => "University deleted"]);
            } else {
                http_response_code(404);
                echo json_encode(["error" => "University not found"]);
            }
        } else {
            http_response_code(500);
            echo json_encode(["error" => "Failed to delete university"]);
        }
        $stmt->close();
        break;

    default:
        http_response_code(405);
        echo json_encode(["error" => "Method not allowed"]);
        break;
}

$conn->close();
?>
