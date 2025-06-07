<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
  header('Location: login.php');
  exit;
}
require 'conexao.php';

// Pega o ID pela URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    $_SESSION['mensagem'] = "ID inválido!";
    header("Location: index.php");
    exit;
}

// Busca dados do usuário
$sql = "SELECT * FROM usuarios WHERE id = $id LIMIT 1";
$result = mysqli_query($conexao, $sql);
$usuario = mysqli_fetch_assoc($result);

if (!$usuario) {
    $_SESSION['mensagem'] = "Usuário não encontrado!";
    header("Location: index.php");
    exit;
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Usuário</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:400,500,700&display=swap">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<div id="loader" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:#ffffffcc;z-index:99999;align-items:center;justify-content:center;">
  <div style="width:64px;height:64px;border-radius:50%;border:7px solid #c5f8e1;border-top:7px solid #21d89a;animation:spin 1.2s linear infinite"></div>
</div>

<?php include('navbar.php'); ?>
<div class="container mt-4">
  <?php include('mensagem.php'); ?>
  <div class="card shadow mb-4 fade-in-up">
    <div class="card-header">
      <h4 class="mb-0"><i class="bi bi-pencil-square"></i> Editar Usuário</h4>
    </div>
    <div class="card-body">
      <form method="POST" action="acoes.php" autocomplete="off" class="row g-3">
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">
        <div class="col-md-6">
          <label for="nome" class="form-label">Nome <span class="text-danger">*</span></label>
          <input type="text" name="nome" id="nome" class="form-control" maxlength="120" value="<?= htmlspecialchars($usuario['nome']) ?>" required>
        </div>
        <div class="col-md-6">
          <label for="email" class="form-label">E-mail <span class="text-danger">*</span></label>
          <input type="email" name="email" id="email" class="form-control" maxlength="120" value="<?= htmlspecialchars($usuario['email']) ?>" required>
        </div>
        <div class="col-md-4">
          <label for="data_nascimento" class="form-label">Data de Nascimento <span class="text-danger">*</span></label>
          <input type="date" name="data_nascimento" id="data_nascimento" class="form-control" value="<?= $usuario['data_nascimento'] ?>" required>
        </div>
        <div class="col-md-4">
          <label for="status" class="form-label">Status</label>
          <select name="status" id="status" class="form-select">
            <option value="ativo" <?= ($usuario['status'] == 'ativo') ? 'selected' : '' ?>>Ativo</option>
            <option value="inativo" <?= ($usuario['status'] == 'inativo') ? 'selected' : '' ?>>Inativo</option>
          </select>
        </div>
        <div class="col-md-4">
          <label for="senha" class="form-label">Nova Senha <small class="text-muted">(deixe em branco para não alterar)</small></label>
          <input type="password" name="senha" id="senha" class="form-control" minlength="6">
        </div>
        <div class="col-12 mt-3">
          <button type="submit" name="editar_usuario" class="btn btn-primary ripple">
            <i class="bi bi-check-circle"></i> Salvar alterações
          </button>
          <a href="index.php" class="btn btn-outline-dark ripple ms-2">
            <i class="bi bi-arrow-left"></i> Voltar
          </a>
        </div>
      </form>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Fade-in animado no card de edição
document.querySelectorAll('.card').forEach((el, idx) => {
  el.classList.add('fade-in-up');
  el.style.animationDelay = (0.10 + idx * 0.06) + "s";
});
// Tooltips Bootstrap
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
  return new bootstrap.Tooltip(tooltipTriggerEl)
});
// Efeito Ripple nos botões com .ripple
document.querySelectorAll('.btn.ripple').forEach(btn => {
  btn.addEventListener('click', function (e) {
    const circle = document.createElement('span');
    circle.className = 'ripple-effect';
    const rect = btn.getBoundingClientRect();
    circle.style.width = circle.style.height = Math.max(rect.width, rect.height) + 'px';
    circle.style.left = e.clientX - rect.left - (rect.width/2) + 'px';
    circle.style.top = e.clientY - rect.top - (rect.height/2) + 'px';
    btn.appendChild(circle);
    setTimeout(() => circle.remove(), 520);
  });
});
// Alertas/flash animados somem sozinhos
setTimeout(() => {
  document.querySelectorAll('.alert, .mensagem-flash').forEach(el => {
    el.style.transition = "opacity .6s, transform .6s";
    el.style.opacity = 0;
    el.style.transform = "translateY(-28px) scale(.96)";
    setTimeout(()=>el.remove(), 600);
  });
}, 3400);
// Loader ao submeter formulário
document.querySelectorAll("form").forEach(form => {
  form.addEventListener("submit", ()=> {
    var loader = document.getElementById("loader");
    if(loader) loader.style.display = "flex";
  });
});
window.addEventListener('pageshow', () => {
  var loader = document.getElementById("loader");
  if(loader) loader.style.display = "none";
});
</script>
</body>
</html>
