<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: financeiro.php");
    exit;
}

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $valor = floatval(str_replace(['.', ','], ['', '.'], $_POST['valor'] ?? '0'));
    $data = $_POST['data_lancamento'] ?? '';
    $origem = $_POST['origem'] ?? '';

    if ($tipo && $descricao && $valor && $data) {
        $stmt = $conn->prepare("UPDATE financeiro SET tipo = ?, descricao = ?, valor = ?, data_lancamento = ?, origem = ? WHERE id = ?");
        $stmt->bind_param("ssdssi", $tipo, $descricao, $valor, $data, $origem, $id);
        if ($stmt->execute()) {
            $sucesso = "Movimentação atualizada com sucesso!";
        } else {
            $erro = "Erro ao atualizar movimentação.";
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}

// Buscar dados atuais
$mov = $conn->query("SELECT * FROM financeiro WHERE id = $id")->fetch_assoc();
if (!$mov) {
    echo "<p class='text-danger'>Movimentação não encontrada.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Movimentação</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3 p-md-5">
<div class="container">
  <h3 class="mb-4 text-center">Editar Movimentação #<?= $id ?></h3>

  <?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
  <?php endif; ?>

  <?php if ($sucesso): ?>
    <div class="alert alert-success"><?= $sucesso ?></div>
    <a href="financeiro.php" class="btn btn-success">Voltar ao Painel</a>
  <?php else: ?>
    <form method="POST" class="row g-3">
      <div class="col-md-6">
        <label for="tipo" class="form-label">Tipo*</label>
        <select name="tipo" id="tipo" class="form-select" required>
          <option value="entrada" <?= $mov['tipo'] === 'entrada' ? 'selected' : '' ?>>Entrada</option>
          <option value="saida" <?= $mov['tipo'] === 'saida' ? 'selected' : '' ?>>Saída</option>
        </select>
      </div>

      <div class="col-md-6">
        <label for="descricao" class="form-label">Descrição*</label>
        <input type="text" name="descricao" id="descricao" class="form-control" required value="<?= htmlspecialchars($mov['descricao']) ?>">
      </div>

      <div class="col-md-4">
        <label for="valor" class="form-label">Valor (R$)*</label>
        <input type="text" name="valor" id="valor" class="form-control" required value="<?= number_format($mov['valor'], 2, ',', '.') ?>">
      </div>

      <div class="col-md-4">
        <label for="data_lancamento" class="form-label">Data*</label>
        <input type="date" name="data_lancamento" id="data_lancamento" class="form-control" required value="<?= $mov['data_lancamento'] ?>">
      </div>

      <div class="col-md-4">
        <label for="origem" class="form-label">Origem</label>
        <input type="text" name="origem" id="origem" class="form-control" value="<?= htmlspecialchars($mov['origem']) ?>">
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="financeiro.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
