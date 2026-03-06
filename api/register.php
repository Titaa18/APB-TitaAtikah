<?php
session_start();
require 'koneksi.php';

if (isset($_SESSION['role'])) {
    header("Location: index.php");
    exit;
}

if (isset($_POST['register'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = md5($_POST['password']);
    $role = 'user';

    // Check if email exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
    if (mysqli_num_rows($check) > 0) {
        $error = "Email sudah terdaftar!";
    } else {
        $query = "INSERT INTO users (nama, email, password, role) VALUES ('$nama', '$email', '$password', '$role')";
        if (mysqli_query($conn, $query)) {
            $success = "Registrasi berhasil! Silahkan login.";
        } else {
            $error = "Registrasi gagal! Silahkan coba lagi.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Digital Library</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .register-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
            width: 100%;
            max-width: 450px;
            background: white;
        }
        .register-header {
            background: #f8f9fa;
            padding: 25px;
            text-align: center;
            border-bottom: 1px solid #eee;
        }
        .register-body {
            padding: 25px;
        }
        .btn-primary {
            background: #1e3c72;
            border: none;
            padding: 12px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: #2a5298;
        }
    </style>
</head>
<body>

<div class="register-card">
    <div class="register-header">
        <i class="fas fa-user-plus fa-3x text-primary mb-2"></i>
        <h4>Buat Akun Baru</h4>
        <p class="text-muted">Lengkapi data untuk mendaftar</p>
    </div>
    <div class="register-body">
        <?php if (isset($error)) : ?>
            <div class="alert alert-danger"><?= $error; ?></div>
        <?php endif; ?>
        <?php if (isset($success)) : ?>
            <div class="alert alert-success"><?= $success; ?></div>
        <?php endif; ?>

        <form action="" method="POST">
            <div class="mb-3">
                <label class="form-label">Nama Lengkap</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name="nama" class="form-control" placeholder="Nama Lengkap" required>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="nama@email.com" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>
            </div>
            <button type="submit" name="register" class="btn btn-primary w-100 mb-3">Daftar</button>
            <div class="text-center">
                <p class="mb-0">Sudah punya akun? <a href="login.php">Login di sini</a></p>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
