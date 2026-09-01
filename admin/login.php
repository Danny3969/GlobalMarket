<?php
require_once __DIR__ . '/auth.php';

$error = '';
if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    
    if (empty($user) || empty($pass)) {
        $error = 'Por favor ingresa usuario y contraseña.';
    } else if (verify_admin_login($user, $pass)) {
        $_SESSION['gm_admin_logged'] = true;
        $_SESSION['gm_admin_user'] = $user;
        $_SESSION['gm_last_activity'] = time();
        header('Location: index.php');
        exit;
    } else {
        $error = 'Credenciales incorrectas. Intenta nuevamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Acceso al Panel de Control | GlobalMarket GM</title>
  <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=3">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="stylesheet" href="css/admin.css?v=1">
</head>
<body class="login-body">

  <div class="login-container">
    <div class="login-card">
      <div class="login-header">
        <img src="../assets/images/logo.png?v=3" alt="GlobalMarket GM" class="login-logo">
        <h2>Consola de Administración</h2>
        <p>Gestión de contenidos, galerías de frutas y cotizaciones</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
          <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($error) ?>
        </div>
      <?php endif; ?>

      <?php if (isset($_GET['msg']) && $_GET['msg'] === 'logged_out'): ?>
        <div class="alert alert-success">
          <i class="fa-solid fa-circle-check"></i> Has cerrado sesión correctamente.
        </div>
      <?php endif; ?>

      <form method="POST" action="login.php" class="login-form">
        <div class="form-group">
          <label for="username"><i class="fa-solid fa-user"></i> Usuario</label>
          <input type="text" id="username" name="username" class="form-control" placeholder="admin" required autofocus>
        </div>

        <div class="form-group">
          <label for="password"><i class="fa-solid fa-lock"></i> Contraseña</label>
          <input type="password" id="password" name="password" class="form-control" placeholder="••••••••••••" required>
        </div>

        <button type="submit" class="btn btn-primary btn-block">
          <i class="fa-solid fa-right-to-bracket"></i> Iniciar Sesión
        </button>
      </form>

      <div class="login-footer">
        <a href="../index.html" class="back-link"><i class="fa-solid fa-arrow-left"></i> Volver al Sitio Web Principal</a>
        <p class="security-note"><i class="fa-solid fa-shield-halved"></i> Conexión Cifrada SSL • GlobalMarket GM</p>
      </div>
    </div>
  </div>

</body>
</html>
