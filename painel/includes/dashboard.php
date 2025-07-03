<?php
include_once(__DIR__ . "/auth.php");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel - Boleiros de Sábado</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      margin: 0;
      padding: 0;
      display: flex;
      min-height: 100vh;
      flex-direction: column;
    }

    .wrapper {
      display: flex;
      flex: 1;
    }

    .sidebar {
      width: 220px;
      background-color: #000;
      color: #fff;
      padding: 1rem;
      transition: transform 0.3s ease;
    }

    .sidebar a {
      display: block;
      color: #fff;
      text-decoration: none;
      margin-bottom: 1rem;
    }

    .sidebar a:hover {
      text-decoration: underline;
    }

    .content {
      flex-grow: 1;
      padding: 2rem;
      background-color: #f8f9fa;
    }

    .menu-toggle {
      background-color: #000;
      color: #fff;
      border: none;
      padding: 0.5rem 1rem;
      font-size: 1.2rem;
      width: 100%;
      text-align: left;
    }

    @media (max-width: 768px) {
      .sidebar {
        position: absolute;
        top: 44px;
        left: 0;
        height: 100vh;
        transform: translateX(-100%);
        z-index: 1000;
      }

      .sidebar.show {
        transform: translateX(0);
      }

      .content {
        padding: 1rem;
      }
    }
  </style>
</head>
<body>

  <!-- Botão de menu para mobile -->
  <button class="menu-toggle d-md-none" onclick="toggleSidebar()">☰ Menu</button>

  <div class="wrapper">
    <div class="sidebar" id="sidebar">
      <h4 class="text-center">Boleiros ⚽</h4>
      <hr>
      <a href="jogadores.php">🏃 Jogadores</a>
      <a href="sorteios_painel.php">🎲 Sorteio</a>
      <a href="enquetes_painel.php">📊 Enquetes</a>
      <a href="presencas_painel.php">✅ Presenças</a>
      <a href="financeiro.php">💰 Financeiro</a>
      <a href="provisoes.php">📅 Provisões</a>
      <a href="patrocinadores.php">📸 Patrocinadores</a>
      <a href="diretoria.php">👥 Diretoria</a>
      <a href="album_fotos.php">🖼️ Álbum de Fotos</a>
      <a href="melhor_em_campo_painel.php">⚽ Melhor em Campo</a>
      <a href="../../index.php" target="_blank">🌐 Ir para o Site</a>

      

      <hr>
      <a href="logout.php">🚪 Sair</a>
    </div>

    <div class="content">
      <h3>Bem-vindo, <?php echo $_SESSION['usuario_logado']; ?>!</h3>
      <p>Cargo: <?php echo $_SESSION['cargo']; ?></p>
      <p>Use o menu ao lado para gerenciar o sistema do Boleiros de Sábado.</p>
    </div>
  </div>

  <script>
    function toggleSidebar() {
      const sidebar = document.getElementById('sidebar');
      sidebar.classList.toggle('show');
    }
  </script>
</body>
</html>
