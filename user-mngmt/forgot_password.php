<?php
// 1. Menggunakan kelas-kelas PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// 1b. Menginclude file PHPMailer
require '../PHPMailer/Exception.php';
require '../PHPMailer/PHPMailer.php';
require '../PHPMailer/SMTP.php';

// 2. Menginclude file koneksi database
require_once '../config/conn_db.php';

// 3. Variabel untuk menyimpan pesan
$message = '';
$error = '';
$form_processed = false;

// 4. Cek apakah form sudah di-submit (method POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST['email'];

    if (empty($email)) {
        $error = "Field email wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid!";
    } else {
        
        // 5. Cek apakah email ada di database DAN sudah aktif (status = 1)
        $stmt_check = $conn->prepare("SELECT id, name FROM users WHERE email = ? AND status = 1");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $result = $stmt_check->get_result();

        if ($result->num_rows == 0) {
            // Email tidak ditemukan atau belum aktif.
            // CATATAN KEAMANAN: Kita tetap tampilkan pesan sukses
            // agar orang lain tidak bisa menebak-nebak email yang terdaftar.
            $message = "Jika email Anda terdaftar dan aktif, link reset password telah dikirim.";
            $form_processed = true;
        } else {
            // 6. Email ditemukan dan aktif. Lanjutkan proses reset.
            $user = $result->fetch_assoc();
            $user_name = $user['name'];

            // 7. Buat token reset unik
            $reset_token = bin2hex(random_bytes(32));
            
            // 8. Tentukan waktu kadaluarsa token (misal: 1 jam dari sekarang)
            // 'time() + 3600' berarti 3600 detik = 1 jam
            $expires = date("Y-m-d H:i:s", time() + 3600); 

            // 9. Simpan token dan waktu kadaluarsa ke database
            $stmt_update = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE email = ?");
            $stmt_update->bind_param("sss", $reset_token, $expires, $email);
            
            if ($stmt_update->execute()) {
                // 10. Buat link reset
                $reset_link = "http://localhost/UTS/user-mngmt/reset_password.php?token=" . $reset_token;

                // MULAI KODE PENGIRIMAN EMAIL (PHPMailer)
                
                $mail = new PHPMailer(true);

                try {
                    // Server settings
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    
                    // !! GANTI BAGIAN INI DENGAN DATA 
                    $mail->Username   = 'harikkurniawan6072@gmail.com'; // GANTI DENGAN EMAIL GMAIL ANDA
                    $mail->Password   = 'mrli gtfi tegn iylb';    // GANTI DENGAN APP PASSWORD ANDA
                    

                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                    $mail->Port       = 465;

                    // Recipients
                    // Ganti 'email_anda@gmail.com' di bawah ini dengan email yang sama dengan $mail->Username
                    $mail->setFrom('harikkurniawan6072@gmail.com', 'Admin SETRUM'); // Email dan Nama Pengirim
                    $mail->addAddress($email, $user_name);     // Email Penerima (dari form)

                    // Content
                    $mail->isHTML(true);
                    $mail->Subject = 'Reset Password Akun SETRUM Anda';
                    $mail->Body    = "Halo $user_name,<br><br>Kami menerima permintaan untuk mereset password Anda. Klik link di bawah ini untuk melanjutkan:<br><br><a href='$reset_link'>Reset Password Saya</a><br><br>Link ini akan kadaluarsa dalam 1 jam.<br><br>Jika Anda tidak merasa meminta ini, abaikan email ini.<br><br>Salam,<br>Tim SETRUM";
                    $mail->AltBody = "Halo $user_name, Silakan salin dan tempel link ini di browser Anda untuk mereset password: $reset_link";

                    $mail->send();
                    
                    $message = "Jika email Anda terdaftar dan aktif, link reset password telah dikirim.";
                    $form_processed = true;
                    
                } catch (Exception $e) {
                    $error = "Email gagal dikirim. Hubungi admin. Mailer Error: {$mail->ErrorInfo}";
                }
                // SELESAI KODE PENGIRIMAN EMAIL
                
            } else {
                $error = "Gagal memperbarui token. Silakan coba lagi.";
            }
            $stmt_update->close();
        }
        $stmt_check->close();
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password</title>
    <style>
        body { font-family: Arial, sans-serif; display: grid; place-items: center; min-height: 90vh; background-color: #f4f4f4; }
        .container { background: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 30px; width: 400px; }
        h2 { text-align: center; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: bold; }
        .form-group input { width: 100%; padding: 10px; box-sizing: border-box; border: 1px solid #ccc; border-radius: 4px; }
        .btn { background-color: #dc3545; color: white; padding: 12px; border: none; border-radius: 4px; cursor: pointer; width: 100%; font-size: 16px; }
        .btn:hover { background-color: #c82333; }
        .message { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .login-link { text-align: center; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Lupa Password</h2>

        <?php if (!empty($message)): ?>
            <div class="message success"><?php echo $message; ?></div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="message error"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php 
        // Jika form belum diproses, tampilkan form.
        // Jika sudah, sembunyikan.
        if ($form_processed == false): 
        ?>
            <form action="forgot_password.php" method="POST">
                <div class="form-group">
                    <label for="email">Email Terdaftar:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn">Kirim Link Reset</button>
            </form>
        <?php endif; ?>

        <div class="login-link">
            <a href="login.php">Kembali ke Login</a>
        </div>
    </div>
</body>
</html>