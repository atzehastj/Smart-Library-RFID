<?php
include '../database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $uid        = $_POST['uid'];
    $judul      = $_POST['judul'];
    $pengarang  = $_POST['pengarang'];
    $isbn       = $_POST['isbn'];
    $status     = $_POST['status'];

    $stmt = $conn->prepare("INSERT INTO buku (uid, judul, pengarang, isbn, status) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $uid, $judul, $pengarang, $isbn, $status);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>✅ Buku berhasil ditambahkan!</div>";
    } else {
        echo "<div class='alert alert-danger'>❌ Gagal menambahkan buku: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Tambah Buku</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="card shadow">
            <div class="card-body">
                <h2 class="card-title mb-4">➕ Tambah Buku Baru</h2>

                <form method="POST" action="">
                    <div class="mb-3">
                        <label class="form-label">UID RFID</label>
                        <input type="text" name="uid" id="uid" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Judul Buku</label>
                        <input type="text" name="judul" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Pengarang</label>
                        <input type="text" name="pengarang" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ISBN</label>
                        <input type="text" name="isbn" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select" required>
                            <option value="tersedia">Tersedia</option>
                            <option value="dipinjam">Dipinjam</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">💾 Simpan</button>
                    <a href="data_buku.php" class="btn btn-secondary">⬅️ Kembali</a>
                </form>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $("#uid").val("");
        });

        let lastUID = '';
        function checkForNewRFID() {
            if ($('#uid').val() === '') {
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

        // Cek setiap 1 detik untuk UID buku baru
        setInterval(checkForNewRFID, 1000);
    </script>
</body>

</html>