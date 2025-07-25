<?php
require 'database.php';
header('Content-Type: application/json');

$sql = 'SELECT id, uid FROM scanned_uid ORDER BY created_at DESC LIMIT 1';
$result = $conn->query($sql);
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode(['uid' => $row['uid']]);
    // Hapus UID setelah diambil agar tidak tampil terus
    $conn->query('DELETE FROM scanned_uid WHERE id = ' . intval($row['id']));
} else {
    echo json_encode(['uid' => '']);
}
?>
