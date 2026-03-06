<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user') {
    header("Location: ../login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
$message = '';

// Proses Peminjaman
if (isset($_GET['id_buku'])) {
    $id_buku = $_GET['id_buku'];
    $tgl_pinjam = date('Y-m-d');

    // Cek stok
    $res_stok = mysqli_query($conn, "SELECT stok FROM buku WHERE id_buku = $id_buku");
    $data_stok = mysqli_fetch_assoc($res_stok);

    if ($data_stok['stok'] > 0) {
        // Cek apakah user sudah pinjam buku ini dan belum kembali
        $cek_pinjam = mysqli_query($conn, "SELECT * FROM transaksi WHERE id_user = $id_user AND id_buku = $id_buku AND status = 'dipinjam'");
        if (mysqli_num_rows($cek_pinjam) > 0) {
            $message = 'already_borrowed';
        } else {
            // Kurangi stok
            mysqli_query($conn, "UPDATE buku SET stok = stok - 1 WHERE id_buku = $id_buku");
            // Simpan transaksi
            mysqli_query($conn, "INSERT INTO transaksi (id_user, id_buku, tanggal_pinjam, status) VALUES ($id_user, $id_buku, '$tgl_pinjam', 'dipinjam')");
            $message = 'success';
        }
    } else {
        $message = 'out_of_stock';
    }
}

// Pencarian
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query_buku = "SELECT * FROM buku WHERE judul LIKE '%$search%' OR pengarang LIKE '%$search%' ORDER BY judul ASC";
} else {
    $query_buku = "SELECT * FROM buku ORDER BY judul ASC";
}
$result_buku = mysqli_query($conn, $query_buku);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pinjam Buku - Digital Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .book-card {
            transition: transform 0.2s;
            border-radius: 12px;
        }
        .book-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-book-reader me-2"></i>Siswa</h3>
        </div>
        <ul class="list-unstyled components">
            <li><a href="dashboard.php"><i class="fas fa-home me-2"></i> Beranda</a></li>
            <li class="active"><a href="pinjam.php"><i class="fas fa-search me-2"></i> Cari & Pinjam</a></li>
            <li><a href="riwayat.php"><i class="fas fa-history me-2"></i> Riwayat Saya</a></li>
            <li class="mt-5"><a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
            <div class="container-fluid">
                <span class="navbar-brand h1">Katalog Buku</span>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="row g-3">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control" placeholder="Cari judul atau pengarang..." value="<?= htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100">Cari Buku</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row">
                <?php if (mysqli_num_rows($result_buku) > 0) : ?>
                <?php while ($row = mysqli_fetch_assoc($result_buku)) : ?>
                <div class="col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 book-card border-0 shadow-sm">
                        <div class="card-body d-flex flex-column text-center">
                            <div class="mb-3">
                                <i class="fas fa-book fa-4x text-primary opacity-25"></i>
                            </div>
                            <h5 class="card-title text-truncate" title="<?= $row['judul']; ?>"><?= $row['judul']; ?></h5>
                            <p class="card-text text-muted mb-1"><?= $row['pengarang']; ?></p>
                            <p class="small text-secondary"><?= $row['penerbit']; ?> (<?= $row['tahun']; ?>)</p>
                            <div class="mt-auto">
                                <div class="mb-2">
                                    <span class="badge <?= $row['stok'] > 0 ? 'bg-success' : 'bg-danger'; ?> w-100">
                                        <?= $row['stok'] > 0 ? "Tersedia: " . $row['stok'] : "Stok Habis"; ?>
                                    </span>
                                </div>
                                <button class="btn btn-primary w-100 btn-pinjam" data-id="<?= $row['id_buku']; ?>" data-judul="<?= $row['judul']; ?>" <?= $row['stok'] == 0 ? 'disabled' : ''; ?>>
                                    Pinjam Sekarang
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
                <?php else : ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-frown fa-4x text-muted mb-3"></i>
                    <h4>Maaf, buku tidak ditemukan.</h4>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if ($message == 'success') : ?>
        Swal.fire('Berhasil!', 'Buku berhasil dipinjam. Silahkan cek riwayat.', 'success');
    <?php elseif ($message == 'out_of_stock') : ?>
        Swal.fire('Stok Habis!', 'Maaf, buku ini tidak tersedia untuk saat ini.', 'error');
    <?php elseif ($message == 'already_borrowed') : ?>
        Swal.fire('Peringatan!', 'Anda sudah meminjam buku ini.', 'warning');
    <?php endif; ?>

    document.querySelectorAll('.btn-pinjam').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const judul = this.getAttribute('data-judul');
            Swal.fire({
                title: 'Konfirmasi Pinjam',
                text: `Ingin meminjam buku "${judul}"?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0D6EFD',
                confirmButtonText: 'Ya, Pinjam'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `pinjam.php?id_buku=${id}`;
                }
            })
        });
    });
</script>
</body>
</html>
