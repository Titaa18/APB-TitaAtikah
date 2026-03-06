<?php
session_start();
require '../koneksi.php';

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ambil statistik
$total_buku = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM buku"))['count'];
$total_user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM users WHERE role = 'user'"))['count'];
$total_pinjam = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM transaksi WHERE status = 'dipinjam'"))['count'];

// Data untuk Chart.js (contoh data statis atau bisa dinamis)
$data_chart = [
    'labels' => ['Buku', 'Anggota', 'Peminjaman'],
    'data' => [$total_buku, $total_user, $total_pinjam]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Digital Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div id="wrapper">
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-book-open me-2"></i>E-Perpus</h3>
        </div>

        <ul class="list-unstyled components">
            <li class="active">
                <a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a>
            </li>
            <li>
                <a href="buku.php"><i class="fas fa-book me-2"></i> Data Buku</a>
            </li>
            <li>
                <a href="user.php"><i class="fas fa-users me-2"></i> Data Anggota</a>
            </li>
            <li>
                <a href="transaksi.php"><i class="fas fa-exchange-alt me-2"></i> Transaksi</a>
            </li>
            <li class="mt-5">
                <a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
            </li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
            <div class="container-fluid">
                <span class="navbar-brand mb-0 h1">Dashboard Overview</span>
                <div class="ms-auto d-flex align-items-center">
                    <span class="me-3 text-muted">Selamat datang, <strong><?= $_SESSION['nama']; ?></strong></span>
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['nama']); ?>&background=random" class="rounded-circle" width="35" alt="Avatar">
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card stat-card bg-primary text-white">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title">Total Buku</h6>
                                <h2 class="mb-0"><?= $total_buku; ?></h2>
                            </div>
                            <i class="fas fa-book fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-success text-white">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title">Total Anggota</h6>
                                <h2 class="mb-0"><?= $total_user; ?></h2>
                            </div>
                            <i class="fas fa-users fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card stat-card bg-warning text-white">
                        <div class="card-body">
                            <div>
                                <h6 class="card-title">Buku Dipinjam</h6>
                                <h2 class="mb-0"><?= $total_pinjam; ?></h2>
                            </div>
                            <i class="fas fa-exchange-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Chart -->
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white font-weight-bold">
                            Statistik Perpustakaan
                        </div>
                        <div class="card-body">
                            <canvas id="myChart" height="150"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Recent Activity (Static Placeholder) -->
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-white font-weight-bold">
                            Informasi Sistem
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Status Server
                                    <span class="badge bg-success rounded-pill">Online</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Database
                                    <span class="badge bg-info rounded-pill">Connected</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    Version
                                    <span class="badge bg-secondary rounded-pill">1.0.0</span>
                                </li>
                            </ul>
                            <div class="mt-4 p-3 bg-light rounded text-center">
                                <small class="text-muted">Digital Library System &copy; 2024</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('myChart').getContext('2d');
    const myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?= json_encode($data_chart['labels']); ?>,
            datasets: [{
                label: 'Data Perpustakaan',
                data: <?= json_encode($data_chart['data']); ?>,
                backgroundColor: [
                    'rgba(54, 162, 235, 0.5)',
                    'rgba(75, 192, 192, 0.5)',
                    'rgba(255, 206, 86, 0.5)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(255, 206, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
</body>
</html>
