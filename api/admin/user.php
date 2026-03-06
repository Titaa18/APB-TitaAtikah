<?php
session_start();
require '../koneksi.php';

// Cek login & role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$message = '';

// Tambah User
if (isset($_POST['tambah'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
    if (mysqli_query($conn, $query)) {
        $message = "success_tambah";
    }
}

// Edit User
if (isset($_POST['edit'])) {
    $id_user = $_POST['id_user'];
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);

    if (!empty($_POST['password'])) {
        $password = md5($_POST['password']);
        $query = "UPDATE users SET nama='$nama', email='$email', role='$role', password='$password' WHERE id_user=$id_user";
    } else {
        $query = "UPDATE users SET nama='$nama', email='$email', role='$role' WHERE id_user=$id_user";
    }

    if (mysqli_query($conn, $query)) {
        $message = "success_edit";
    }
}

// Hapus User
if (isset($_GET['hapus'])) {
    $id_user = $_GET['hapus'];
    // Prevent deleting self
    if ($id_user != $_SESSION['id_user']) {
        $query = "DELETE FROM users WHERE id_user=$id_user";
        if (mysqli_query($conn, $query)) {
            header("Location: user.php?msg=success_hapus");
            exit;
        }
    } else {
        header("Location: user.php?msg=error_self");
        exit;
    }
}

// Pencarian
$search = "";
if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query_user = "SELECT * FROM users WHERE nama LIKE '%$search%' OR email LIKE '%$search%' ORDER BY id_user DESC";
} else {
    $query_user = "SELECT * FROM users ORDER BY id_user DESC";
}
$result_user = mysqli_query($conn, $query_user);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Anggota - Digital Library</title>
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
            <li><a href="buku.php"><i class="fas fa-book me-2"></i> Data Buku</a></li>
            <li class="active"><a href="user.php"><i class="fas fa-users me-2"></i> Data Anggota</a></li>
            <li><a href="transaksi.php"><i class="fas fa-exchange-alt me-2"></i> Transaksi</a></li>
            <li class="mt-5"><a href="../logout.php" onclick="return confirm('Yakin ingin logout?')"><i class="fas fa-sign-out-alt me-2"></i> Logout</a></li>
        </ul>
    </nav>

    <!-- Page Content -->
    <div id="content">
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom mb-4">
            <div class="container-fluid">
                <span class="navbar-brand h1">Manajemen Anggota</span>
                <div class="ms-auto">
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambah">
                        <i class="fas fa-user-plus me-1"></i> Tambah Anggota
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
                            <input type="text" name="search" class="form-control" placeholder="Cari nama atau email..." value="<?= htmlspecialchars($search); ?>">
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
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Terdaftar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = mysqli_fetch_assoc($result_user)) : ?>
                        <tr>
                            <td><?= $row['id_user']; ?></td>
                            <td><?= $row['nama']; ?></td>
                            <td><?= $row['email']; ?></td>
                            <td>
                                <span class="badge <?= $row['role'] == 'admin' ? 'bg-danger' : 'bg-info'; ?>">
                                    <?= ucfirst($row['role']); ?>
                                </span>
                            </td>
                            <td><?= date('d M Y', strtotime($row['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#modalEdit<?= $row['id_user']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger btn-hapus" data-id="<?= $row['id_user']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                        <!-- Modal Edit -->
                        <div class="modal fade" id="modalEdit<?= $row['id_user']; ?>" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <form action="" method="POST">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Anggota</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="id_user" value="<?= $row['id_user']; ?>">
                                            <div class="mb-3">
                                                <label class="form-label">Nama Lengkap</label>
                                                <input type="text" name="nama" class="form-control" value="<?= $row['nama']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Email</label>
                                                <input type="email" name="email" class="form-control" value="<?= $row['email']; ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Password (Kosongkan jika tidak diubah)</label>
                                                <input type="password" name="password" class="form-control">
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Role</label>
                                                <select name="role" class="form-select" required>
                                                    <option value="user" <?= $row['role'] == 'user' ? 'selected' : ''; ?>>User/Siswa</option>
                                                    <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="edit" class="btn btn-primary">Simpan</button>
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
                    <h5 class="modal-title">Tambah Anggota Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="user">User/Siswa</option>
                            <option value="admin">Admin</option>
                        </select>
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
    <?php if ($message == 'success_tambah') : ?>
        Swal.fire('Berhasil!', 'Anggota baru telah ditambahkan.', 'success');
    <?php elseif ($message == 'success_edit') : ?>
        Swal.fire('Berhasil!', 'Data anggota diperbarui.', 'success');
    <?php endif; ?>

    <?php if (isset($_GET['msg'])) : ?>
        <?php if ($_GET['msg'] == 'success_hapus') : ?>
            Swal.fire('Berhasil!', 'Anggota telah dihapus.', 'success');
        <?php elseif ($_GET['msg'] == 'error_self') : ?>
            Swal.fire('Error!', 'Anda tidak bisa menghapus akun sendiri.', 'error');
        <?php endif; ?>
    <?php endif; ?>

    document.querySelectorAll('.btn-hapus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            Swal.fire({
                title: 'Hapus anggota?',
                text: "Riwayat transaksi anggota ini mungkin akan hilang!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `user.php?hapus=${id}`;
                }
            })
        });
    });
</script>
</body>
</html>
