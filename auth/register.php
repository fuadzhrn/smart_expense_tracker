<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'pages/dashboard.php');
    exit;
}

$errorMsg   = '';
$successMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $errorMsg = 'Semua field wajib diisi.';
    } else {
        $stmt = mysqli_prepare($koneksi, 'SELECT id FROM users WHERE email = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errorMsg = 'Email sudah terdaftar.';
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $insert = mysqli_prepare($koneksi, 'INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
            mysqli_stmt_bind_param($insert, 'sss', $name, $email, $hashedPassword);

            if (mysqli_stmt_execute($insert)) {
                $successMsg = 'Registrasi berhasil. Silakan login.';
            } else {
                $errorMsg = 'Registrasi gagal, silakan coba lagi.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Daftar | Smart Expense Tracker</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500;600;700;800&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="auth-logo">
            <span class="logo-icon"><i class="fa-solid fa-wallet"></i></span>
            <span class="logo-text">Smart Expense</span>
        </div>
        <p class="auth-subtitle">Buat akun baru untuk mulai mencatat keuangan</p>

        <?php if ($errorMsg): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($successMsg); ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="auth-form">
            <div class="form-group">
                <label for="name">Nama Lengkap</label>
                <input type="text" id="name" name="name" placeholder="Nama kamu" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="nama@email.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="********" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Daftar</button>
        </form>

        <p class="auth-footer">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>
</div>
</body>
</html>
