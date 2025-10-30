<?php
session_start();

// 1. Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: user-mngmt/login.php");
    exit;
}

// 2. Include koneksi
require_once 'config/conn_db.php';

// 3. Ambil data session
$user_name = $_SESSION['user_name'];
$user_role = $_SESSION['user_role'];

// 4. Inisialisasi variabel
$error_message = '';
$name = '';
$description = '';
$price = '';
$product_id = 0;

// 5. Logika saat Form di-SUBMIT (POST) untuk menyimpan perubahan
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $product_id = $_POST['product_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    // Validasi
    if (empty($name) || empty($description) || empty($price)) {
        $error_message = "Semua field wajib diisi!";
    } elseif (!is_numeric($price) || $price < 0) {
        $error_message = "Harga harus berupa angka yang valid!";
    } else {
        // Siapkan query UPDATE
        $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
        // "ssdi" = string, string, double, integer
        $stmt->bind_param("ssdi", $name, $description, $price, $product_id);

        // Eksekusi
        if ($stmt->execute()) {
            // Berhasil, kembali ke daftar produk
            header("Location: products.php");
            exit;
        } else {
            $error_message = "Gagal memperbarui produk: " . $stmt->error;
        }
        $stmt->close();
    }

// 6. Logika saat Halaman di-LOAD (GET) untuk menampilkan data lama
} elseif (isset($_GET['id']) && !empty($_GET['id'])) {
    
    $product_id = $_GET['id'];
    
    // Ambil data produk dari database
    $stmt_select = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt_select->bind_param("i", $product_id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();

    if ($result->num_rows == 1) {
        $product = $result->fetch_assoc();
        // Masukkan data ke variabel untuk ditampilkan di form
        $name = $product['name'];
        $description = $product['description'];
        $price = $product['price'];
    } else {
        // Jika ID produk tidak ditemukan
        echo "Produk tidak ditemukan.";
        exit;
    }
    $stmt_select->close();

} else {
    // Jika halaman diakses tanpa ID
    echo "ID produk tidak valid.";
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Produk - Admin Gudang</title>
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
            <h2>Edit Produk</h2>
            <a href="products.php" class="btn btn-secondary">Kembali ke Daftar Produk</a>
        </div>
        <hr style="margin-top: 20px; margin-bottom: 20px;">

        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="product_edit.php" method="POST">
            
            <input type="hidden" name="product_id" value="<?php echo $product_id; ?>">
            
            <div class="form-group">
                <label for="name">Nama Produk:</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Deskripsi:</label>
                <textarea id="description" name="description" required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="form-group">
                <label for="price">Harga (Rp):</label>
                <input type="number" id="price" name="price" step="100" min="0" value="<?php echo htmlspecialchars($price); ?>" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
        </form>
    </div>
</body>
</html>