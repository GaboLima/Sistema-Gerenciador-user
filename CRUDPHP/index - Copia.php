<?php
session_start();
require 'conexao.php';

$busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';
$viewMode = isset($_GET['view']) ? $_GET['view'] : 'tabela';

$sql = "SELECT * FROM usuarios";
if (!empty($busca)) {
    $buscaEscapada = mysqli_real_escape_string($conexao, $busca);
    $sql .= " WHERE nome LIKE '%$buscaEscapada%'";
}
$usuarios = mysqli_query($conexao, $sql);
$quantidadeUsuarios = mysqli_num_rows($usuarios);
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Usuários</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
<?php include('navbar.php'); ?>
<div class="container mt-4">
  <?php include('mensagem.php'); ?>

  <div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <div>
        <h4 class="mb-0"><i class="bi bi-people-fill"></i> Lista de Usuários</h4>
        <?php if (!empty($busca)): ?>
          <small class="text-muted">Resultado para: <strong><?= htmlspecialchars($busca) ?></strong></small>
        <?php endif; ?>
      </div>
      <div>
        <a href="?view=tabela<?= !empty($busca) ? '&busca=' . urlencode($busca) : '' ?>" class="btn btn-outline-dark btn-sm <?= $viewMode === 'tabela' ? 'active' : '' ?>"><i class="bi bi-table"></i></a>
        <a href="?view=card<?= !empty($busca) ? '&busca=' . urlencode($busca) : '' ?>" class="btn btn-outline-dark btn-sm <?= $viewMode === 'card' ? 'active' : '' ?>"><i class="bi bi-grid-3x3-gap-fill"></i></a>
        <a href="usuario-create.php" class="btn btn-primary ms-2">
          <i class="bi bi-plus-circle"></i> Adicionar usuário
        </a>
      </div>
    </div>
    <div class="card-body">
      <form method="GET" class="mb-4">
        <div class="input-group">
          <input type="text" name="busca" class="form-control" placeholder="Buscar por nome" value="<?= htmlspecialchars($busca) ?>">
          <input type="hidden" name="view" value="<?= htmlspecialchars($viewMode) ?>">
          <button type="submit" class="btn btn-outline-primary">
            <i class="bi bi-search"></i> Buscar
          </button>
        </div>
        

      </form>
      <a href="exportar.php" class="btn btn-success mb-3">📤 Exportar para Excel</a>
      <?php if ($quantidadeUsuarios > 0): ?>
        <p class="text-success fw-semibold">Total de usuários encontrados: <?= $quantidadeUsuarios ?></p>

        <?php if ($viewMode === 'tabela'): ?>
          <table class="table table-bordered table-striped table-hover align-middle">
            <thead class="table-dark">
              <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Data Nascimento</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($usuarios as $usuario): ?>
                <tr>
                  <td><?= $usuario['id'] ?></td>
                  <td><?= $usuario['nome'] ?></td>
                  <td><?= $usuario['email'] ?></td>
                  <td><?= date('d/m/Y', strtotime($usuario['data_nascimento'])) ?></td>
                  <td>
                    <span class="badge bg-<?= $usuario['status'] === 'ativo' ? 'success' : 'secondary' ?>">
                      <?= ucfirst($usuario['status']) ?>
                    </span>
                  </td>
                  <td>
                    <a href="usuario-view.php?id=<?= $usuario['id'] ?>" class="btn btn-secondary btn-sm"><i class="bi bi-eye-fill"></i></a>
                    <a href="usuario-edit.php?id=<?= $usuario['id'] ?>" class="btn btn-success btn-sm"><i class="bi bi-pencil-fill"></i></a>
                    <form action="acoes.php" method="POST" class="d-inline">
                      <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="delete_usuario" value="<?= $usuario['id'] ?>" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash3-fill"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($usuarios as $usuario): ?>
              <div class="col">
                <div class="card border-<?= $usuario['status'] === 'ativo' ? 'success' : 'secondary' ?>">
                  <div class="card-body">
                    <h5 class="card-title"><?= $usuario['nome'] ?></h5>
                    <p class="card-text">
                      <strong>Email:</strong> <?= $usuario['email'] ?><br>
                      <strong>Nascimento:</strong> <?= date('d/m/Y', strtotime($usuario['data_nascimento'])) ?><br>
                      <strong>Status:</strong>
                      <span class="badge bg-<?= $usuario['status'] === 'ativo' ? 'success' : 'secondary' ?>">
                        <?= ucfirst($usuario['status']) ?>
                      </span>
                    </p>
                    <a href="usuario-view.php?id=<?= $usuario['id'] ?>" class="btn btn-secondary btn-sm"><i class="bi bi-eye-fill"></i> Visualizar</a>
                    <a href="usuario-edit.php?id=<?= $usuario['id'] ?>" class="btn btn-success btn-sm"><i class="bi bi-pencil-fill"></i> Editar</a>
                    <form action="acoes.php" method="POST" class="d-inline">
                      <button onclick="return confirm('Tem certeza que deseja excluir?')" type="submit" name="delete_usuario" value="<?= $usuario['id'] ?>" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash3-fill"></i> Excluir
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
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

</body>
</html>
