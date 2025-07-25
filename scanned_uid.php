<?php
require 'database.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['uid'] ?? '';
    if (!empty($uid)) {
        // Simpan UID hasil scan ke tabel scanned_uid
        $stmt = $conn->prepare('INSERT INTO scanned_uid (uid, created_at) VALUES (?, NOW())');
        $stmt->bind_param('s', $uid);
        $stmt->execute();
        echo json_encode(['status' => 'success', 'message' => 'UID saved to database']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'UID is empty']);
    }
    exit;
}

// GET: Ambil UID terbaru hasil scan
$sql = 'SELECT uid FROM scanned_uid ORDER BY created_at DESC LIMIT 1';
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['uid' => $row['uid']]);
} else {
    echo json_encode(['uid' => '']);
}
