<?php
session_start();

// 1. Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: user-mngmt/login.php");
    exit;
}

// 2. Include koneksi database
require_once 'config/conn_db.php';

// 3. Cek apakah ID produk ada di URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    $product_id = $_GET['id'];
    
    // 4. Siapkan query DELETE
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id); // "i" = integer

    // 5. Eksekusi query
    if ($stmt->execute()) {
        // Berhasil dihapus, kembali ke daftar produk
        header("Location: products.php");
        exit;
    } else {
        // Gagal dihapus
        echo "Error: Gagal menghapus produk. " . $stmt->error;
    }
    
    $stmt->close();
    
} else {
    // Jika halaman diakses tanpa ID
    echo "ID produk tidak valid atau tidak ditemukan.";
}

$conn->close();
?>