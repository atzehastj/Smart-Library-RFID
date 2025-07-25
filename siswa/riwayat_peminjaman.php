<?php
session_start();
include '../database.php';

if (!isset($_SESSION['id_siswa']) || !isset($_SESSION['uid'])) {
    die("❌ Sesi tidak ditemukan. Silakan login kembali.");
}

$idSiswa = $_SESSION['id_siswa'];
$uid = $_SESSION['uid'];

// Ambil data siswa
$stmt = $conn->prepare("SELECT * FROM siswa WHERE id = ?");
$stmt->bind_param("i", $idSiswa);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ Data siswa tidak ditemukan.");
}
$siswa = $result->fetch_assoc();

// Ambil data peminjaman lengkap (dengan UID buku)
$query = $conn->query("
    SELECT b.uid AS uid_buku, b.judul, b.pengarang, p.tanggal_pinjam, p.tenggat_kembali, p.tanggal_kembali, p.status
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id
    WHERE p.id_siswa = $idSiswa
    ORDER BY p.tanggal_pinjam DESC
");
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Riwayat Peminjaman</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <h4 class="card-title">📖 Riwayat Peminjaman - <?= htmlspecialchars($siswa['nama']) ?></h4>
                <p>Kelas: <strong><?= htmlspecialchars($siswa['kelas']) ?></strong></p>

                <div class="mb-3">
                    <a href="../dashboard_siswa.php" class="btn btn-secondary">⬅️ Kembali ke Dashboard</a>
                </div>

                <?php if ($query->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>UID Buku</th>
                                    <th>Judul</th>
                                    <th>Pengarang</th>
                                    <th>Tanggal Pinjam</th>
                                    <th>Tenggat</th>
                                    <th>Tanggal Kembali</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $no = 1;
                                while ($row = $query->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><code><?= htmlspecialchars($row['uid_buku']) ?></code></td>
                                        <td><?= htmlspecialchars($row['judul']) ?></td>
                                        <td><?= htmlspecialchars($row['pengarang']) ?></td>
                                        <td><?= $row['tanggal_pinjam'] ?></td>
                                        <td><?= $row['tenggat_kembali'] ?? '-' ?></td>
                                        <td><?= $row['tanggal_kembali'] ?? '-' ?></td>
                                        <td>
                                            <span class="badge bg-<?= $row['status'] === 'dipinjam' ? 'warning text-dark' : 'success' ?>">
                                                <?= ucfirst($row['status']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada riwayat peminjaman.</div>
                <?php endif; ?>

            </div>
        </div>
    </div>

</body>

</html>