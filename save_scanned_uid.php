<?php
require 'database.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_POST["uid"] ?? "";
    if (!empty($uid)) {
        $stmt = $conn->prepare('INSERT INTO scanned_uid (uid, created_at) VALUES (?, NOW())');
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        echo json_encode(["status" => "success", "message" => "UID saved to database"]);
    } else {
        echo json_encode(["status" => "error", "message" => "UID is empty"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
}
?>
