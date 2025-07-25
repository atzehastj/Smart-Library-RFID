<?php
include '../database.php';

if (!isset($_GET['uid']) || empty($_GET['uid'])) {
    die("❌ UID tidak ditemukan.");
}

$uid = $_GET['uid'];

// Ambil data siswa untuk cek apakah ada foto
$cek = $conn->prepare("SELECT foto FROM siswa WHERE uid = ?");
$cek->bind_param("s", $uid);
$cek->execute();
$result = $cek->get_result();

if ($result->num_rows == 0) {
    die("❌ Siswa tidak ditemukan.");
}

$data = $result->fetch_assoc();

// Hapus file foto jika ada dan bukan default
if (!empty($data['foto']) && file_exists("../foto/" . $data['foto'])) {
    unlink("../foto/" . $data['foto']);
}

// Hapus dari database
$hapus = $conn->prepare("DELETE FROM siswa WHERE uid = ?");
$hapus->bind_param("s", $uid);
if ($hapus->execute()) {
    header("Location: data_siswa.php?success=hapus");
    exit;
} else {
    die("❌ Gagal menghapus data.");
}
