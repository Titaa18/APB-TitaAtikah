<?php
require 'koneksi.php';

echo "<h2>Starting Database Setup...</h2>";

$sql = "
CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS buku (
    id_buku INT AUTO_INCREMENT PRIMARY KEY,
    judul VARCHAR(150) NOT NULL,
    pengarang VARCHAR(100) NOT NULL,
    penerbit VARCHAR(100) NOT NULL,
    tahun YEAR NOT NULL,
    stok INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_user INT NOT NULL,
    id_buku INT NOT NULL,
    tanggal_pinjam DATE NOT NULL,
    tanggal_kembali DATE DEFAULT NULL,
    status ENUM('dipinjam', 'dikembalikan') DEFAULT 'dipinjam',
    FOREIGN KEY (id_user) REFERENCES users(id_user) ON DELETE CASCADE,
    FOREIGN KEY (id_buku) REFERENCES buku(id_buku) ON DELETE CASCADE
);
";

// Execute multi-query
if (mysqli_multi_query($conn, $sql)) {
    do {
        // Store first result set
        if ($result = mysqli_store_result($conn)) {
            mysqli_free_result($result);
        }
        if (mysqli_more_results($conn)) {
            echo "Successfully ran a part of the migration...<br>";
        }
    } while (mysqli_next_result($conn));
    
    echo "<h3 style='color:green;'>Tables created successfully!</h3>";
    
    // Check if admin exists
    $check = mysqli_query($conn, "SELECT * FROM users WHERE email = 'admin@gmail.com'");
    if (mysqli_num_rows($check) == 0) {
        $pass = md5('admin123');
        mysqli_query($conn, "INSERT INTO users (nama, email, password, role) VALUES ('Administrator', 'admin@gmail.com', '$pass', 'admin')");
        echo "Admin default added (admin@gmail.com / admin123).<br>";
    }
} else {
    echo "<h3 style='color:red;'>Error creating tables: " . mysqli_error($conn) . "</h3>";
}

echo "<br><a href='index.php'>Go to Login</a>";
?>
