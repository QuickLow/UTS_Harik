<?php
session_start();

// 1. Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    // Jika belum, arahkan kembali ke halaman login
    header("Location: user-mngmt/login.php");
    exit;
}

// 2. MENGINCLUDE KONEKSI DATABASE (INI TAMBAHAN)
require_once 'config/conn_db.php';

// 3. Mengambil data pengguna dari session
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];

// ==========================================================
// 4. MENGAMBIL DATA STATISTIK (INI BAGIAN BARU)
// ==========================================================

// Query untuk Total Produk
$result_produk = $conn->query("SELECT COUNT(id) as total_produk FROM products");
$data_produk = $result_produk->fetch_assoc();
$total_produk = $data_produk['total_produk'];

// Query untuk Pengguna Aktif
$result_pengguna = $conn->query("SELECT COUNT(id) as total_pengguna FROM users WHERE status = 1");
$data_pengguna = $result_pengguna->fetch_assoc();
$total_pengguna = $data_pengguna['total_pengguna'];

// Catatan: "Produk Aktif"
// Tabel 'products' kita tidak punya kolom 'status'.
// Untuk saat ini, kita samakan "Produk Aktif" dengan "Total Produk".
// Jika nanti Anda menambah kolom status di produk, logikanya bisa diubah.
$produk_aktif = $total_produk; 

// 5. Menutup koneksi
$conn->close();
// ==========================================================

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ecef; margin: 0; padding: 20px; }
        .navbar { background-color: #343a40; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-radius: 5px; margin-bottom: 20px; }
        .navbar a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; }
        .navbar a:hover, .navbar a.active { background-color: #007bff; }
        .navbar .user-info { margin-right: 15px; }
        .container { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        h2 { color: #333; margin-bottom: 20px; }
        p { line-height: 1.6; }
        .dashboard-stats { display: flex; gap: 20px; margin-top: 30px; }
        .stat-card { background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; flex: 1; text-align: center; }
        .stat-card h3 { color: #007bff; margin-bottom: 10px; }
        .stat-card p { font-size: 1.5em; font-weight: bold; color: #495057; }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="dashboard.php" class="active">Dashboard</a>
            <a href="products.php">Kelola Produk</a> 
            <a href="user-mngmt/profile.php">Profil Saya</a> 
        </div>
        <div>
            <span class="user-info">Halo, <?php echo htmlspecialchars($user_name); ?> 
            <a href="user-mngmt/logout.php">Logout</a> 
        </div>
    </div>

    <div class="container">
        <h2>Selamat Datang di Dashboard Admin Gudang!</h2>
        <p>Ini adalah ruang kerja Anda untuk mengelola data produk dan profil pengguna.</p>
        

        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Produk</h3>
                <p><?php echo $total_produk; ?></p> 
            </div>
            <div class="stat-card">
                <h3>Produk Aktif</h3>
                <p><?php echo $produk_aktif; ?></p> 
            </div>
            <div class="stat-card">
                <h3>Pengguna Aktif</h3>
                <p><?php echo $total_pengguna; ?></p> 
            </div>
        </div>
        
        <p style="margin-top: 40px;">Gunakan menu di atas untuk navigasi.</p>
    </div>
</body>
</html>