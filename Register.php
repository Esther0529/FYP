<?php
session_start();
include_once (file_exists('includes/db.php') ? 'includes/db.php' : 'db.php');
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($username && $email && $password) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $error = 'Email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, 'sss', $username, $email, $hash);
            if (mysqli_stmt_execute($stmt)) {
                header('Location: Login.php');
                exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Kulai Sport</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <main style="max-width:400px;margin:60px auto;padding:32px;background:#fff;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,0.08);">
        <h2 style="text-align:center;">Register</h2>
        <?php if (!empty($error)): ?>
            <div style="color:red;text-align:center;margin-bottom:12px;"> <?= htmlspecialchars($error) ?> </div>
        <?php endif; ?>
        <form method="post" style="display:flex;flex-direction:column;gap:18px;">
            <input type="text" name="name" placeholder="Full Name" required style="padding:10px;font-size:1rem;">
            <input type="email" name="email" placeholder="Email" required style="padding:10px;font-size:1rem;">
            <input type="password" name="password" placeholder="Password" required style="padding:10px;font-size:1rem;">
            <button type="submit" style="padding:12px;background:#222;color:#fff;font-size:1.1rem;border:none;border-radius:4px;cursor:pointer;">Register</button>
        </form>
        <p style="text-align:center;margin-top:18px;">Already have an account? <a href="Login.php">Login here</a></p>
    </main>
    <?php include 'Footer.php'; ?>
</body>
</html> 