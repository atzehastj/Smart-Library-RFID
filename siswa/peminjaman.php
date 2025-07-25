<?php
include '../database.php';
session_start();

$id_siswa = $_SESSION['id_siswa'] ?? $_GET['id_siswa'] ?? '';
$uid = '';
if (!empty($id_siswa)) {
    $data = $conn->query("SELECT uid FROM siswa WHERE id='$id_siswa'")->fetch_assoc();
    if ($data) {
        $uid = $data['uid'];
    }
}

$sukses = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid_buku     = $_POST['buku'] ?? '';
    $tgl_pinjam   = $_POST['tanggal_pinjam'] ?? date('Y-m-d');
    $tenggat      = $_POST['tenggat_kembali'] ?? date('Y-m-d', strtotime('+7 days'));

    // Ambil data siswa & buku
    $siswa = $conn->query("SELECT id, nama FROM siswa WHERE id='$id_siswa'")->fetch_assoc();
    $buku = $conn->query("SELECT id, judul, status FROM buku WHERE uid='$uid_buku'")->fetch_assoc();

    if (!$siswa) {
        $error = "❌ ID siswa tidak ditemukan.";
    } elseif (!$buku) {
        $error = "❌ UID buku tidak ditemukan.";
    } elseif ($buku['status'] === 'dipinjam') {
        $error = "⚠️ Buku <strong>{$buku['judul']}</strong> sedang dipinjam. Silakan pilih buku lain.";
    } else {
        // Proses simpan
        $sql = "INSERT INTO peminjaman (id_siswa, id_buku, tanggal_pinjam, tenggat_kembali, status)
            VALUES ({$siswa['id']}, {$buku['id']}, '$tgl_pinjam', '$tenggat', 'dipinjam')";
        $conn->query($sql);
        $conn->query("UPDATE buku SET status='dipinjam' WHERE id={$buku['id']}");
        $sukses = "✅ <strong>{$buku['judul']}</strong> berhasil dipinjam oleh <strong>{$siswa['nama']}</strong>.<br>
    Tenggat pengembalian: <strong>$tenggat</strong>";
    }
}

// Set nilai default tanggal
$tgl_pinjam_default = date('Y-m-d');
$tenggat_default = date('Y-m-d', strtotime('+7 days'));
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Peminjaman Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h3 class="card-title mb-4">📚 Peminjaman Buku</h3>

                <?php if ($sukses): ?>
                    <div class="alert alert-success"><?= $sukses ?></div>
                    <div class="d-flex justify-content-between mt-3">
                        <a href="peminjaman.php?id_siswa=<?= $id_siswa ?>" class="btn btn-primary">📖 Scan Buku Lain</a>
                        <a href="../dashboard_siswa.php?uid=<?= urlencode($uid) ?>" class="btn btn-secondary">⬅️ Kembali ke Dashboard</a>
                    </div>
                <?php else: ?>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <input type="hidden" name="id_siswa" value="<?= htmlspecialchars($id_siswa) ?>">

                        <div class="mb-3">
                            <label for="buku" class="form-label">Scan UID Buku</label>
                            <input type="text" name="buku" id="buku" class="form-control" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label for="tanggal_pinjam" class="form-label">Tanggal Pinjam</label>
                            <input type="date" name="tanggal_pinjam" id="tanggal_pinjam" class="form-control" value="<?= $tgl_pinjam_default ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="tenggat_kembali" class="form-label">Tenggat Pengembalian</label>
                            <input type="date" name="tenggat_kembali" id="tenggat_kembali" class="form-control" value="<?= $tenggat_default ?>" required>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="../dashboard_siswa.php?uid=<?= urlencode($uid) ?>" class="btn btn-secondary">⬅️ Kembali ke Dashboard</a>
                            <button type="submit" class="btn btn-primary">Proses Peminjaman</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#buku").val("");
        });

        let lastUID = '';
        function checkForNewRFID() {
            if ($('#buku').val() === '') {
                $.ajax({
                    url: '../get_scanned_uid.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Hanya isi jika UID baru dan input masih kosong
                        if (data.uid && data.uid !== lastUID && $('#buku').val() === '') {
                            $('#buku').val(data.uid);
                            lastUID = data.uid;
                        }
                    },
                    error: function() {
                        console.log('Error fetching UID');
                    }
                });
            }
        }

        // Cek setiap 1 detik untuk UID buku baru
        setInterval(checkForNewRFID, 1000);
    </script>
</body>

</html>