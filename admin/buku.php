<?php
session_start();
require '../koneksi.php';

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

// Tambah Buku
if (isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($conn, $_POST['pengarang']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);

    $query = "INSERT INTO buku (judul, pengarang, penerbit, tahun, stok) VALUES ('$judul', '$pengarang', '$penerbit', '$tahun', '$stok')";
    if (mysqli_query($conn, $query)) {
        $message = "success_tambah";
    }
}

// Edit Buku
if (isset($_POST['edit'])) {
    $id_buku = $_POST['id_buku'];
    $judul = mysqli_real_escape_string($conn, $_POST['judul']);
    $pengarang = mysqli_real_escape_string($conn, $_POST['pengarang']);
    $penerbit = mysqli_real_escape_string($conn, $_POST['penerbit']);
    $tahun = mysqli_real_escape_string($conn, $_POST['tahun']);
    $stok = mysqli_real_escape_string($conn, $_POST['stok']);

    $query = "UPDATE buku SET judul='$judul', pengarang='$pengarang', penerbit='$penerbit', tahun='$tahun', stok='$stok' WHERE id_buku=$id_buku";
    if (mysqli_query($conn, $query)) {
        $message = "success_edit";
    }
}

// Hapus Buku
if (isset($_GET['hapus'])) {
    $id_buku = $_GET['hapus'];
    $query = "DELETE FROM buku WHERE id_buku=$id_buku";
    if (mysqli_query($conn, $query)) {
        header("Location: buku.php?msg=success_hapus");
        exit;
    }
}

// Pencarian
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query_buku = "SELECT * FROM buku WHERE judul LIKE '%$search%' OR pengarang LIKE '%$search%' ORDER BY id_buku DESC";
} else {
    $query_buku = "SELECT * FROM buku ORDER BY id_buku DESC";
}
$result_buku = mysqli_query($conn, $query_buku);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Buku - Digital Library</title>
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
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt me-2"></i> Dashboard</a></li>
            <li class="active"><a href="buku.php"><i class="fas fa-book me-2"></i> Data Buku</a></li>
            <li><a href="user.php"><i class="fas fa-users me-2"></i> Data Anggota</a></li>
            <li><a href="transaksi.php"><i class="fas fa-exchange-alt me-2"></i> Transaksi</a></li>
            <li class="mt-5"><a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
            <div class="container-fluid">
                <span class="navbar-brand h1">Manajemen Buku</span>
                <div class="ms-auto">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fas fa-plus me-1"></i> Tambah Buku
                    </button>
                </div>
            </div>
        </nav>

        <div class="container-fluid">
            <!-- Search Bar -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <form action="" method="GET" class="row g-3">
                        <div class="col-md-10">
                            <input type="text" name="search" class="form-control" placeholder="Cari judul atau pengarang..." value="<?= htmlspecialchars($search); ?>">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-secondary w-100">Cari</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Judul</th>
                            <th>Pengarang</th>
                            <th>Penerbit</th>
                            <th>Tahun</th>
                            <th>Stok</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result_buku)) : ?>
                        <tr>
                            <td><?= $row['id_buku']; ?></td>
                            <td><?= $row['judul']; ?></td>
                            <td><?= $row['pengarang']; ?></td>
                            <td><?= $row['penerbit']; ?></td>
                            <td><?= $row['tahun']; ?></td>
                            <td>
                                <span class="badge <?= $row['stok'] > 0 ? 'bg-success' : 'bg-danger'; ?>">
                                    <?= $row['stok']; ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_buku']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $row['id_buku']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit<?= $row['id_buku']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="" method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Buku</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id_buku" value="<?= $row['id_buku']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Judul Buku</label>
                                                <input type="text" name="judul" class="form-control" value="<?= $row['judul']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Pengarang</label>
                                                <input type="text" name="pengarang" class="form-control" value="<?= $row['pengarang']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Penerbit</label>
                                                <input type="text" name="penerbit" class="form-control" value="<?= $row['penerbit']; ?>" required>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Tahun</label>
                                                    <input type="number" name="tahun" class="form-control" value="<?= $row['tahun']; ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Stok</label>
                                                    <input type="number" name="stok" class="form-control" value="<?= $row['stok']; ?>" required>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST">
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Buku Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Judul Buku</label>
                        <input type="text" name="judul" class="form-control" placeholder="Masukkan judul buku" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pengarang</label>
                        <input type="text" name="pengarang" class="form-control" placeholder="Nama pengarang" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Penerbit</label>
                        <input type="text" name="penerbit" class="form-control" placeholder="Nama penerbit" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tahun</label>
                            <input type="number" name="tahun" class="form-control" value="<?= date('Y'); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stok</label>
                            <input type="number" name="stok" class="form-control" value="1" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="tambah" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Handle status message
    <?php if ($message == 'success_tambah') : ?>
        Swal.fire('Berhasil!', 'Buku berhasil ditambahkan.', 'success');
    <?php elseif ($message == 'success_edit') : ?>
        Swal.fire('Berhasil!', 'Data buku diperbarui.', 'success');
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'success_hapus') : ?>
        Swal.fire('Berhasil!', 'Buku telah dihapus.', 'success');
    <?php endif; ?>

    // Delete Confirmation
    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: 'Apakah anda yakin?',
                text: "Data buku akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `buku.php?hapus=${id}`;
                }
            })
        });
    });
</script>
</body>
</html>
