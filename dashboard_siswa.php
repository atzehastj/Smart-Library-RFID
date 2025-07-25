<?php
session_start();
include 'database.php';

if (!isset($_SESSION['uid']) || !isset($_SESSION['id_siswa'])) {
    die("❌ Session tidak ditemukan. Silakan login ulang.");
}

$uid = $_SESSION['uid'];
$idSiswa = $_SESSION['id_siswa'];

// Ambil data siswa
$stmt = $conn->prepare("SELECT * FROM siswa WHERE uid = ?");
$stmt->bind_param("s", $uid);
$stmt->execute();
$data = $stmt->get_result();

if ($data->num_rows == 0) {
    die("❌ Data siswa tidak ditemukan.");
}
$siswa = $data->fetch_assoc();

// Hitung ringkasan peminjaman
$qAktif = $conn->query("SELECT COUNT(*) AS total FROM peminjaman WHERE id_siswa = $idSiswa AND status = 'dipinjam'");
$jumlahAktif = $qAktif->fetch_assoc()['total'];

$qTotal = $conn->query("SELECT COUNT(*) AS total FROM peminjaman WHERE id_siswa = $idSiswa");
$jumlahTotal = $qTotal->fetch_assoc()['total'];

$qTenggat = $conn->query("SELECT MIN(tenggat_kembali) AS tenggat FROM peminjaman WHERE id_siswa = $idSiswa AND status = 'dipinjam'");
$tenggatRow = $qTenggat->fetch_assoc();
$tenggatTerdekat = $tenggatRow['tenggat'] ? date('d-m-Y', strtotime($tenggatRow['tenggat'])) : '-';

// Riwayat 5 terakhir
$qRiwayat = $conn->query("
    SELECT b.judul, b.uid AS uid_buku, p.tanggal_pinjam, p.tanggal_kembali, p.status
    FROM peminjaman p
    JOIN buku b ON p.id_buku = b.id
    WHERE p.id_siswa = $idSiswa
    ORDER BY p.tanggal_pinjam DESC
    LIMIT 5
");

if (!$qRiwayat) {
    die("Query gagal: " . $conn->error);
}



?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Dashboard Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to bottom right, #e3edf7, #f0f2f5);
            font-family: 'Poppins', sans-serif;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
        }

        .foto-profil {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #e2e8f0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s ease-in-out;
        }

        .foto-profil:hover {
            transform: scale(1.05);
        }

        .foto-profil-hover-grow {
            transition: transform 0.2s ease-in-out;
        }

        .btn {
            border-radius: 12px;
            font-weight: 500;
            padding: 8px 24px;
            font-size: 0.9rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        .btn:active {
            transform: scale(0.96);
        }

        .btn-hover-grow {
            transition: transform 0.2s ease-in-out;
        }

        .btn-hover-grow:hover {
            transform: scale(1.05);

            /* Efek melebar sedikit */
        }

        .alert {
            border-radius: 16px;
            font-size: 16px;
        }

        .list-group-item {
            border: none;
            background-color: transparent;
        }

        h3 {
            font-weight: 700;
        }

        @media (max-width: 576px) {
            .foto-profil {
                width: 90px;
                height: 90px;
            }

            .btn {
                font-size: 0.8rem;
                padding: 6px 18px;
            }

            .btn-hover-grow {
                transition: transform 0.2s ease-in-out;
            }

            .btn-hover-grow:hover {
                transform: scale(1.05);

                /* Efek melebar sedikit */
            }
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <div class="card p-4">
            <div class="text-center mb-4">
                <img src="<?= !empty($siswa['foto']) ? 'foto/' . $siswa['foto'] : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' ?>" class="foto-profil" alt="Foto Profil">
                <h3 class="mt-3">👋 Selamat datang, <strong><?= htmlspecialchars($siswa['nama']) ?></strong></h3>
                <p class="text-muted"><?= htmlspecialchars($siswa['kelas']) ?></p>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <ul class="list-group">
                        <li class="list-group-item"><strong>Tempat, Tanggal Lahir:</strong> <?= htmlspecialchars($siswa['tempat_lahir']) ?>, <?= htmlspecialchars($siswa['tanggal_lahir']) ?></li>
                        <li class="list-group-item"><strong>No HP:</strong> <?= htmlspecialchars($siswa['no_hp']) ?></li>
                        <li class="list-group-item"><strong>UID:</strong> <?= htmlspecialchars($siswa['uid']) ?></li>
                    </ul>
                </div>
                <div class="col-md-6 d-flex flex-column justify-content-around">
                    <a href="siswa/peminjaman.php" class="btn btn-success mb-2 btn-hover-grow">📖 Pinjam Buku</a>
                    <a href="siswa/pengembalian.php" class="btn btn-primary mb-2 btn-hover-grow">📥 Kembalikan Buku</a>
                    <a href="siswa/riwayat_peminjaman.php" class="btn btn-warning mb-2 text-dark btn-hover-grow">📜 Riwayat Peminjaman</a>
                    <a href="siswa/edit_profil.php" class="btn btn-info text-dark btn-hover-grow">✏️ Edit Profil</a>
                </div>

            </div>

            <hr>

            <div class="row text-center mb-4">
                <div class="col-md-4">
                    <div class="alert alert-info">
                        📕 <strong><?= $jumlahAktif ?></strong><br>Peminjaman Aktif
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-warning">
                        📚 <strong><?= $jumlahTotal ?></strong><br>Total Peminjaman
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="alert alert-danger">
                        ⏰ Tenggat Terdekat:<br><strong><?= $tenggatTerdekat ?></strong>
                    </div>
                </div>
            </div>

            <h5 class="d-flex justify-content-between align-items-center">
                📚 Riwayat Terbaru
                <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#riwayatCollapse">
                    👁️ Tampilkan / Sembunyikan
                </button>
            </h5>

            <div class="collapse show" id="riwayatCollapse">
                <?php if ($qRiwayat->num_rows > 0): ?>
                    <ul class="list-group mb-3">
                        <?php while ($r = $qRiwayat->fetch_assoc()): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <div><strong><?= htmlspecialchars($r['judul']) ?></strong></div>
                                    <small class="text-muted">UID: <code><?= htmlspecialchars($r['uid_buku']) ?></code></small><br>
                                    <small>Pinjam: <?= $r['tanggal_pinjam'] ?> | Kembali: <?= $r['tanggal_kembali'] ?? '-' ?></small>
                                </div>
                                <span class="badge bg-<?= $r['status'] === 'dipinjam' ? 'warning text-dark' : 'success' ?>">
                                    <?= ucfirst($r['status']) ?>
                                </span>
                            </li>
                        <?php endwhile; ?>
                    </ul>

                    <a href="siswa/riwayat_peminjaman.php" class="btn btn-outline-primary btn-sm">🔍 Lihat Semua Riwayat</a>
                <?php else: ?>
                    <div class="alert alert-info">Belum ada riwayat peminjaman.</div>
                <?php endif; ?>
            </div>

            <div class="text-end mt-4">
                <a href="index.php" class="btn btn-outline-danger btn-sm">🚪 Keluar</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>