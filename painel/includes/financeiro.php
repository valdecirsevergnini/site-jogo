<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

date_default_timezone_set("America/Sao_Paulo");

// Excluir movimentação
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn->query("DELETE FROM financeiro WHERE id = $id");
    header("Location: financeiro.php");
    exit;
}

// Buscar dados
$sql = "SELECT * FROM financeiro ORDER BY data_lancamento DESC";
$result = $conn->query($sql);

$entradas = 0;
$saidas = 0;
$registros = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $registros[] = $row;
        if ($row['tipo'] === 'entrada') {
            $entradas += $row['valor'];
        } elseif ($row['tipo'] === 'saida') {
            $saidas += $row['valor'];
        }
    }
}

$saldo = $entradas - $saidas;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel Financeiro</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light p-4">
<div class="container">
  <h3 class="mb-4 text-center">Painel Financeiro</h3>

  <div class="row text-center mb-4">
    <div class="col-md-4 mb-2">
      <div class="alert alert-success">
        <strong>Entradas:</strong> R$ <?= number_format($entradas, 2, ',', '.') ?>
      </div>
    </div>
    <div class="col-md-4 mb-2">
      <div class="alert alert-danger">
        <strong>Saídas:</strong> R$ <?= number_format($saidas, 2, ',', '.') ?>
      </div>
    </div>
    <div class="col-md-4 mb-2">
      <div class="alert alert-primary">
        <strong>Saldo em Caixa:</strong> R$ <?= number_format($saldo, 2, ',', '.') ?>
      </div>
    </div>
  </div>

  <div class="row justify-content-center mb-5">
    <div class="col-md-6">
      <canvas id="graficoFinanceiro"></canvas>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-hover align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Tipo</th>
          <th>Descrição</th>
          <th>Valor</th>
          <th>Data</th>
          <th>Origem</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if (count($registros) > 0): ?>
          <?php foreach ($registros as $row): ?>
            <tr>
              <td><?= $row['id'] ?></td>
              <td>
                <span class="badge <?= $row['tipo'] === 'entrada' ? 'bg-success' : 'bg-danger' ?>">
                  <?= ucfirst($row['tipo']) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($row['descricao']) ?></td>
              <td>R$ <?= number_format($row['valor'], 2, ',', '.') ?></td>
              <td><?= date('d/m/Y', strtotime($row['data_lancamento'])) ?></td>
              <td><?= htmlspecialchars($row['origem']) ?></td>
              <td>
                <a href="editar_movimentacao.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="?excluir=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir esta movimentação?')">Excluir</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-muted">Nenhum lançamento encontrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-4 d-flex flex-wrap gap-2">
    <a href="nova_movimentacao.php" class="btn btn-primary">+ Nova Movimentação</a>
    
  <a href="https://fenixdev.tech/boleiros/painel/includes/dashboard.php" class="btn btn-secondary">← Voltar ao Menu Principal</a>


  </div>
</div>

<script>
  const ctx = document.getElementById('graficoFinanceiro').getContext('2d');
  new Chart(ctx, {
    type: 'pie',
    data: {
      labels: ['Entradas', 'Saídas'],
      datasets: [{
        label: 'Financeiro',
        data: [<?= $entradas ?>, <?= $saidas ?>],
        backgroundColor: ['#198754', '#dc3545'],
        borderColor: '#fff',
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      plugins: {
        legend: { position: 'bottom' },
        title: { display: true, text: 'Distribuição de Entradas e Saídas' }
      }
    }
  });
</script>
</body>
</html>
