<?php
include '../database.php';

$uid           = $_POST['uid'];
$nama          = $_POST['nama'];
$kelas         = $_POST['kelas'];
$tempat_lahir  = $_POST['tempat_lahir'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$no_hp         = $_POST['no_hp'];

$sql = "INSERT INTO siswa (uid, nama, kelas, tempat_lahir, tanggal_lahir, no_hp)
        VALUES ('$uid', '$nama', '$kelas', '$tempat_lahir', '$tanggal_lahir', '$no_hp')";

if ($conn->query($sql)) {
    echo "✅ Siswa berhasil ditambahkan.";
} else {
    echo "❌ Gagal: " . $conn->error;
}
?>
