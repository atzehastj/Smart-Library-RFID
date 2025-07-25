<?php
session_start();
include '../database.php';

// Cek sesi login siswa
if (!isset($_SESSION['id_siswa'])) {
    echo "❌ Sesi tidak ditemukan. Silakan login kembali.";
    exit;
}

$id_siswa = $_SESSION['id_siswa'];

// Ambil data siswa
$q = $conn->query("SELECT * FROM siswa WHERE id = $id_siswa");
if ($q->num_rows == 0) {
    echo "❌ Data siswa tidak ditemukan.";
    exit;
}
$siswa = $q->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Pengembalian Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body {
            background: linear-gradient(to bottom right, #f2f7ff, #e0ecff);
            font-family: 'Poppins', sans-serif;
        }

        .container {
            max-width: 600px;
        }

        .foto-profil {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #dee2e6;
        }

        .card {
            border-radius: 20px;
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.1);
            border: none;
        }

        .form-control {
            border-radius: 12px;
        }

        .btn {
            border-radius: 10px;
        }

        .alert {
            border-radius: 14px;
        }
    </style>
</head>

<body>

    <div class="container my-5">
        <div class="card p-4">
            <div class="text-center mb-4">
                <img src="<?= !empty($siswa['foto']) ? '../foto/' . $siswa['foto'] : 'https://cdn-icons-png.flaticon.com/512/3135/3135715.png' ?>" class="foto-profil mb-3">
                <h4>📥 Pengembalian Buku</h4>
                <p><strong><?= htmlspecialchars($siswa['nama']) ?></strong> - <?= htmlspecialchars($siswa['kelas']) ?></p>
            </div>

            <?php if (isset($_GET['status'])): ?>
                <?php if ($_GET['status'] == 'sukses'): ?>
                    <div class="alert alert-success">✅ Buku berhasil dikembalikan!</div>
                <?php elseif ($_GET['status'] == 'gagal'): ?>
                    <div class="alert alert-danger">❌ Gagal mengembalikan buku.</div>
                <?php else: ?>
                    <div class="alert alert-warning"><?= htmlspecialchars($_GET['status']) ?></div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="proses_pengembalian.php" method="get">
                <label for="buku" class="form-label">📚 Scan UID Buku</label>
                <input type="text" name="buku" id="buku" class="form-control mb-3" placeholder="Scan atau ketik UID buku" autofocus required>

                <button type="submit" class="btn btn-primary w-100">🔁 Kembalikan Buku</button>
            </form>

            <div class="mt-4 text-center">
                <a href="../dashboard_siswa.php" class="btn btn-outline-secondary">⬅️ Kembali ke Dashboard</a>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('#buku').val('');
        });

        let lastUID = '';
        function checkForNewRFID() {
            if ($('#buku').val() === '') {
                $.ajax({
                url: "../get_scanned_uid.php",
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