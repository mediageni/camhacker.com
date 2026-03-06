<?php
session_start();
require_once __DIR__ . '/../includes/config.php';

// Already logged in? Redirect to dashboard
if (!empty($_SESSION['admin_logged_in'])) {
    header('Location: /admin/');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if ($username === ADMIN_USER && hash_equals(ADMIN_PASS, $password)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = $username;
        $_SESSION['admin_login_time'] = time();
        header('Location: /admin/');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex, nofollow">
<title>Admin Login - CamHacker</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<style>
  body {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  }
  .login-card {
    width: 100%;
    max-width: 420px;
    border: 1px solid rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
  }
  .login-logo {
    font-size: 3rem;
    color: #ff6300;
  }
</style>
</head>
<body>

<div class="card login-card border-0 shadow-lg">
  <div class="card-body p-5">
    <div class="text-center mb-4">
      <i class="bi bi-webcam login-logo"></i>
      <h3 class="fw-bold mt-2">CamHacker Admin</h3>
      <p class="text-body-secondary">Sign in to manage webcams</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger d-flex align-items-center gap-2 py-2" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i>
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST" action="/admin/login.php">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" id="username" name="username" class="form-control" placeholder="Username" required autofocus>
        </div>
      </div>

      <div class="mb-4">
        <label for="password" class="form-label">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" id="password" name="password" class="form-control" placeholder="Password" required>
        </div>
      </div>

      <button type="submit" class="btn btn-warning w-100 fw-semibold py-2">
        <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
      </button>
    </form>

    <div class="text-center mt-4">
      <a href="/" class="text-body-secondary text-decoration-none small"><i class="bi bi-arrow-left me-1"></i>Back to site</a>
    </div>
  </div>
</div>

</body>
</html>
