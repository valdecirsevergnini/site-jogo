<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

date_default_timezone_set("America/Sao_Paulo");

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $valor_meta = floatval(str_replace(['.', ','], ['', '.'], $_POST['valor_meta']));
    $data_objetivo = $_POST['data_objetivo'];
    $data_criacao = date('Y-m-d');

    if (!empty($titulo) && $valor_meta > 0 && !empty($data_objetivo)) {
        $stmt = $conn->prepare("INSERT INTO provisoes (titulo, descricao, valor_meta, data_objetivo, data_criacao) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdss", $titulo, $descricao, $valor_meta, $data_objetivo, $data_criacao);
        if ($stmt->execute()) {
            $sucesso = "Provisão cadastrada com sucesso!";
        } else {
            $erro = "Erro ao salvar a provisão.";
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
  <title>Nova Provisão</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3 p-md-5">
<div class="container">
  <h3 class="mb-4 text-center">Nova Provisão</h3>

  <?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
  <?php endif; ?>

  <?php if ($sucesso): ?>
    <div class="alert alert-success"><?= $sucesso ?></div>
    <a href="provisoes.php" class="btn btn-success">Voltar para Provisões</a>
  <?php else: ?>
    <form method="POST" class="row g-3 bg-white p-4 rounded shadow-sm">
      <div class="col-md-6">
        <label for="titulo" class="form-label">Título*</label>
        <input type="text" name="titulo" id="titulo" class="form-control" required>
      </div>

      <div class="col-md-6">
        <label for="valor_meta" class="form-label">Valor Meta (R$)*</label>
        <input type="text" name="valor_meta" id="valor_meta" class="form-control" placeholder="Ex: 1500,00" required>
      </div>

      <div class="col-md-6">
        <label for="data_objetivo" class="form-label">Data do Objetivo*</label>
        <input type="date" name="data_objetivo" id="data_objetivo" class="form-control" required>
      </div>

      <div class="col-md-6">
        <label for="descricao" class="form-label">Descrição</label>
        <input type="text" name="descricao" id="descricao" class="form-control" placeholder="Ex: Compra de uniforme, churrasco...">
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn-primary">Salvar Provisão</button>
        <a href="provisoes.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
