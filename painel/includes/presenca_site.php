<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");


date_default_timezone_set("America/Sao_Paulo");
$data_hoje = date("Y-m-d");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    if (!empty($nome)) {
        $stmt = $conn->prepare("INSERT INTO presencas (nome, data) VALUES (?, ?)");
        $stmt->bind_param("ss", $nome, $data_hoje);
        $stmt->execute();
        header("Location: presenca_site.php?ok=1");
        exit();
    }
}

// Buscar confirmados de hoje
$confirmados = $conn->query("SELECT nome FROM presencas WHERE data = '$data_hoje' ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Confirmação de Presença</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light py-5">
  <div class="container bg-white p-4 rounded shadow" style="max-width: 600px;">
    <h3 class="mb-4">Confirme sua presença para este sábado</h3>

    <?php if (isset($_GET['ok'])): ?>
      <div class="alert alert-success">Presença confirmada com sucesso!</div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
      <div class="mb-3">
        <label class="form-label">Seu nome</label>
        <input type="text" name="nome" class="form-control" required>
      </div>
      <button type="submit" class="btn btn-success">Confirmar Presença</button>
    </form>

    <h5>Confirmados para hoje (<?php echo date('d/m/Y'); ?>)</h5>
    <ul class="list-group">
      <?php while ($p = $confirmados->fetch_assoc()): ?>
        <li class="list-group-item"><?php echo htmlspecialchars($p['nome']); ?></li>
      <?php endwhile; ?>
    </ul>
  </div>
</body>
</html>
