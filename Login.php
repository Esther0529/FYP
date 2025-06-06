<?php
session_start();
include_once (file_exists('includes/db.php') ? 'includes/db.php' : 'db.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = mysqli_prepare($conn, "SELECT id, username, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['username'];
        $_SESSION['user_email'] = $email;
        header('Location: Home.php');
        exit;
    } else {
        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login - Kulai Sport</title>
    <link rel="stylesheet" href="style.css">
    <style>
    .password-toggle-btn {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.1rem;
        color: #888;
        padding: 0 6px;
    }
    .password-input-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        align-items: center;
    }
    </style>
</head>
<body>
    <main style="max-width:400px;margin:60px auto;padding:32px;background:#fff;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <h2 style="text-align:center;">Login</h2>
        <?php if (!empty($error)): ?>
            <div style="color:red;text-align:center;margin-bottom:12px;"> <?= htmlspecialchars($error) ?> </div>
        <?php endif; ?>
        <form method="post" style="display:flex;flex-direction:column;gap:18px;">
            <input type="email" name="email" placeholder="Email" required style="padding:10px;font-size:1rem;">
            <div class="password-input-wrapper">
                <input type="password" name="password" id="password" placeholder="Password" required style="padding:10px;font-size:1rem;width:100%;">
                <button type="button" class="password-toggle-btn" tabindex="-1" onclick="togglePassword()">
                    <span id="toggle-icon">👁️</span>
                </button>
            </div>
            <button type="submit" style="padding:12px;background:#222;color:#fff;font-size:1.1rem;border:none;border-radius:4px;cursor:pointer;">Login</button>
        </form>
        <div style="text-align:center;margin-top:10px;">
            <a href="ForgotPassword.php" style="color:#1565c0;text-decoration:underline;font-size:0.98rem;">Forgot password?</a>
        </div>
        <p style="text-align:center;margin-top:18px;">Don't have an account? <a href="Register.php">Register here</a></p>
    </main>
    <script>
    function togglePassword() {
        var pwd = document.getElementById('password');
        var icon = document.getElementById('toggle-icon');
        if (pwd.type === 'password') {
            pwd.type = 'text';
            icon.textContent = '🙈';
        } else {
            pwd.type = 'password';
            icon.textContent = '👁️';
        }
    }
    </script>
</body>
</html> 