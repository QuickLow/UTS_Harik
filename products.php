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

// 4. Mengambil semua data produk dari database (Diurutkan berdasarkan ID menaik)
$result_products = $conn->query("SELECT * FROM products ORDER BY id ASC");

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Produk - Admin Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ecef; margin: 0; padding: 20px; }
        .navbar { background-color: #343a40; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-radius: 5px; margin-bottom: 20px; }
        .navbar a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; }
        .navbar a:hover, .navbar a.active { background-color: #007bff; }
        .navbar .user-info { margin-right: 15px; }
        .container { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; }
        .header-container { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        h2 { color: #333; }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-primary:hover { background-color: #0056b3; }
        .btn-warning { background-color: #ffc107; color: #212529; }
        .btn-danger { background-color: #dc3545; color: white; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table th, table td { border: 1px solid #dee2e6; padding: 12px; text-align: left; }
        table th { background-color: #f8f9fa; }
        table tr:nth-child(even) { background-color: #f2f2f2; }
        .action-links a { margin-right: 8px; }
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
        <div class="header-container">
            <h2>Manajemen Data Produk</h2>
            <a href="product_create.php" class="btn btn-primary">Tambah Produk Baru</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>No.</th> <th>Nama Produk</th>
                    <th>Deskripsi</th>
                    <th>Harga</th>
                    <th>Dibuat Pada</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                
                <?php $nomor = 1; // ?>
                
                <?php if ($result_products->num_rows > 0): ?>
                    <?php while($row = $result_products->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $nomor; ?></td> <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td>Rp <?php echo number_format($row['price'], 0, ',', '.'); ?></td>
                            <td><?php echo $row['created_at']; ?></td>
                            <td class="action-links">
                                <a href="product_edit.php?id=<?php echo $row['id']; ?>" class="btn btn-warning">Edit</a>
                                <a href="product_delete.php?id=<?php echo $row['id']; ?>" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">Hapus</a>
                            </td>
                        </tr>
                        
                    <?php $nomor++; // ?>
                    
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="text-align: center;">Belum ada data produk.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php
    // Tutup koneksi
    $conn->close();
    ?>
</body>
</html>