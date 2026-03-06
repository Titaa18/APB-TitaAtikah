<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil statistik user
$pinjam_aktif = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM transaksi WHERE id_user = $id_user AND status = 'dipinjam'"))['count'];
$total_pinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM transaksi WHERE id_user = $id_user"))['count'];

// Daftar buku terbaru
$buku_baru = mysqli_query($conn, "SELECT * FROM buku ORDER BY id_buku DESC LIMIT 4");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Digital Library</title>
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
            <li class="active"><a href="dashboard.php"><i class="fas fa-home me-2"></i> Beranda</a></li>
            <li><a href="pinjam.php"><i class="fas fa-search me-2"></i> Cari & Pinjam</a></li>
            <li><a href="riwayat.php"><i class="fas fa-history me-2"></i> Riwayat Saya</a></li>
            <li class="mt-5"><a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
            <div class="container-fluid">
                <span class="navbar-brand h1 text-primary">Digital Library</span>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 text-muted">Halo, <strong><?= explode(' ', $_SESSION['nama'])[0]; ?></strong></span>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']); ?>&background=0D6EFD&color=fff" class="rounded-circle" width="35">
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <!-- Welcome Jumbotron -->
            <div class="p-5 mb-4 bg-primary text-white rounded-3 shadow">
                <div class="container-fluid py-2">
                    <h1 class="display-5 fw-bold">Selamat Datang di Perpustakaan</h1>
                    <p class="col-md-8 fs-4">Akses ribuan pengetahuan dalam satu klik. Pinjam buku favoritmu sekarang juga!</p>
                    <a href="pinjam.php" class="btn btn-light btn-lg text-primary fw-bold">Mulai Meminjam</a>
                </div>
            </div>

            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card stat-card bg-info text-white">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title">Sedang Dipinjam</h6>
                                <h2 class="mb-0"><?= $pinjam_aktif; ?> Buku</h2>
                            </div>
                            <i class="fas fa-book-reader fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title">Total Pernah Dipinjam</h6>
                                <h2 class="mb-0"><?= $total_pinjam; ?> Kali</h2>
                            </div>
                            <i class="fas fa-check-double fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Books Recommendation -->
            <h4 class="mb-3">Buku Terbaru</h4>
            <div class="row">
                <?php while($buku = mysqli_fetch_assoc($buku_baru)): ?>
                <div class="col-md-3 mb-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body">
                            <h5 class="card-title"><?= $buku['judul']; ?></h5>
                            <h6 class="card-subtitle mb-2 text-muted"><?= $buku['pengarang']; ?></h6>
                            <p class="card-text small text-muted"><?= $buku['penerbit']; ?> (<?= $buku['tahun']; ?>)</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge <?= $buku['stok'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                    Stok: <?= $buku['stok']; ?>
                                </span>
                                <a href="pinjam.php" class="btn btn-sm btn-outline-primary">Pinjam</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
