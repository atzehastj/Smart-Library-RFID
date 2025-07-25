<?php
include '../database.php';
$result = $conn->query("SELECT * FROM buku");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Data Buku</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <div class="container mt-5">
    <div class="card shadow">
      <div class="card-body">
        <h2 class="card-title mb-4">📗 Data Buku</h2>

        <div class="mb-3">
          <a href="tambah_buku.php" class="btn btn-success">➕ Tambah Buku Baru</a>
          <a href="../dashboard_admin.php" class="btn btn-secondary">⬅️ Kembali</a>
        </div>

        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>No</th>
                <th>UID</th>
                <th>Judul</th>
                <th>Pengarang</th>
                <th>ISBN</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1;
              while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($row['uid']) ?></td>
                  <td><?= htmlspecialchars($row['judul']) ?></td>
                  <td><?= htmlspecialchars($row['pengarang']) ?></td>
                  <td><?= htmlspecialchars($row['isbn']) ?></td>
                  <td><?= htmlspecialchars($row['status']) ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

      </div>
    </div>
  </div>

</body>

</html>