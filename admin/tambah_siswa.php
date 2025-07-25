<?php
include '../database.php';

$editMode = false;
$uid = '';
$nama = '';
$kelas = '';
$tempat_lahir = '';
$tanggal_lahir = '';
$no_hp = '';

if (isset($_GET['uid'])) {
    $editMode = true;
    $uid = $_GET['uid'];

    $stmt = $conn->prepare("SELECT * FROM siswa WHERE uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $s = $result->fetch_assoc();
        $nama = $s['nama'];
        $kelas = $s['kelas'];
        $tempat_lahir = $s['tempat_lahir'];
        $tanggal_lahir = $s['tanggal_lahir'];
        $no_hp = $s['no_hp'];
    }
}

// Proses penyimpanan
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid = $_POST['uid'];
    $nama = $_POST['nama'];
    $kelas = $_POST['kelas'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $no_hp = $_POST['no_hp'];

    if (isset($_POST['hapus'])) {
        $stmt = $conn->prepare("DELETE FROM siswa WHERE uid = ?");
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        header('Location: data_siswa.php?hapus=1');
        exit;
    } else {
        if ($editMode) {
            $stmt = $conn->prepare("UPDATE siswa SET nama=?, kelas=?, tempat_lahir=?, tanggal_lahir=?, no_hp=? WHERE uid=?");
            $stmt->bind_param("ssssss", $nama, $kelas, $tempat_lahir, $tanggal_lahir, $no_hp, $uid);
        } else {
            $stmt = $conn->prepare("INSERT INTO siswa (uid, nama, kelas, tempat_lahir, tanggal_lahir, no_hp) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssss", $uid, $nama, $kelas, $tempat_lahir, $tanggal_lahir, $no_hp);
        }

        if ($stmt->execute()) {
            header('Location: data_siswa.php?success=1');
            exit;
        } else {
            $error = "❌ Gagal menyimpan data siswa!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title><?= $editMode ? 'Edit Siswa' : 'Tambah Siswa' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <h2 class="mb-4"><?= $editMode ? '✏️ Edit Data Siswa' : '➕ Tambah Data Siswa' ?></h2>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="uid" value="<?= htmlspecialchars($uid) ?>">
                    <div class="mb-3">
                        <label for="uid" class="form-label">UID RFID</label>
                        <input type="text" class="form-control" id="uid" name="uid" value="<?= htmlspecialchars($uid) ?>" required>
                    </div>
                    <div class=" mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" class="form-control" id="nama" name="nama" value="<?= htmlspecialchars($nama) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="kelas" class="form-label">Kelas</label>
                        <input type="text" class="form-control" id="kelas" name="kelas" value="<?= htmlspecialchars($kelas) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" value="<?= htmlspecialchars($tempat_lahir) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" value="<?= htmlspecialchars($tanggal_lahir) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="no_hp" class="form-label">No HP</label>
                        <div class="d-flex">
                            <input type="text" class="form-control me-2" id="no_hp" name="no_hp" value="<?= htmlspecialchars($no_hp) ?>">
                            <?php if ($editMode): ?>
                                <button type="submit" name="hapus" class="btn btn-outline-danger" onclick="return confirm('Hapus data siswa ini?')">🗑️</button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">💾 Simpan</button>
                    <a href="data_siswa.php" class="btn btn-secondary">❌ Batal</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            if (!<?= $editMode ? 'true' : 'false' ?>) {
                $('#uid').val('');
            }
        });

        let lastUID = '';
        function checkForNewRFID() {
            if (!<?= $editMode ? 'true' : 'false' ?> && $('#uid').val() === '') {
                $.ajax({
                    url: '../get_scanned_uid.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Hanya isi jika UID baru dan input masih kosong
                        if (data.uid && data.uid !== lastUID && $('#uid').val() === '') {
                            $('#uid').val(data.uid);
                            lastUID = data.uid;
                        }
                    },
                    error: function() {
                        console.log('Error fetching UID');
                    }
                });
            }
        }

        // Cek setiap 1 detik untuk UID siswa baru
        setInterval(checkForNewRFID, 1000);
    </script>
</body>

</html>