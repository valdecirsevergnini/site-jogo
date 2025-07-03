<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

date_default_timezone_set("America/Sao_Paulo");

$provisoes = $conn->query("SELECT * FROM provisoes ORDER BY data_objetivo ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Provisões Financeiras</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3 p-md-5">
<div class="container">
  <h3 class="mb-4 text-center">Provisões Financeiras</h3>

  <div class="mb-3 text-end d-flex flex-wrap justify-content-between gap-2">
    <div>
      <a href="nova_provisao.php" class="btn btn-success">+ Nova Provisão</a>
      <a href="financeiro.php" class="btn btn-secondary">⬅ Voltar ao Financeiro</a>
      <a href="https://fenixdev.tech/boleiros/painel/includes/dashboard.php" class="btn btn-secondary">← Voltar ao Menu Principal</a>


    </div>
    <div>
      <a href="exportar_provisoes_pdf.php" class="btn btn-outline-danger">📄 Exportar PDF</a>
      <a href="exportar_provisoes_excel.php" class="btn btn-outline-success">📊 Exportar Excel</a>
    </div>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Título</th>
          <th>Meta (R$)</th>
          <th>Data Objetivo</th>
          <th>Mensal Necessário</th>
          <th>Progresso</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($provisoes->num_rows > 0): ?>
          <?php while ($p = $provisoes->fetch_assoc()): 
            $data_atual = new DateTime();
            $data_obj = new DateTime($p['data_objetivo']);
            $intervalo = $data_atual->diff($data_obj);
            $meses_restantes = max(1, ($intervalo->y * 12) + $intervalo->m);
            $mensal = $p['valor_meta'] / $meses_restantes;

            $total_arrecadado = floatval($p['valor_atual'] ?? 0);
            $progresso = $p['valor_meta'] > 0 ? min(100, round(($total_arrecadado / $p['valor_meta']) * 100)) : 0;
          ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= htmlspecialchars($p['titulo']) ?></td>
              <td>R$ <?= number_format($p['valor_meta'], 2, ',', '.') ?></td>
              <td><?= date('d/m/Y', strtotime($p['data_objetivo'])) ?></td>
              <td>R$ <?= number_format($mensal, 2, ',', '.') ?>/mês</td>
              <td>
                <div class="progress" style="height: 20px;">
                  <div class="progress-bar bg-success" role="progressbar" style="width: <?= $progresso ?>%">
                    <?= $progresso ?>%
                  </div>
                </div>
              </td>
              <td>
                <a href="editar_provisao.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="provisoes.php?excluir=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir esta provisão?')">Excluir</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="7" class="text-muted">Nenhuma provisão cadastrada.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
