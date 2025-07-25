<?php
session_start();
include '../database.php';

if (!isset($_SESSION['id_siswa']) || !isset($_SESSION['uid'])) {
    die("❌ Sesi tidak ditemukan. Silakan login kembali.");
}

$id_siswa = $_SESSION['id_siswa'];
$uid = $_SESSION['uid'];

// Ambil data siswa berdasarkan ID
$query = $conn->prepare("SELECT * FROM siswa WHERE id = ?");
$query->bind_param("i", $id_siswa);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("❌ Data siswa tidak ditemukan.");
}

$siswa = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama           = $_POST['nama'];
    $kelas          = $_POST['kelas'];
    $tempat_lahir   = $_POST['tempat_lahir'];
    $tanggal_lahir  = $_POST['tanggal_lahir'];

    // Proses upload foto jika ada
    if (!empty($_FILES['foto']['name'])) {
        $ext      = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $destination = "../foto/$filename";

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destination)) {
            // Hapus foto lama jika ada dan bukan default
            if (!empty($siswa['foto']) && file_exists("../foto/" . $siswa['foto'])) {
                unlink("../foto/" . $siswa['foto']);
            }

            $stmt = $conn->prepare("UPDATE siswa SET nama=?, kelas=?, tempat_lahir=?, tanggal_lahir=?, foto=? WHERE id=?");
            $stmt->bind_param("sssssi", $nama, $kelas, $tempat_lahir, $tanggal_lahir, $filename, $id_siswa);
        } else {
            $error = "❌ Gagal upload foto.";
        }
    } else {
        $stmt = $conn->prepare("UPDATE siswa SET nama=?, kelas=?, tempat_lahir=?, tanggal_lahir=? WHERE id=?");
        $stmt->bind_param("ssssi", $nama, $kelas, $tempat_lahir, $tanggal_lahir, $id_siswa);
    }

    if (isset($stmt) && $stmt->execute()) {
        header("Location: ../dashboard_siswa.php");
        exit;
    } else {
        $error = "❌ Gagal menyimpan perubahan.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Edit Profil Siswa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">✏️ Edit Profil Siswa</h5>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">UID (tidak dapat diubah)</label>
                        <input type="text" class="form-control" value="<?= htmlspecialchars($siswa['uid']) ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label for="nama" class="form-label">Nama</label>
                        <input type="text" id="nama" name="nama" class="form-control" value="<?= htmlspecialchars($siswa['nama']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="kelas" class="form-label">Kelas</label>
                        <input type="text" id="kelas" name="kelas" class="form-control" value="<?= htmlspecialchars($siswa['kelas']) ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                        <input type="text" id="tempat_lahir" name="tempat_lahir" class="form-control" value="<?= htmlspecialchars($siswa['tempat_lahir']) ?>">
                    </div>

                    <div class="mb-3">
                        <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                        <input type="date" id="tanggal_lahir" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($siswa['tanggal_lahir']) ?>">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Foto Profil (opsional)</label>
                        <?php
                        $fotoPath = !empty($siswa['foto']) && file_exists("../foto/{$siswa['foto']}")
                            ? "../foto/{$siswa['foto']}"
                            : "https://cdn-icons-png.flaticon.com/512/3135/3135715.png";
                        ?>
                        <div class="mb-2">
                            <img src="<?= $fotoPath ?>?v=<?= time() ?>" alt="Foto Profil" class="rounded-circle border" width="100" height="100" style="object-fit: cover;">
                        </div>
                        <input type="file" name="foto" class="form-control" accept="image/*">
                        <?php if (!empty($siswa['foto']) && file_exists('../foto/' . $siswa['foto'])): ?>
                            <a href="hapus_foto.php" class="btn btn-outline-danger btn-sm mt-2" onclick="return confirm('Yakin ingin menghapus foto ini?')">🗑️ Hapus Foto</a>
                        <?php endif; ?>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="../dashboard_siswa.php" class="btn btn-secondary">↩️ Batal</a>
                        <button type="submit" class="btn btn-primary">💾 Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

</html>