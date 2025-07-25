<?php
include '../database.php';

$query = "
  SELECT p.id, s.nama AS siswa, b.judul AS buku,
         p.tanggal_pinjam, p.tanggal_kembali
  FROM peminjaman p
  JOIN siswa s ON p.id_siswa = s.id
  JOIN buku b ON p.id_buku = b.id
  WHERE p.status = 'dikembalikan'
  ORDER BY p.tanggal_kembali DESC
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Daftar Pengembalian</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <div class="container mt-5">
    <div class="card shadow-lg">
      <div class="card-body">
        <h2 class="card-title mb-4">📘 Daftar Pengembalian</h2>

        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-dark">
              <tr>
                <th>No</th>
                <th>Nama Siswa</th>
                <th>Judul Buku</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
              </tr>
            </thead>
            <tbody>
              <?php $no = 1;
              while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td><?= $no++ ?></td>
                  <td><?= htmlspecialchars($row['siswa']) ?></td>
                  <td><?= htmlspecialchars($row['buku']) ?></td>
                  <td><?= htmlspecialchars($row['tanggal_pinjam']) ?></td>
                  <td><?= htmlspecialchars($row['tanggal_kembali']) ?></td>
                </tr>
              <?php endwhile; ?>
            </tbody>
          </table>
        </div>

        <a href="../dashboard_admin.php" class="btn btn-secondary mt-3">🔙 Kembali ke Dashboard</a>
      </div>
    </div>
  </div>

</body>

</html>