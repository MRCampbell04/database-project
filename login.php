<?php require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$_POST['username']]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'], $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid credentials.";
    }
}
require 'header.php';
?>
<h2>Login</h2>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="post">
  <div class="mb-3"><input name="username" class="form-control" placeholder="Username" required></div>
  <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
  <button class="btn btn-primary">Login</button>
</form>
<?php require 'footer.php'; ?>