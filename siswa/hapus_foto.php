<?php
include '../database.php';

if (!isset($_GET['uid']) || empty($_GET['uid'])) {
    die("❌ UID tidak ditemukan.");
}

$uid = $_GET['uid'];

// Ambil data siswa
$stmt = $conn->prepare("SELECT * FROM siswa WHERE uid = ?");
$stmt->bind_param("s", $uid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("❌ Siswa tidak ditemukan.");
}

$siswa = $result->fetch_assoc();

// Hapus file foto jika ada
if (!empty($siswa['foto'])) {
    $path = "../foto/" . $siswa['foto'];
    if (file_exists($path)) {
        unlink($path); // hapus file dari folder
    }

    // Kosongkan field `foto` di database
    $update = $conn->prepare("UPDATE siswa SET foto = NULL WHERE uid = ?");
    $update->bind_param("s", $uid);
    $update->execute();
}

header("Location: ../dashboard_siswa.php?uid=" . urlencode($uid));
exit;
