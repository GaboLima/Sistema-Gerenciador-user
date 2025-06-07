<?php
session_start();
require 'conexao.php';

// Consultas para resumo
$totalUsuarios = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as total FROM usuarios"))['total'];
$totalAtivos = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as total FROM usuarios WHERE status='ativo'"))['total'];
$totalInativos = mysqli_fetch_assoc(mysqli_query($conexao, "SELECT COUNT(*) as total FROM usuarios WHERE status='inativo'"))['total'];

// Exemplo para gráfico: usuários cadastrados por mês (substitua pelo seu SQL real)
$dadosPorMes = [];
$consulta = mysqli_query($conexao, "
    SELECT DATE_FORMAT(data_nascimento, '%m/%Y') as mes, COUNT(*) as total
    FROM usuarios
    GROUP BY mes
    ORDER BY STR_TO_DATE(CONCAT('01/', mes), '%d/%m/%Y') ASC
");
while ($linha = mysqli_fetch_assoc($consulta)) {
    $dadosPorMes[$linha['mes']] = $linha['total'];
}
$labelsMes = array_keys($dadosPorMes);
$valoresMes = array_values($dadosPorMes);
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:400,500,700&display=swap">
  <link rel="stylesheet" href="styles.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background: linear-gradient(120deg, #e9f9f1 0%, #fff 100%);
    }
    .dashboard-header {
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      gap: 0.5em;
      margin-bottom: 2.3rem;
      margin-top: 1rem;
    }
    .dashboard-header h1 {
      font-size: 2.2rem;
      font-weight: 800;
      color: #27ae60;
      margin-bottom: 0.3em;
      letter-spacing: 1px;
    }
    .dashboard-header .lead {
      color: #22984f;
      font-weight: 500;
      font-size: 1.18rem;
    }
    .card-resumo {
      border: none;
      border-radius: 18px;
      box-shadow: 0 3px 22px rgba(44,204,112,0.09), 0 1.5px 3.5px rgba(44,204,112,0.08);
      background: #fff;
      padding: 2.3em 1.4em 1.3em 1.5em;
      text-align: left;
      transition: box-shadow 0.13s;
      min-height: 148px;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
      position: relative;
    }
    .card-resumo:hover {
      box-shadow: 0 8px 36px rgba(39,174,96,0.11);
    }
    .card-resumo .icon-bg {
      position: absolute;
      right: 18px; top: 18px;
      font-size: 3.2em;
      color: #e9f9f1;
      z-index: 0;
      pointer-events: none;
      user-select: none;
    }
    .card-resumo .card-title {
      color: #27ae60;
      font-weight: 600;
      font-size: 1.13rem;
      z-index: 1;
    }
    .card-resumo .card-number {
      font-size: 2.3rem;
      font-weight: 900;
      color: #232323;
      margin-top: 0.18em;
      letter-spacing: 1px;
      z-index: 1;
    }
    .card-resumo .card-description {
      font-size: 1.01rem;
      color: #6da78a;
      margin-top: 0.09em;
      z-index: 1;
    }
    .dashboard-graficos {
      margin-top: 3rem;
      margin-bottom: 2rem;
      display: flex;
      flex-wrap: wrap;
      gap: 2.5rem;
      align-items: stretch;
      justify-content: space-between;
    }
    .grafico-card {
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 2px 16px rgba(44,204,112,0.07);
      padding: 2rem 1.2rem 1.5rem 1.2rem;
      flex: 1 1 320px;
      min-width: 320px;
      max-width: 480px;
      margin: 0 auto;
      display: flex;
      flex-direction: column;
      align-items: center;
    }
    .grafico-card h3 {
      color: #27ae60;
      font-weight: 700;
      margin-bottom: 1.5rem;
      font-size: 1.13rem;
    }
    @media (max-width: 990px) {
      .dashboard-header h1 { font-size: 1.4rem; }
      .card-resumo { min-height: 110px; padding: 1.2em 0.7em 1.1em 1.1em;}
      .card-resumo .icon-bg { font-size: 2.2em; right: 12px; top: 12px;}
      .dashboard-graficos { flex-direction: column; gap: 1.7rem;}
      .grafico-card { max-width: 98vw;}
    }
  </style>
</head>
<body>
<?php include('navbar.php'); ?>

<div class="container mt-5 mb-4">
  <div class="dashboard-header fade-in-up">
    <h1><i class="bi bi-speedometer2 me-1"></i> Dashboard</h1>
    <div class="lead">Visão geral do sistema</div>
  </div>
  <div class="row g-4">
    <div class="col-md-4">
      <div class="card-resumo fade-in-up" style="border-left:7px solid #27ae60;">
        <span class="icon-bg"><i class="bi bi-people-fill"></i></span>
        <div class="card-title">Total de Usuários</div>
        <div class="card-number"><?= $totalUsuarios ?></div>
        <div class="card-description">Usuários cadastrados</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card-resumo fade-in-up" style="border-left:7px solid #38e68d;">
        <span class="icon-bg"><i class="bi bi-person-check-fill"></i></span>
        <div class="card-title">Ativos</div>
        <div class="card-number"><?= $totalAtivos ?></div>
        <div class="card-description">Usuários ativos</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card-resumo fade-in-up" style="border-left:7px solid #b6f7d8;">
        <span class="icon-bg"><i class="bi bi-person-x-fill"></i></span>
        <div class="card-title">Inativos</div>
        <div class="card-number"><?= $totalInativos ?></div>
        <div class="card-description">Usuários inativos</div>
      </div>
    </div>
  </div>

  <div class="dashboard-graficos">
    <div class="grafico-card">
      <h3><i class="bi bi-bar-chart-fill me-1"></i>Usuários por Mês</h3>
      <canvas id="graficoMes" width="360" height="240"></canvas>
    </div>
    <div class="grafico-card">
      <h3><i class="bi bi-pie-chart-fill me-1"></i>Ativos x Inativos</h3>
      <canvas id="graficoStatus" width="280" height="240"></canvas>
    </div>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // Gráfico de Barras (Usuários por Mês)
  const ctxMes = document.getElementById('graficoMes').getContext('2d');
  new Chart(ctxMes, {
    type: 'bar',
    data: {
      labels: <?= json_encode($labelsMes) ?>,
      datasets: [{
        label: 'Usuários',
        data: <?= json_encode($valoresMes) ?>,
        backgroundColor: 'rgba(39, 174, 96, 0.67)',
        borderRadius: 6,
        maxBarThickness: 40
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: { grid: { display: false }, ticks: { color: '#27ae60', font: {weight:'bold'}} },
        y: { beginAtZero: true, grid: { color:'#ecf5f0'}, ticks: { color: '#aaa'} }
      }
    }
  });

  // Gráfico de Pizza (Ativos x Inativos)
  const ctxStatus = document.getElementById('graficoStatus').getContext('2d');
  new Chart(ctxStatus, {
    type: 'doughnut',
    data: {
      labels: ['Ativos', 'Inativos'],
      datasets: [{
        data: [<?= $totalAtivos ?>, <?= $totalInativos ?>],
        backgroundColor: ['#27ae60', '#e1efe7'],
        borderWidth: 2,
        borderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: true,
          labels: { color: '#27ae60', font: {weight:'bold'}}
        }
      },
      cutout: '68%'
    }
  });
</script>
</body>
</html>
