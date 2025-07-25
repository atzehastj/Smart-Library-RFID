<?php
session_start(); // 🟢 WAJIB agar session bisa digunakan
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['uid']) && !empty(trim($_POST['uid']))) {
    $uid = trim($_POST['uid']);
    print($uid);

    // Cek UID di database
    $stmt = $conn->prepare("SELECT * FROM siswa WHERE uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      $siswa = $result->fetch_assoc();

      // Simpan ke session
      $_SESSION['uid'] = $siswa['uid'];
      $_SESSION['id_siswa'] = $siswa['id'];

      // Arahkan ke dashboard
      header("Location: dashboard_siswa.php");
      exit;
    } else {
      $error = "❌ UID tidak ditemukan!";
    }
  } else {
    $error = "Silakan masukkan UID terlebih dahulu.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Smart Library - Login</title>
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
  <div class="login-box">
    <h2>📚 Smart Library</h2>
    <p>Silakan Scan Kartu Anda (UID)</p>

    <?php if (!empty($error)): ?>
      <div class="error" style="color: red; margin-bottom: 10px;"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" class="form-control" name="uid" id="uid" placeholder="Masukkan UID" autofocus>
      <button type="submit">Login</button>
    </form>
  </div>

 <script>
        $(document).ready(function() {
            $("#uid").val("");
        });


        let lastUID = '';
        function checkForNewRFID() {
            if ($('#uid').val() === '') {
                $.ajax({
                    url: 'get_scanned_uid.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(data) {
                        // Hanya isi jika UID baru dan input masih kosong
                        if (data.uid && data.uid !== lastUID && $('#uid').val() === '') {
                            $('#uid').val(data.uid);
                            lastUID = data.uid;
                        }
                    }
                });
            }
        }

        // Cek setiap 1 detik
        setInterval(checkForNewRFID, 1000);
    </script>

</body>

</html>