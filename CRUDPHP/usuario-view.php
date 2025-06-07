<?php
session_start();
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
  <title>Detalhes do Usuário</title>
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
    <div class="card-header d-flex align-items-center">
      <h4 class="mb-0 flex-grow-1"><i class="bi bi-person-badge"></i> Detalhes do Usuário</h4>
      <a href="usuario-edit.php?id=<?= $usuario['id'] ?>" class="btn btn-success btn-sm ms-2 ripple" title="Editar" data-bs-toggle="tooltip">
        <i class="bi bi-pencil-fill"></i> Editar
      </a>
      <a href="index.php" class="btn btn-outline-dark btn-sm ms-2 ripple" title="Voltar para lista" data-bs-toggle="tooltip">
        <i class="bi bi-arrow-left"></i> Voltar
      </a>
    </div>
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-md-3 mb-2">ID</dt>
        <dd class="col-md-9 mb-2"><?= $usuario['id'] ?></dd>
        <dt class="col-md-3 mb-2">Nome</dt>
        <dd class="col-md-9 mb-2"><?= htmlspecialchars($usuario['nome']) ?></dd>
        <dt class="col-md-3 mb-2">E-mail</dt>
        <dd class="col-md-9 mb-2"><?= htmlspecialchars($usuario['email']) ?></dd>
        <dt class="col-md-3 mb-2">Data de Nascimento</dt>
        <dd class="col-md-9 mb-2"><?= date('d/m/Y', strtotime($usuario['data_nascimento'])) ?></dd>
        <dt class="col-md-3 mb-2">Status</dt>
        <dd class="col-md-9 mb-2">
          <span class="badge bg-<?= $usuario['status'] === 'ativo' ? 'success' : 'secondary' ?>">
            <?= ucfirst($usuario['status']) ?>
          </span>
        </dd>
      </dl>
      <div class="mt-4">
        <form action="acoes.php" method="POST" class="d-inline">
          <input type="hidden" name="delete_usuario" value="<?= $usuario['id'] ?>">
          <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" class="btn btn-danger ripple" title="Excluir" data-bs-toggle="tooltip">
            <i class="bi bi-trash3-fill"></i> Excluir
          </button>
        </form>
        <form action="acoes.php" method="POST" class="d-inline">
          <input type="hidden" name="toggle_status" value="<?= $usuario['id'] ?>">
          <button type="submit" class="btn btn-warning ripple" title="Ativar/Inativar" data-bs-toggle="tooltip">
            <i class="bi bi-arrow-repeat"></i> Ativar/Inativar
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Fade-in animado no card de detalhes
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
});
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
