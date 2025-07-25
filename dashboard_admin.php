<?php
session_start();
if (!isset($_SESSION['admin'])) {
  header('Location: login_admin.php');
  exit();
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/dashboard_admin.css">
</head>

<body class="bg-light">

  <div class="container mt-5">
    <div class="card shadow-lg">
      <div class="card-body">
        <h2 class="card-title text-center mb-4">👩‍💼 Selamat Datang, Admin!</h2>

        <div class="list-group">
          <a href="admin/data_siswa.php" class="list-group-item list-group-item-action">
            📘 Data Siswa
          </a>
          <a href="admin/data_buku.php" class="list-group-item list-group-item-action">
            📕 Data Buku
          </a>
          <a href="admin/data_peminjaman.php" class="list-group-item list-group-item-action">
            📖 Data Peminjaman
          </a>
        </div>

        <div class="text-center mt-4">
          <a href="logout.php" class="btn btn-outline-danger btn-sm">🚪 Logout</a>
        </div>
      </div>
    </div>
  </div>

</body>

</html>