<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

$query = "SELECT t.*, b.judul, b.pengarang 
          FROM transaksi t 
          JOIN buku b ON t.id_buku = b.id_buku 
          WHERE t.id_user = $id_user 
          ORDER BY t.id_transaksi DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pinjam - Digital Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-book-reader me-2"></i>Siswa</h3>
        </div>
        <ul class="list-unstyled components">
            <li><a href="dashboard.php"><i class="fas fa-home me-2"></i> Beranda</a></li>
            <li><a href="pinjam.php"><i class="fas fa-search me-2"></i> Cari & Pinjam</a></li>
            <li class="active"><a href="riwayat.php"><i class="fas fa-history me-2"></i> Riwayat Saya</a></li>
            <li class="mt-5"><a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
            <div class="container-fluid">
                <span class="navbar-brand h1">Riwayat Peminjaman</span>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>ID Transaksi</th>
                            <th>Judul Buku</th>
                            <th>Tanggal Pinjam</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td>#TR-<?= $row['id_transaksi']; ?></td>
                            <td>
                                <strong><?= $row['judul']; ?></strong><br>
                                <small class="text-muted"><?= $row['pengarang']; ?></small>
                            </td>
                            <td><?= date('d M Y', strtotime($row['tanggal_pinjam'])); ?></td>
                            <td><?= $row['tanggal_kembali'] ? date('d M Y', strtotime($row['tanggal_kembali'])) : '-'; ?></td>
                            <td>
                                <span class="badge rounded-pill <?= $row['status'] == 'dipinjam' ? 'bg-warning text-dark' : 'bg-success'; ?>">
                                    <?= ucfirst($row['status']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else : ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <p class="text-muted mb-0">Belum ada riwayat peminjaman.</p>
                                <a href="pinjam.php" class="btn btn-sm btn-link">Mulai Meminjam</a>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
