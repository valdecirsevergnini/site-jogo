<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$mensagem = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $descricao = trim($_POST['descricao'] ?? '');
    $valor = floatval(str_replace([',', '.'], ['.', ''], $_POST['valor'] ?? '0.00')) / 100;
    $data = $_POST['data_lancamento'] ?? '';
    $origem = trim($_POST['origem'] ?? '');

    if (!empty($descricao) && $valor > 0 && !empty($data) && in_array($tipo, ['entrada', 'saida'])) {
        $stmt = $conn->prepare("INSERT INTO financeiro (tipo, descricao, valor, data_lancamento, origem) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $tipo, $descricao, $valor, $data, $origem);
        if ($stmt->execute()) {
            header("Location: financeiro.php?ok=1");
            exit;
        } else {
            $mensagem = "Erro ao salvar movimentação.";
        }
    } else {
        $mensagem = "Preencha todos os campos corretamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Nova Movimentação Financeira</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3 p-md-5">
<div class="container">
  <h3 class="mb-4 text-center">+ Nova Movimentação</h3>

  <?php if (!empty($mensagem)): ?>
    <div class="alert alert-danger"><?= $mensagem ?></div>
  <?php endif; ?>

  <form method="POST" class="bg-white p-4 rounded shadow-sm row g-3">
    <div class="col-md-6">
      <label for="tipo" class="form-label">Tipo*</label>
      <select name="tipo" id="tipo" class="form-select" required>
        <option value="">Selecione</option>
        <option value="entrada">Entrada</option>
        <option value="saida">Saída</option>
      </select>
    </div>

    <div class="col-md-6">
      <label for="data_lancamento" class="form-label">Data*</label>
      <input type="date" name="data_lancamento" id="data_lancamento" class="form-control" value="<?= date('Y-m-d') ?>" required>
    </div>

    <div class="col-12">
      <label for="descricao" class="form-label">Descrição*</label>
      <input type="text" name="descricao" id="descricao" class="form-control" required>
    </div>

    <div class="col-md-6">
      <label for="valor" class="form-label">Valor (R$)*</label>
      <input type="text" name="valor" id="valor" class="form-control" placeholder="Ex: 50,00" required>
    </div>

    <div class="col-md-6">
      <label for="origem" class="form-label">Origem</label>
      <input type="text" name="origem" id="origem" class="form-control" placeholder="Ex: Doação, Evento, Compra de bola">
    </div>

    <div class="col-12 d-flex gap-2 flex-wrap">
      <button type="submit" class="btn btn-primary">Salvar</button>
      <a href="financeiro.php" class="btn btn-secondary">Cancelar</a>
    </div>
  </form>
</div>
</body>
</html>
