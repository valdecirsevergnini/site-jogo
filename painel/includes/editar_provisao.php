<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

date_default_timezone_set("America/Sao_Paulo");

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: provisoes.php");
    exit;
}

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $valor_meta = floatval(str_replace(['.', ','], ['', '.'], $_POST['valor_meta'] ?? '0'));
    $valor_arrecadado = floatval(str_replace(['.', ','], ['', '.'], $_POST['valor_arrecadado'] ?? '0'));
    $data_objetivo = $_POST['data_objetivo'] ?? '';

    if ($titulo && $valor_meta > 0 && $data_objetivo) {
        $stmt = $conn->prepare("UPDATE provisoes SET titulo=?, descricao=?, valor_meta=?, valor_arrecadado=?, data_objetivo=? WHERE id=?");
        $stmt->bind_param("ssddsi", $titulo, $descricao, $valor_meta, $valor_arrecadado, $data_objetivo, $id);
        if ($stmt->execute()) {
            $sucesso = "Provisão atualizada com sucesso!";
        } else {
            $erro = "Erro ao atualizar a provisão.";
        }
    } else {
        $erro = "Preencha todos os campos obrigatórios.";
    }
}

$provisao = $conn->query("SELECT * FROM provisoes WHERE id = $id")->fetch_assoc();
if (!$provisao) {
    echo "<p class='text-danger'>Provisão não encontrada.</p>";
    exit;
}

// Cálculo de progresso
$progresso = $provisao['valor_arrecadado'] > 0 && $provisao['valor_meta'] > 0
    ? round(($provisao['valor_arrecadado'] / $provisao['valor_meta']) * 100)
    : 0;

// Cálculo de quanto juntar por mês
$hoje = new DateTime();
$objetivo = new DateTime($provisao['data_objetivo']);
$meses = $hoje < $objetivo ? $hoje->diff($objetivo)->m + ($hoje->diff($objetivo)->y * 12) : 0;
$valor_faltante = max(0, $provisao['valor_meta'] - $provisao['valor_arrecadado']);
$por_mes = $meses > 0 ? $valor_faltante / $meses : $valor_faltante;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Provisão</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3 p-md-5">
<div class="container">
  <h3 class="mb-4 text-center">Editar Provisão</h3>

  <?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
  <?php endif; ?>

  <?php if ($sucesso): ?>
    <div class="alert alert-success"><?= $sucesso ?></div>
    <a href="provisoes.php" class="btn btn-success">Voltar às Provisões</a>
  <?php else: ?>
    <form method="POST" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Título*</label>
        <input type="text" name="titulo" class="form-control" required value="<?= htmlspecialchars($provisao['titulo']) ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Data Objetivo*</label>
        <input type="date" name="data_objetivo" class="form-control" required value="<?= $provisao['data_objetivo'] ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Valor da Meta (R$)*</label>
        <input type="text" name="valor_meta" class="form-control" required value="<?= number_format($provisao['valor_meta'], 2, ',', '.') ?>">
      </div>
      <div class="col-md-6">
        <label class="form-label">Valor Arrecadado (R$)</label>
        <input type="text" name="valor_arrecadado" class="form-control" value="<?= number_format($provisao['valor_arrecadado'], 2, ',', '.') ?>">
      </div>
      <div class="col-12">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control"><?= htmlspecialchars($provisao['descricao']) ?></textarea>
      </div>

      <div class="col-12 mt-3">
        <label class="form-label">Progresso</label>
        <div class="progress">
          <div class="progress-bar bg-info" style="width: <?= $progresso ?>%"><?= $progresso ?>%</div>
        </div>
        <small class="text-muted">Faltam R$ <?= number_format($valor_faltante, 2, ',', '.') ?> | <?= $meses ?> mês(es) → R$ <?= number_format($por_mes, 2, ',', '.') ?>/mês</small>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap mt-4">
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="provisoes.php" class="btn btn-secondary">Cancelar</a>
        <a href="exportar_provisoes_pdf.php?id=<?= $provisao['id'] ?>" class="btn btn-outline-danger">📄 PDF</a>
        <a href="exportar_provisoes_excel.php?id=<?= $provisao['id'] ?>" class="btn btn-outline-success">📊 Excel</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
