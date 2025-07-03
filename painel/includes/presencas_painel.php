<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$dataSelecionada = $_GET['data'] ?? date('Y-m-d');
$filtro_nome = $_GET['filtro_nome'] ?? '';

// Lista de datas disponíveis
$datas = $conn->query("SELECT DISTINCT data_jogo FROM presencas ORDER BY data_jogo DESC");

// Filtro de nome na consulta
$filtro_sql = $filtro_nome ? " AND nome LIKE '%$filtro_nome%'" : '';
$presencas = $conn->query("SELECT * FROM presencas WHERE data_jogo = '$dataSelecionada' $filtro_sql ORDER BY nome");

// Excluir presença
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn->query("DELETE FROM presencas WHERE id = $id");
    header("Location: presencas_painel.php?data=$dataSelecionada&filtro_nome=" . urlencode($filtro_nome));
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel de Presenças</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-4">
<div class="container">
  <h3 class="mb-3">Controle de Presenças</h3>

  <!-- Botão Nova Presença -->
  <a href="cadastrar_presenca.php" class="btn btn-success mb-3">+ Nova Presença</a>

  <!-- Filtro por data -->
  <form method="GET" class="row mb-3">
    <div class="col-md-4">
      <label class="form-label">Data do Jogo</label>
      <select name="data" class="form-select" onchange="this.form.submit()">
        <?php while ($d = $datas->fetch_assoc()): ?>
          <option value="<?= $d['data_jogo'] ?>" <?= $dataSelecionada === $d['data_jogo'] ? 'selected' : '' ?>>
            <?= date('d/m/Y', strtotime($d['data_jogo'])) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
  </form>

  <!-- Filtro por nome -->
  <form method="GET" class="row mb-3">
    <input type="hidden" name="data" value="<?= $dataSelecionada ?>">
    <div class="col-md-6">
      <input type="text" name="filtro_nome" class="form-control" placeholder="Filtrar por nome..." value="<?= htmlspecialchars($filtro_nome) ?>">
    </div>
    <div class="col-md-2">
      <button type="submit" class="btn btn-primary w-100">Filtrar</button>
    </div>
    <div class="col-md-2">
      <a href="presencas_painel.php?data=<?= $dataSelecionada ?>" class="btn btn-outline-secondary w-100">Limpar</a>
    </div>
    <div class="col-md-2">
      <a href="exportar_presencas_excel.php?data=<?= $dataSelecionada ?>&filtro_nome=<?= urlencode($filtro_nome) ?>" class="btn btn-outline-success w-100">📥 Excel</a>
    </div>
  </form>

  <!-- Lista de Presenças -->
  <div class="card">
    <div class="card-header bg-dark text-white">Confirmados em <?= date('d/m/Y', strtotime($dataSelecionada)) ?></div>
    <ul class="list-group list-group-flush">
      <?php if ($presencas->num_rows > 0): ?>
        <?php while ($row = $presencas->fetch_assoc()): ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <?= htmlspecialchars($row['nome']) ?>
              <small class="text-muted">(<?= htmlspecialchars($row['posicao'] ?? 'Indefinido') ?>)</small>
            </div>
            <a href="?data=<?= $dataSelecionada ?>&filtro_nome=<?= urlencode($filtro_nome) ?>&excluir=<?= $row['id'] ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remover presença de <?= $row['nome'] ?>?')">Excluir</a>
          </li>
        <?php endwhile; ?>
      <?php else: ?>
        <li class="list-group-item text-muted">Nenhuma presença registrada para este filtro.</li>
      <?php endif; ?>
    </ul>
  </div>
</div>
<div class="text-center mt-4">
  <a href="https://fenixdev.tech/boleiros/painel/includes/dashboard.php" class="btn btn-secondary">← Voltar ao Menu Principal</a>
</div>

</body>
</html>
