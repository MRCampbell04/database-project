<?php require 'config.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $hash = password_hash($password, PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    try {
        $stmt->execute([$username, $email, $hash]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit;
    } catch (PDOException $e) {
        $error = "Username or email already exists.";
    }
}
require 'header.php';
?>
<h2>Register</h2>
<?php if (!empty($error)): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
<form method="post">
  <div class="mb-3"><input name="username" class="form-control" placeholder="Username" required></div>
  <div class="mb-3"><input name="email" type="email" class="form-control" placeholder="Email" required></div>
  <div class="mb-3"><input name="password" type="password" class="form-control" placeholder="Password" required></div>
  <button class="btn btn-primary">Register</button>
</form>
<?php require 'footer.php'; ?>