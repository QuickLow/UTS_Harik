<?php
session_start();

// 1. Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: user-mngmt/login.php");
    exit;
}

// 2. Include koneksi database
require_once 'config/conn_db.php';

// 3. Mengambil data pengguna dari session
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// 4. Variabel untuk pesan
$error_message = '';
$success_message = '';

// 5. Cek apakah form sudah di-submit (method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 6. Ambil data dari form
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // 7. Validasi sederhana
    if (empty($name) || empty($description) || empty($price)) {
        $error_message = "Semua field wajib diisi!";
    } elseif (!is_numeric($price) || $price < 0) {
        $error_message = "Harga harus berupa angka yang valid!";
    } else {
        // 8. Siapkan query INSERT
        $stmt = $conn->prepare("INSERT INTO products (name, description, price) VALUES (?, ?, ?)");
        // "ssd" = string, string, double
        $stmt->bind_param("ssd", $name, $description, $price);

        // 9. Eksekusi query
        if ($stmt->execute()) {
            // Jika berhasil, arahkan kembali ke halaman products.php
            header("Location: products.php");
            exit;
        } else {
            $error_message = "Gagal menambahkan produk ke database: " . $stmt->error;
        }
        
        $stmt->close();
    }
    
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Produk - Admin Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ecef; margin: 0; padding: 20px; }
        .navbar { background-color: #343a40; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-radius: 5px; margin-bottom: 20px; }
        .navbar a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; }
        .navbar a:hover, .navbar a.active { background-color: #007bff; }
        .navbar .user-info { margin-right: 15px; }
        .container { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; max-width: 700px; margin: auto; }
        h2 { color: #333; }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-secondary { background-color: #6c757d; color: white; }
        .btn-secondary:hover { background-color: #5a6268; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input, .form-group textarea { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .form-group textarea { min-height: 100px; resize: vertical; }
        
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="dashboard.php">Dashboard</a>
            <a href="products.php" class="active">Kelola Produk</a>
            <a href="user-mngmt/profile.php">Profil Saya</a>
        </div>
        <div>
            <span class="user-info">Halo, <?php echo htmlspecialchars($user_name); ?> (<?php echo htmlspecialchars($user_role); ?>)</span>
            <a href="user-mngmt/logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h2>Tambah Produk Baru</h2>
            <a href="products.php" class="btn btn-secondary">Kembali ke Daftar Produk</a>
        </div>
        <hr style="margin-top: 20px; margin-bottom: 20px;">

        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="product_create.php" method="POST">
            <div class="form-group">
                <label for="name">Nama Produk:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" required></textarea>
            </div>
            <div class="form-group">
                <label for="price">Harga (Rp):</label>
                <input type="number" id="price" name="price" step="100" min="0" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan Produk</button>
        </form>
    </div>
</body>
</html>