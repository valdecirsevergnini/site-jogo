<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

date_default_timezone_set("America/Sao_Paulo");

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $valor = floatval(str_replace(['.', ','], ['', '.'], $_POST['valor'] ?? '0'));
    $data = $_POST['data_lancamento'] ?? '';
    $origem = $_POST['origem'] ?? '';

    if (!empty($tipo) && !empty($descricao) && $valor > 0 && !empty($data)) {
        $stmt = $conn->prepare("INSERT INTO financeiro (tipo, descricao, valor, data_lancamento, origem) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $tipo, $descricao, $valor, $data, $origem);
        if ($stmt->execute()) {
            $sucesso = "Movimentação registrada com sucesso!";
        } else {
            $erro = "Erro ao registrar movimentação.";
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nova Movimentação</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3 p-md-5">
<div class="container">
  <h3 class="mb-4 text-center">Nova Movimentação Financeira</h3>

  <?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
  <?php endif; ?>

  <?php if ($sucesso): ?>
    <div class="alert alert-success"><?= $sucesso ?></div>
    <a href="financeiro.php" class="btn btn-success">Voltar ao Painel</a>
  <?php else: ?>
    <form method="POST" class="row g-3">
      <div class="col-md-6 col-lg-4">
        <label for="tipo" class="form-label">Tipo*</label>
        <select name="tipo" id="tipo" class="form-select" required>
          <option value="">Selecione</option>
          <option value="entrada">Entrada</option>
          <option value="saida">Saída</option>
        </select>
      </div>

      <div class="col-md-6 col-lg-8">
        <label for="descricao" class="form-label">Descrição*</label>
        <input type="text" name="descricao" id="descricao" class="form-control" required>
      </div>

      <div class="col-md-6 col-lg-4">
        <label for="valor" class="form-label">Valor (R$)*</label>
        <input type="text" name="valor" id="valor" class="form-control" placeholder="0,00" required>
      </div>

      <div class="col-md-6 col-lg-4">
        <label for="data_lancamento" class="form-label">Data*</label>
        <input type="date" name="data_lancamento" id="data_lancamento" class="form-control" value="<?= date('Y-m-d') ?>" required>
      </div>

      <div class="col-md-12 col-lg-4">
        <label for="origem" class="form-label">Origem</label>
        <input type="text" name="origem" id="origem" class="form-control" placeholder="Ex: mensalidade, patrocínio...">
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn-primary">Salvar Movimentação</button>
        <a href="financeiro.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
