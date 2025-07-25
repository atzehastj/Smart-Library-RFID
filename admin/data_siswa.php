<?php
include '../database.php';
$result = $conn->query("SELECT * FROM siswa");
?>

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <title>Data Siswa</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <div class="container mt-5">
    <div class="card shadow-lg">
      <div class="card-body">
        <h2 class="mb-4">📘 Data Siswa</h2>

        <a href="tambah_siswa.php" class="btn btn-success mb-3">➕ Tambah Siswa</a>

        <table class="table table-hover table-striped">
          <thead class="table-dark text-center">
            <tr>
              <th></th>
              <th> UID RFID</th>
              <th> Nama Lengkap</th>
              <th> Kelas</th>
              <th> Nomor HP</th>
              <th> Aksi</th>
            </tr>
          </thead>
          <tbody class="align-middle">
            <?php
            include '../database.php';
            $no = 1;
            $result = $conn->query("SELECT * FROM siswa");
            while ($row = $result->fetch_assoc()):
            ?>
              <tr>
                <td><?= $no++ ?></td>
                <td><?= $row['uid'] ?></td>
                <td><?= $row['nama'] ?></td>
                <td><?= $row['kelas'] ?></td>
                <td><?= $row['no_hp'] ?></td>
                <td class="text-center">
                  <a href="tambah_siswa.php?uid=<?= $row['uid'] ?>" class="btn btn-sm btn-warning">✏️ Edit</a>
                  <a href="hapus_siswa.php?uid=<?= $row['uid'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus siswa ini?')">🗑️ Hapus</a>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>

      </div>

      <a href="../dashboard_admin.php" class="btn btn-primary mt-2"> Kembali ke Dashboard</a>
    </div>
  </div>
  </div>

</body>

</html>