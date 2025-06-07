<?php
session_start();
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = $_POST['senha'];

    $stmt = $conexao->prepare("SELECT * FROM usuarios WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if ($user && password_verify($senha, $user['senha'])) {
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
        $_SESSION['usuario_email'] = $user['email'];
        header('Location: index.php');
        exit;
    } else {
        $erro = "E-mail ou senha inválidos.";
    }
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:400,500,700&display=swap">
  <style>
    :root {
      --green: #27ae60;
      --green-dark: #22984f;
      --card-radius: 26px;
      --shadow: 0 8px 40px rgba(39,174,96,.09), 0 1.5px 4px rgba(39,174,96,0.14);
    }
    body {
      min-height: 100vh;
      background: linear-gradient(120deg, #f2fff9 0%, #d4f8e8 40%, #fafdff 100%);
      font-family: 'Inter', Arial, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-section {
      min-height: 100vh;
      width: 100vw;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .login-card {
      width: 410px;
      max-width: 96vw;
      background: #fff;
      border-radius: var(--card-radius);
      box-shadow: var(--shadow);
      padding: 3.2rem 2.2rem 2.6rem 2.2rem;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
      overflow: hidden;
      animation: fadeInUp 0.7s;
    }
    .login-illus {
      margin-bottom: 16px;
      margin-top: -18px;
    }
    .login-illus svg {
      width: 88px;
      height: 88px;
      display: block;
      margin: auto;
    }
    .login-title {
      color: var(--green-dark);
      font-weight: 800;
      font-size: 1.65rem;
      letter-spacing: 0.5px;
      margin-bottom: 18px;
      text-align: center;
    }
    .login-card form {
      width: 100%;
      margin-top: 10px;
    }
    .login-card .form-label {
      color: #444;
      font-weight: 600;
      font-size: 1.09em;
      margin-bottom: 0.15em;
      letter-spacing: 0.4px;
    }
    .login-card .form-control {
      margin-bottom: 22px;
      border-radius: 10px;
      font-size: 1.09rem;
      padding: 1.05rem 1.15rem;
      border: 1.3px solid #e0e4ea;
      background: #fafdff;
      color: #232323;
      transition: border-color 0.18s, box-shadow 0.13s;
    }
    .login-card .form-control:focus {
      border-color: var(--green);
      box-shadow: 0 0 0 2.5px rgba(39,174,96,.12);
      background: #fff;
    }
    .login-card .btn-success {
      width: 100%;
      font-size: 1.19rem;
      padding: 0.96rem 0;
      border-radius: 12px;
      font-weight: 700;
      letter-spacing: 1px;
      position: relative;
      overflow: hidden;
      box-shadow: 0 2px 10px rgba(39,174,96,0.09);
      border: none;
      background: linear-gradient(90deg, var(--green) 0%, var(--green-dark) 100%);
      color: #fff;
      transition: background 0.17s, color 0.17s, transform .11s;
      margin-bottom: 10px;
    }
    .login-card .btn-success:hover,
    .login-card .btn-success:focus {
      background: linear-gradient(100deg, #22984f 0%, #27ae60 100%);
      color: #fff;
      transform: scale(1.021);
    }
    .alert-login {
      width: 100%;
      margin-bottom: 18px;
      padding: 1em 1.25em;
      border-radius: 9px;
      background: #ebfaef;
      color: #22984f;
      font-weight: 700;
      border: 1.5px solid #c2f7d5;
      font-size: 1.06em;
      letter-spacing: .6px;
      text-align: center;
    }
    .login-extra {
      margin-top: 14px;
      width: 100%;
      display: flex;
      justify-content: space-between;
      font-size: .98em;
      color: #6f9280;
      opacity: 0.86;
    }
    .login-extra a {
      color: var(--green);
      font-weight: 600;
      text-decoration: none;
      transition: color 0.17s;
    }
    .login-extra a:hover {
      color: #19a95a;
      text-decoration: underline;
    }
    .login-footer {
      margin-top: 22px;
      font-size: 0.97em;
      color: #a8b9ac;
      text-align: center;
      opacity: 0.72;
    }
    @media (max-width: 480px) {
      .login-card { padding: 1.2rem 0.45rem 1.2rem 0.45rem; }
      .login-title { font-size: 1.12rem; }
      .login-illus svg { width: 66px; height: 66px; }
    }
    /* Anim. fade-in do card */
    @keyframes fadeInUp {
      0% { opacity: 0; transform: translateY(38px);}
      100% { opacity: 1; transform: translateY(0);}
    }
    /* Efeito ripple */
    .ripple-effect {
      position: absolute;
      border-radius: 50%;
      background: rgba(39, 174, 96, 0.14);
      pointer-events: none;
      animation: ripple-animation .5s linear;
      z-index: 2;
    }
    @keyframes ripple-animation {
      from { transform: scale(0.4); opacity: 0.7;}
      to   { transform: scale(2.3); opacity: 0;}
    }
  </style>
</head>
<body>
  <section class="login-section">
    <div class="login-card shadow-lg">
      <div class="login-illus">
        <!-- SVG ILUSTRAÇÃO MODERNA - pode trocar por um logo seu se quiser -->
        <svg fill="none" viewBox="0 0 80 80"><ellipse fill="#e9f9f1" rx="40" ry="40" cx="40" cy="40"/><rect x="25" y="35" width="30" height="27" rx="6" fill="#27ae60"/><rect x="33" y="30" width="14" height="14" rx="7" fill="#b3efd0"/><ellipse cx="40" cy="62" rx="5" ry="2.2" fill="#a8d2bd"/></svg>
      </div>
      <div class="login-title">Bem-vindo de volta!</div>
      <?php if (!empty($erro)): ?>
        <div class="alert-login"><?= $erro ?></div>
      <?php endif; ?>
      <form method="POST" autocomplete="off">
        <label for="email" class="form-label">E-mail</label>
        <input type="email" name="email" id="email" class="form-control" required autofocus>

        <label for="senha" class="form-label">Senha</label>
        <input type="password" name="senha" id="senha" class="form-control" required>

        <button type="submit" class="btn btn-success ripple">Entrar</button>
      </form>
      <div class="login-extra">
        <a href="#">Esqueci a senha</a>
        <a href="#">Criar conta</a>
      </div>
      <div class="login-footer">
        © <?= date('Y') ?> — design by Gabriel 😊
      </div>
    </div>
  </section>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Efeito ripple no botão
    document.querySelectorAll('.btn.ripple').forEach(btn => {
      btn.addEventListener('click', function (e) {
        const circle = document.createElement('span');
        circle.className = 'ripple-effect';
        const rect = btn.getBoundingClientRect();
        circle.style.width = circle.style.height = Math.max(rect.width, rect.height) + 'px';
        circle.style.left = e.clientX - rect.left - (rect.width/2) + 'px';
        circle.style.top = e.clientY - rect.top - (rect.height/2) + 'px';
        btn.appendChild(circle);
        setTimeout(() => circle.remove(), 500);
      });
    });
  </script>
</body>
</html>
