<?php
session_start();
require '../koneksi.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

// Update Status Kembali
if (isset($_GET['kembali'])) {
    $id_transaksi = $_GET['kembali'];
    $tgl_kembali = date('Y-m-d');

    // Ambil id_buku dari transaksi
    $res = mysqli_query($conn, "SELECT id_buku FROM transaksi WHERE id_transaksi = $id_transaksi");
    $data = mysqli_fetch_assoc($res);
    $id_buku = $data['id_buku'];

    // Update status transaksi
    mysqli_query($conn, "UPDATE transaksi SET status = 'dikembalikan', tanggal_kembali = '$tgl_kembali' WHERE id_transaksi = $id_transaksi");
    
    // Tambah stok buku
    mysqli_query($conn, "UPDATE buku SET stok = stok + 1 WHERE id_buku = $id_buku");

    header("Location: transaksi.php?msg=success_kembali");
    exit;
}

// Hapus Transaksi
if (isset($_GET['hapus'])) {
    $id_transaksi = $_GET['hapus'];
    mysqli_query($conn, "DELETE FROM transaksi WHERE id_transaksi = $id_transaksi");
    header("Location: transaksi.php?msg=success_hapus");
    exit;
}

// Filter Pencarian
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT t.*, u.nama, b.judul 
              FROM transaksi t 
              JOIN users u ON t.id_user = u.id_user 
              JOIN buku b ON t.id_buku = b.id_buku 
              WHERE u.nama LIKE '%$search%' OR b.judul LIKE '%$search%'
              ORDER BY t.id_transaksi DESC";
} else {
    $query = "SELECT t.*, u.nama, b.judul 
              FROM transaksi t 
              JOIN users u ON t.id_user = u.id_user 
              JOIN buku b ON t.id_buku = b.id_buku 
              ORDER BY t.id_transaksi DESC";
}
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaksi - Digital Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div id="wrapper">
    <nav id="sidebar">
        <div class="sidebar-header">
            <h3><i class="fas fa-book-open me-2"></i>E-Perpus</h3>
        </div>
        <ul class="list-unstyled components">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li><a href="buku.php"><i class="fas fa-book me-2"></i> Data Buku</a></li>
            <li><a href="user.php"><i class="fas fa-users me-2"></i> Data Anggota</a></li>
            <li class="active"><a href="transaksi.php"><i class="fas fa-exchange-alt me-2"></i> Transaksi</a></li>
            <li class="mt-5"><a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
            <div class="container-fluid">
                <span class="navbar-brand h1">Riwayat Transaksi</span>
            </div>
        </nav>

        <div class="container-fluid">
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="row g-3">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control" placeholder="Cari nama anggota atau judul buku..." value="<?= htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Anggota</th>
                            <th>Buku</th>
                            <th>Pinjam</th>
                            <th>Kembali</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0) : ?>
                        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                        <tr>
                            <td><?= $row['id_transaksi']; ?></td>
                            <td><?= $row['nama']; ?></td>
                            <td><?= $row['judul']; ?></td>
                            <td><?= date('d/m/Y', strtotime($row['tanggal_pinjam'])); ?></td>
                            <td><?= $row['tanggal_kembali'] ? date('d/m/Y', strtotime($row['tanggal_kembali'])) : '-'; ?></td>
                            <td>
                                <span class="badge <?= $row['status'] == 'dipinjam' ? 'bg-warning' : 'bg-success'; ?>">
                                    <?= ucfirst($row['status']); ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($row['status'] == 'dipinjam') : ?>
                                    <button class="btn btn-sm btn-success btn-kembali" data-id="<?= $row['id_transaksi']; ?>" title="Tandai Dikembalikan">
                                        <i class="fas fa-check-circle"></i>
                                    </button>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $row['id_transaksi']; ?>" title="Hapus Riwayat">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else : ?>
                        <tr><td colspan="7" class="text-center">Tidak ada data transaksi.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    <?php if (isset($_GET['msg'])) : ?>
        <?php if ($_GET['msg'] == 'success_kembali') : ?>
            Swal.fire('Berhasil!', 'Buku telah dikembalikan dan stok diperbarui.', 'success');
        <?php elseif ($_GET['msg'] == 'success_hapus') : ?>
            Swal.fire('Berhasil!', 'Riwayat transaksi dihapus.', 'success');
        <?php endif; ?>
    <?php endif; ?>

    document.querySelectorAll('.btn-kembali').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: 'Konfirmasi Pengembalian',
                text: "Pastikan buku sudah diterima dengan baik.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                confirmButtonText: 'Ya, Kembalikan'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `transaksi.php?kembali=${id}`;
                }
            })
        });
    });

    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: 'Hapus riwayat?',
                text: "Tindakan ini tidak dapat dibatalkan.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `transaksi.php?hapus=${id}`;
                }
            })
        });
    });
</script>
</body>
</html>
