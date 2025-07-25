<?php
include '../database.php';
session_start();

$id_siswa = $_SESSION['id_siswa'] ?? '';
$uid_buku = $_GET['buku'] ?? '';

if (empty($id_siswa) || empty($uid_buku)) {
    header("Location: pengembalian.php?status=Data tidak lengkap");
    exit;
}

// Cari ID buku berdasarkan UID
$buku = $conn->query("SELECT id FROM buku WHERE uid = '$uid_buku'")->fetch_assoc();

if (!$buku) {
    header("Location: pengembalian.php?status=UID buku tidak ditemukan");
    exit;
}

$id_buku = $buku['id'];

// Cek apakah buku sedang dipinjam oleh siswa ini
$cek = $conn->query("SELECT id FROM peminjaman WHERE id_siswa = $id_siswa AND id_buku = $id_buku AND status = 'dipinjam'");

if ($cek->num_rows === 0) {
    header("Location: pengembalian.php?status=Buku tidak sedang dipinjam");
    exit;
}

// Update status peminjaman dan buku
$conn->query("UPDATE peminjaman SET status = 'dikembalikan', tanggal_kembali = CURDATE() 
              WHERE id_siswa = $id_siswa AND id_buku = $id_buku AND status = 'dipinjam'");

$conn->query("UPDATE buku SET status = 'tersedia' WHERE id = $id_buku");

header("Location: pengembalian.php?status=sukses");
