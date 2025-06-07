<?php
session_start();
require 'conexao.php';

// BUSCA
$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'tabela';
$status = isset($_GET['status']) ? $_GET['status'] : '';

// ORDENAÇÃO
$ordem = isset($_GET['ordem']) ? $_GET['ordem'] : 'nome';
$direcao = isset($_GET['direcao']) && in_array(strtolower($_GET['direcao']), ['asc','desc']) ? $_GET['direcao'] : 'asc';
$camposValidos = ['id','nome','email','data_nascimento','status'];
if (!in_array($ordem, $camposValidos)) $ordem = 'nome';

// PAGINAÇÃO
$porPagina = 10;
$pagina = isset($_GET['pagina']) ? max((int)$_GET['pagina'], 1) : 1;
$offset = ($pagina - 1) * $porPagina;

// FILTRO SQL
$where = [];
if (!empty($busca)) {
    $buscaEscapada = mysqli_real_escape_string($conexao, $busca);
    $where[] = "nome LIKE '%$buscaEscapada%'";
}
if (!empty($status)) {
    $statusEscapado = mysqli_real_escape_string($conexao, $status);
    $where[] = "status = '$statusEscapado'";
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// USUÁRIOS PARA LISTA
$sql = "SELECT * FROM usuarios $whereSql ORDER BY $ordem $direcao LIMIT $porPagina OFFSET $offset";
$usuarios = mysqli_query($conexao, $sql);

// TOTAL PARA PAGINAÇÃO
$sqlTotal = "SELECT COUNT(*) as total FROM usuarios $whereSql";
$totalUsuarios = mysqli_fetch_assoc(mysqli_query($conexao, $sqlTotal))['total'];
$totalPaginas = ceil($totalUsuarios / $porPagina);

// RESUMO RÁPIDO
$totalGeral = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as total FROM usuarios"))['total'];
$totalAtivos = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as total FROM usuarios WHERE status='ativo'"))['total'];
$totalInativos = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as total FROM usuarios WHERE status='inativo'"))['total'];

// Função para gerar links de ordenação
function linkOrdena($campo, $label, $ordemAtual, $direcaoAtual, $busca, $status, $view, $pagina) {
  $direcao = ($ordemAtual === $campo && $direcaoAtual === 'asc') ? 'desc' : 'asc';
  $icone = '';
  if ($ordemAtual === $campo) {
    $icone = $direcaoAtual === 'asc' ? ' ▲' : ' ▼';
  }
  $params = [
    'ordem' => $campo,
    'direcao' => $direcao,
    'pagina' => $pagina,
    'busca' => $busca,
    'status' => $status,
    'view' => $view
  ];
  $url = '?' . http_build_query($params);
  return "<a href='$url' class='text-decoration-none'>$label$icone</a>";
}
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Usuários</title>
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

  <!-- Cards Resumo -->
  <div class="row mb-3">
    <div class="col-md-4">
      <div class="card border-success card-resumo shadow-sm">
        <div class="card-body text-success">
          <h6 class="card-title mb-2">Usuários Ativos</h6>
          <div class="card-text"><?= $totalAtivos ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-secondary card-resumo shadow-sm">
        <div class="card-body text-secondary">
          <h6 class="card-title mb-2">Usuários Inativos</h6>
          <div class="card-text"><?= $totalInativos ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-dark card-resumo shadow-sm">
        <div class="card-body text-dark">
          <h6 class="card-title mb-2">Total Geral</h6>
          <div class="card-text"><?= $totalGeral ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-0"><i class="bi bi-people-fill"></i> Lista de Usuários</h4>
        <?php if (!empty($busca)): ?>
          <small class="text-muted">Resultado para: <strong><?= htmlspecialchars($busca) ?></strong></small>
        <?php endif; ?>
      </div>
      <div>
        <a href="?view=tabela<?= !empty($busca) ? '&busca=' . urlencode($busca) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?>" class="btn btn-outline-dark btn-sm <?= $viewMode === 'tabela' ? 'active' : '' ?> ripple" title="Visualização em tabela" data-bs-toggle="tooltip"><i class="bi bi-table"></i></a>
        <a href="?view=card<?= !empty($busca) ? '&busca=' . urlencode($busca) : '' ?><?= !empty($status) ? '&status=' . urlencode($status) : '' ?>" class="btn btn-outline-dark btn-sm <?= $viewMode === 'card' ? 'active' : '' ?> ripple" title="Visualização em cards" data-bs-toggle="tooltip"><i class="bi bi-grid-3x3-gap-fill"></i></a>
        <a href="usuario-create.php" class="btn btn-primary ms-2 ripple" title="Adicionar usuário" data-bs-toggle="tooltip">
          <i class="bi bi-plus-circle"></i> Adicionar usuário
        </a>
      </div>
    </div>
    <div class="card-body">
      <form method="GET" class="row g-2 align-items-end mb-4">
        <div class="col-md-4">
          <input type="text" name="busca" class="form-control" placeholder="Buscar por nome" value="<?= htmlspecialchars($busca) ?>">
        </div>
        <div class="col-md-3">
          <select name="status" class="form-select">
            <option value="">Todos os Status</option>
            <option value="ativo" <?= ($status === 'ativo') ? 'selected' : '' ?>>Ativo</option>
            <option value="inativo" <?= ($status === 'inativo') ? 'selected' : '' ?>>Inativo</option>
          </select>
        </div>
        <input type="hidden" name="view" value="<?= htmlspecialchars($viewMode) ?>">
        <input type="hidden" name="ordem" value="<?= htmlspecialchars($ordem) ?>">
        <input type="hidden" name="direcao" value="<?= htmlspecialchars($direcao) ?>">
        <div class="col-auto">
          <button type="submit" class="btn btn-outline-primary ripple">
            <i class="bi bi-search"></i> Buscar
          </button>
        </div>
      </form>
      <a href="exportar.php" class="btn btn-success mb-3 ripple"><i class="bi bi-file-earmark-excel"></i> Exportar para Excel</a>

      <?php if ($totalUsuarios > 0): ?>
        <p class="text-success fw-semibold">Total de usuários encontrados: <?= $totalUsuarios ?></p>

        <?php if ($viewMode === 'tabela'): ?>
          <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle">
              <thead class="table-dark">
                <tr>
                  <th><?= linkOrdena('id', 'ID', $ordem, $direcao, $busca, $status, $viewMode, $pagina) ?></th>
                  <th><?= linkOrdena('nome', 'Nome', $ordem, $direcao, $busca, $status, $viewMode, $pagina) ?></th>
                  <th><?= linkOrdena('email', 'E-mail', $ordem, $direcao, $busca, $status, $viewMode, $pagina) ?></th>
                  <th><?= linkOrdena('data_nascimento', 'Data Nasc.', $ordem, $direcao, $busca, $status, $viewMode, $pagina) ?></th>
                  <th><?= linkOrdena('status', 'Status', $ordem, $direcao, $busca, $status, $viewMode, $pagina) ?></th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
  <?php foreach ($usuarios as $usuario): ?>
    <tr>
      <td><?= $usuario['id'] ?></td>
      <td><?= htmlspecialchars($usuario['nome']) ?></td>
      <td><?= htmlspecialchars($usuario['email']) ?></td>
      <td><?= date('d/m/Y', strtotime($usuario['data_nascimento'])) ?></td>
      <td>
        <span class="badge bg-<?= $usuario['status'] === 'ativo' ? 'success' : 'secondary' ?>">
          <?= ucfirst($usuario['status']) ?>
        </span>
      </td>
      <td>
        <div class="d-flex align-items-center flex-nowrap gap-1" style="white-space:nowrap;">
          <a href="usuario-view.php?id=<?= $usuario['id'] ?>" class="btn btn-secondary btn-sm ripple" title="Visualizar" data-bs-toggle="tooltip">
            <i class="bi bi-eye-fill"></i>
          </a>
          <a href="usuario-edit.php?id=<?= $usuario['id'] ?>" class="btn btn-success btn-sm ripple" title="Editar" data-bs-toggle="tooltip">
            <i class="bi bi-pencil-fill"></i>
          </a>
          <form action="acoes.php" method="POST" class="d-inline m-0 p-0">
            <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="delete_usuario" value="<?= $usuario['id'] ?>" class="btn btn-danger btn-sm ripple" title="Excluir" data-bs-toggle="tooltip" style="margin:0;">
              <i class="bi bi-trash3-fill"></i>
            </button>
          </form>
          <form action="acoes.php" method="POST" class="d-inline m-0 p-0">
            <input type="hidden" name="toggle_status" value="<?= $usuario['id'] ?>">
            <button type="submit" class="btn btn-warning btn-sm ripple" title="Ativar/Inativar" data-bs-toggle="tooltip" style="margin:0;">
              <i class="bi bi-arrow-repeat"></i>
            </button>
          </form>
        </div>
      </td>
    </tr>
  <?php endforeach; ?>
</tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($usuarios as $usuario): ?>
              <div class="col">
                <div class="card border-<?= $usuario['status'] === 'ativo' ? 'success' : 'secondary' ?>">
                  <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($usuario['nome']) ?></h5>
                    <p class="card-text">
                      <strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?><br>
                      <strong>Nascimento:</strong> <?= date('d/m/Y', strtotime($usuario['data_nascimento'])) ?><br>
                      <strong>Status:</strong>
                      <span class="badge bg-<?= $usuario['status'] === 'ativo' ? 'success' : 'secondary' ?>">
                        <?= ucfirst($usuario['status']) ?>
                      </span>
                    </p>
                    <a href="usuario-view.php?id=<?= $usuario['id'] ?>" class="btn btn-secondary btn-sm ripple" title="Visualizar" data-bs-toggle="tooltip"><i class="bi bi-eye-fill"></i></a>
                    <a href="usuario-edit.php?id=<?= $usuario['id'] ?>" class="btn btn-success btn-sm ripple" title="Editar" data-bs-toggle="tooltip"><i class="bi bi-pencil-fill"></i></a>
                    <form action="acoes.php" method="POST" class="d-inline">
                      <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="delete_usuario" value="<?= $usuario['id'] ?>" class="btn btn-danger btn-sm ripple" title="Excluir" data-bs-toggle="tooltip">
                        <i class="bi bi-trash3-fill"></i>
                      </button>
                    </form>
                    <form action="acoes.php" method="POST" class="d-inline">
                      <input type="hidden" name="toggle_status" value="<?= $usuario['id'] ?>">
                      <button type="submit" class="btn btn-warning btn-sm ripple" title="Ativar/Inativar" data-bs-toggle="tooltip">
                        <i class="bi bi-arrow-repeat"></i>
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <!-- Paginação -->
        <?php if ($totalPaginas > 1): ?>
          <nav aria-label="Paginação">
            <ul class="pagination justify-content-center mt-4">
              <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                  <a class="page-link ripple" href="?<?= http_build_query([
                    'pagina'=>$i,
                    'busca'=>$busca,
                    'status'=>$status,
                    'ordem'=>$ordem,
                    'direcao'=>$direcao,
                    'view'=>$viewMode
                  ]) ?>">
                    <?= $i ?>
                  </a>
                </li>
              <?php endfor; ?>
            </ul>
          </nav>
        <?php endif; ?>

      <?php else: ?>
        <div class="alert alert-warning text-center">
          Nenhum usuário encontrado<?= !empty($busca) ? ' para "<strong>' . htmlspecialchars($busca) . '</strong>"' : '' ?>.
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Cards de resumo
  document.querySelectorAll('.card-resumo').forEach((el, idx) => {
    el.classList.add('fade-in-up');
    el.style.animationDelay = (0.05 + idx * 0.07) + "s";
  });
  // Cards de usuário
  document.querySelectorAll('.card:not(.card-resumo)').forEach((el, idx) => {
    el.classList.add('fade-in-up');
    el.style.animationDelay = (0.10 + idx * 0.06) + "s";
  });
  // Linhas da tabela
  document.querySelectorAll('table tbody tr').forEach((el, idx) => {
    el.classList.add('fade-in-up');
    el.style.animationDelay = (0.11 + idx * 0.03) + "s";
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

// Loader ao filtrar/buscar
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

<script>
document.getElementById('toggleTheme').addEventListener('click', function() {
  document.body.classList.toggle('dark-mode');
  localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
  this.querySelector('i').className = document.body.classList.contains('dark-mode') ? 'bi bi-brightness-high' : 'bi bi-moon';
});

// Mantém preferência ao recarregar
if (localStorage.getItem('theme') === 'dark') {
  document.body.classList.add('dark-mode');
  document.getElementById('toggleTheme').querySelector('i').className = 'bi bi-brightness-high';
}
</script>

</body>
</html>
