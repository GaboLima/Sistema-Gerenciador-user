<nav class="navbar navbar-expand-lg navbar-light navbar-custom shadow-sm py-3">
  <div class="container-fluid">
    <!-- LOGO OU ÍCONE -->
    <a class="navbar-brand d-flex align-items-center" href="index.php" style="font-size:1.4rem;font-weight:800;letter-spacing:1.5px;">
      <i class="bi bi-person-bounding-box me-2" style="color:#27ae60;font-size:1.5rem;"></i>
      <span style="color:#27ae60;">User</span><span style="color:#222;">Panel</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Menu">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse justify-content-end" id="mainNavbar">
      <ul class="navbar-nav mb-2 mb-lg-0 align-items-lg-center" style="font-size:1.13rem;">
        <!-- DASHBOARD/INÍCIO SEMPRE VISÍVEL -->
        <li class="nav-item">
          <a class="nav-link nav-menu-link" href="dashboard.php"><i class="bi bi-house-door-fill me-1"></i>Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link nav-menu-link" href="usuario-create.php"><i class="bi bi-plus-circle me-1"></i>Adicionar</a>
        </li>
        <!-- Outros itens -->
        <!--
        <li class="nav-item">
          <a class="nav-link nav-menu-link" href="relatorios.php"><i class="bi bi-graph-up me-1"></i>Relatórios</a>
        </li>
        -->
        <li class="nav-item ms-2">
          <a class="btn btn-success px-3" href="logout.php"><i class="bi bi-box-arrow-right me-1"></i>Sair</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<style>
  .navbar-custom {
    background: #fff !important;
    box-shadow: 0 4px 24px rgba(39,174,96,0.08), 0 1px 2.5px rgba(44,204,112,0.08);
    border-radius: 0 0 18px 18px;
    min-height: 65px;
  }
  .navbar-custom .navbar-brand {
    font-family: 'Inter', Arial, sans-serif;
    color: #27ae60 !important;
    letter-spacing: 1px;
    font-size: 1.3rem;
  }
  .nav-menu-link {
    color: #222 !important;
    font-weight: 600;
    border-radius: 6px;
    padding: 0.38em 0.95em;
    transition: background 0.17s, color 0.17s;
    margin-right: 2px;
  }
  .nav-menu-link.active,
  .nav-menu-link:focus,
  .nav-menu-link:hover {
    background: #e9f9f1;
    color: #27ae60 !important;
    text-decoration: none;
  }
  .btn-success {
    background: #27ae60;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    letter-spacing: 0.8px;
    transition: background 0.16s, color 0.16s;
    font-size: 1.06rem;
    box-shadow: 0 2px 7px rgba(44,204,112,0.06);
  }
  .btn-success:hover,
  .btn-success:focus {
    background: #22984f;
    color: #fff;
  }
  /* Menor sombra no mobile */
  @media (max-width: 480px) {
    .navbar-custom { box-shadow: 0 2px 10px rgba(39,174,96,0.11); }
    .navbar-brand { font-size: 1.01rem !important; }
    .btn-success { font-size: .99rem; }
  }
</style>
