<?php
session_start();

// 1. Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Arahkan ke login.php di folder yang sama
    exit;
}

// 2. Include koneksi
require_once '../config/conn_db.php'; // Path '../' karena file ini di dalam user-mngmt

// 3. Ambil data session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];

// 4. Inisialisasi variabel pesan
$success_message = '';
$error_message = '';

// 5. Logika saat Form Ganti Password di-SUBMIT (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Ambil data dari form
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validasi
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = "Semua field password wajib diisi!";
    } elseif ($new_password !== $confirm_password) {
        $error_message = "Password baru dan konfirmasi password tidak cocok!";
    } elseif (strlen($new_password) < 6) {
        $error_message = "Password baru minimal harus 6 karakter!";
    } else {
        // Ambil password HASHED yang sekarang dari database
        $stmt_get = $conn->prepare("SELECT password FROM users WHERE id = ?");
        $stmt_get->bind_param("i", $user_id);
        $stmt_get->execute();
        $result = $stmt_get->get_result();
        $user = $result->fetch_assoc();
        $hashed_password_db = $user['password'];
        $stmt_get->close();

        // Verifikasi password lama
        if (password_verify($current_password, $hashed_password_db)) {
            // Password lama benar.
            // Hash password baru
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password baru ke database
            $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt_update->bind_param("si", $new_hashed_password, $user_id);
            
            if ($stmt_update->execute()) {
                $success_message = "Password Anda telah berhasil diperbarui!";
            } else {
                $error_message = "Gagal memperbarui password. Silakan coba lagi.";
            }
            $stmt_update->close();
        } else {
            // Password lama salah
            $error_message = "Password lama yang Anda masukkan salah!";
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - Admin Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #e9ecef; margin: 0; padding: 20px; }
        .navbar { background-color: #343a40; color: white; padding: 15px 20px; display: flex; justify-content: space-between; align-items: center; border-radius: 5px; margin-bottom: 20px; }
        .navbar a { color: white; text-decoration: none; padding: 8px 15px; border-radius: 4px; }
        /* Perhatikan 'a.active' untuk menandai halaman aktif */
        .navbar a:hover, .navbar a.active { background-color: #007bff; }
        .navbar .user-info { margin-right: 15px; }
        .container { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; max-width: 600px; margin: auto; }
        h2, h3 { color: #333; }
        .btn { padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; font-size: 14px; background-color: #007bff; color: white; }
        .btn:hover { background-color: #0056b3; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        
        .profile-info { background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; margin-bottom: 30px; }
        .profile-info p { margin: 10px 0; font-size: 1.1em; }
        .profile-info strong { color: #495057; }
    </style>
</head>
<body>
    <div class="navbar">
        <div>
            <a href="../dashboard.php">Dashboard</a>
            <a href="../products.php">Kelola Produk</a>
            <a href="profile.php" class="active">Profil Saya</a>
        </div>
        <div>
            <span class="user-info">Halo, <?php echo htmlspecialchars($user_name); ?> 
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="container">
        <h2>Profil Pengguna</h2>

        <div class="profile-info">
            <p><strong>Nama:</strong> <?php echo htmlspecialchars($user_name); ?></p>
            <p><strong>Email (Username):</strong> <?php echo htmlspecialchars($user_email); ?></p>
            <p><strong>Role:</strong> <?php echo htmlspecialchars($user_role); ?></p>
        </div>

        <hr style="margin-bottom: 30px;">

        <h3>Ubah Password</h3>
        
        <?php if (!empty($success_message)): ?>
            <div class="message success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo $error_message; ?></div>
        <?php endif; ?>

        <form action="profile.php" method="POST">
            <div class="form-group">
                <label for="current_password">Password Lama:</label>
                <input type="password" id="current_password" name="current_password" required>
            </div>
            <div class="form-group">
                <label for="new_password">Password Baru:</label>
                <input type="password" id="new_password" name="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Konfirmasi Password Baru:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">Perbarui Password</button>
        </form>
    </div>
</body>
</html>