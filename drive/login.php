<?php
require_once __DIR__ . '/auth.php';

$error = '';
if (is_drive_logged_in()) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Por favor ingrese usuario y contraseña.';
    } else {
        $res = verify_drive_login($username, $password);
        if ($res['success']) {
            header('Location: index.php');
            exit;
        } else {
            $error = $res['error'] ?? 'Credenciales no válidas.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Drive GlobalMarket | Portal de Archivos & Intranet</title>
  <link rel="icon" type="image/png" href="../assets/images/favicon.png?v=3">

  <!-- Google Fonts & FontAwesome -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <link rel="stylesheet" href="css/drive.css?v=3">
</head>
<body class="drive-login-body">

  <div class="drive-login-container">
    <div class="drive-login-card">
      
      <div class="login-header">
        <a href="../index.html">
          <img src="../assets/images/logo.png?v=3" alt="GlobalMarket GM" class="login-logo">
        </a>
        <h2>Drive GlobalMarket</h2>
        <p>Portal Privado de Almacenamiento & Documentación de Exportación</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="login-alert">
          <i class="fa-solid fa-triangle-exclamation"></i>
          <span><?= htmlspecialchars($error) ?></span>
        </div>
      <?php endif; ?>

      <form action="login.php" method="POST" class="drive-login-form">
        <div class="form-group">
          <label for="username"><i class="fa-solid fa-user"></i> Usuario o Correo Corporativo</label>
          <input type="text" id="username" name="username" class="form-control" required autofocus placeholder="Ej: admin o cliente">
        </div>

        <div class="form-group">
          <label for="password"><i class="fa-solid fa-lock"></i> Contraseña de Acceso</label>
          <input type="password" id="password" name="password" class="form-control" required placeholder="••••••••••••">
        </div>

        <button type="submit" class="btn btn-primary btn-block btn-lg" style="margin-top: 1.5rem;">
          <i class="fa-solid fa-cloud-arrow-up"></i> Iniciar Sesión en el Drive
        </button>
      </form>

      <div class="login-footer">
        <div class="footer-links">
          <a href="../index.html" class="back-link"><i class="fa-solid fa-arrow-left"></i> Volver a la Web Principal</a>
          <a href="https://globalmarket-gm.com/webmail" target="_blank" class="webmail-link"><i class="fa-solid fa-envelope"></i> Acceder a Webmail</a>
        </div>
        <p class="security-note"><i class="fa-solid fa-shield-halved"></i> Conexión Encriptada SSL/TLS • Acceso Restringido</p>
      </div>

    </div>
  </div>

</body>
</html>
