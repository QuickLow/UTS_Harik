<?php
// 1. Memulai session
session_start();

// 2. Jika user sudah login, arahkan langsung ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard.php"); // Arahkan ke dashboard.php di folder UTS (root proyek)
    exit;
}

// 3. Menginclude file koneksi database
require_once '../config/conn_db.php';

// 4. Variabel untuk menyimpan pesan
$error = '';

// 5. Cek apakah form sudah di-submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 6. Ambil data dari form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 7. Validasi data
    if (empty($email) || empty($password)) {
        $error = "Email dan password wajib diisi!";
    } else {
        // 8. Cari pengguna berdasarkan email
        $stmt = $conn->prepare("SELECT id, name, email, password, status, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // 9. Verifikasi password
            if (password_verify($password, $user['password'])) {
                
                // 10. Cek status aktivasi akun
                if ($user['status'] == 1) {
                    // Login berhasil!
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['user_role'] = $user['role']; // Simpan role juga

                    // Arahkan ke halaman dashboard
                    header("Location: ../dashboard.php"); 
                    exit;

                } else {
                    $error = "Akun Anda belum diaktifkan. Silakan cek email Anda untuk link aktivasi.";
                }
            } else {
                $error = "Email atau password salah.";
            }
        } else {
            $error = "Email atau password salah.";
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
    <title>Login Admin Gudang</title>
    <style>
        body { font-family: Arial, sans-serif; display: grid; place-items: center; min-height: 90vh; background-color: #f4f4f4; }
        .container { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; width: 400px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { background-color: #007bff; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        .btn:hover { background-color: #0056b3; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .link-forgot { text-align: right; margin-top: -10px; margin-bottom: 15px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Login Admin Gudang</h2>

        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="link-forgot">
                <a href="forgot_password.php">Lupa Password?</a> 
                </div>
            <button type="submit" class="btn">Login</button>
        </form>
        <p style="text-align: center; margin-top: 20px;">
            Belum punya akun? <a href="create_account.php">Daftar sekarang</a>
        </p>
    </div>
</body>
</html>