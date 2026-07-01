<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/koneksi.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'pages/dashboard.php');
    exit;
}

$errorMsg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errorMsg = 'Email dan password wajib diisi.';
    } else {
        $stmt = mysqli_prepare($koneksi, 'SELECT id, name, password FROM users WHERE email = ? LIMIT 1');
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user   = $result ? mysqli_fetch_assoc($result) : null;

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: ' . BASE_URL . 'pages/dashboard.php');
            exit;
        } else {
            $errorMsg = 'Email atau password salah.';
        }
    }
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login | Smart Expense Tracker</title>
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
        <p class="auth-subtitle">Masuk untuk mengelola keuangan kamu</p>

        <?php if ($errorMsg): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($errorMsg); ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="auth-form">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="nama@email.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="********" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block">Login</button>
        </form>

        <p class="auth-footer">Belum punya akun? <a href="register.php">Daftar di sini</a></p>
    </div>
</div>
</body>
</html>
