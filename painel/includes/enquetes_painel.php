<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

// Criar nova enquete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pergunta'])) {
    $pergunta = $_POST['pergunta'];
    $opcoes = array_filter($_POST['opcoes']);

    $stmt = $conn->prepare("INSERT INTO enquetes (pergunta) VALUES (?)");
    $stmt->bind_param("s", $pergunta);
    $stmt->execute();
    $enquete_id = $stmt->insert_id;

    // Desativa todas as anteriores
    $conn->query("UPDATE enquetes SET ativa = 0");
    $conn->query("UPDATE enquetes SET ativa = 1 WHERE id = $enquete_id");

    $opcao_stmt = $conn->prepare("INSERT INTO opcoes_enquete (enquete_id, opcao, votos) VALUES (?, ?, 0)");
    foreach ($opcoes as $opcao) {
        $opcao_stmt->bind_param("is", $enquete_id, $opcao);
        $opcao_stmt->execute();
    }

    header("Location: enquetes_painel.php?sucesso=1");
    exit();
}

// Excluir enquete
if (isset($_GET['excluir']) && is_numeric($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $conn->query("DELETE FROM opcoes_enquete WHERE enquete_id = $id");
    $conn->query("DELETE FROM enquetes WHERE id = $id");
    header("Location: enquetes_painel.php?excluida=1");
    exit();
}

// Listar
$enquetes = $conn->query("SELECT * FROM enquetes ORDER BY data_criacao DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Enquetes - Painel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; padding: 2rem; }
    .container { max-width: 850px; }
  </style>
</head>
<body>
<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h3>Criar Nova Enquete</h3>
    <a href="dashboard.php" class="btn btn-outline-secondary">Voltar ao Painel</a>
  </div>

  <?php if (isset($_GET['sucesso'])): ?>
    <div class="alert alert-success">Enquete criada com sucesso!</div>
  <?php elseif (isset($_GET['excluida'])): ?>
    <div class="alert alert-warning">Enquete excluída com sucesso!</div>
  <?php endif; ?>

  <form method="POST" class="mb-5">
    <div class="mb-3">
      <label class="form-label">Pergunta</label>
      <input type="text" name="pergunta" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Opções de resposta (até 4)</label>
      <input type="text" name="opcoes[]" class="form-control mb-2" placeholder="Opção 1" required>
      <input type="text" name="opcoes[]" class="form-control mb-2" placeholder="Opção 2">
      <input type="text" name="opcoes[]" class="form-control mb-2" placeholder="Opção 3">
      <input type="text" name="opcoes[]" class="form-control mb-2" placeholder="Opção 4">
    </div>
    <button type="submit" class="btn btn-primary">Criar Enquete</button>
  </form>

  <hr>

  <h4>Enquetes Recentes</h4>
  <ul class="list-group">
    <?php while ($row = $enquetes->fetch_assoc()): ?>
      <li class="list-group-item d-flex justify-content-between align-items-center <?= $row['ativa'] ? 'list-group-item-success' : '' ?>">
        <div>
          <strong><?= htmlspecialchars($row['pergunta']) ?></strong><br>
          <small>Criada em: <?= date('d/m/Y H:i', strtotime($row['data_criacao'])) ?></small><br>
          <?php if ($row['ativa']): ?>
            <span class="badge bg-success mt-1">Enquete Ativa</span>
          <?php endif; ?>
        </div>
        <a href="enquetes_painel.php?excluir=<?= $row['id'] ?>" onclick="return confirm('Deseja realmente excluir esta enquete?')" class="btn btn-sm btn-outline-danger">Excluir</a>
      </li>
    <?php endwhile; ?>
  </ul>
</div>
</body>
</html>
