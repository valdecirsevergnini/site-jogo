<?php
include_once("painel/includes/conexao.php");

// Buscar enquete ativa
$enquete = $conn->query("SELECT * FROM enquetes WHERE ativa = 1 ORDER BY data_criacao DESC LIMIT 1")->fetch_assoc();

if (!$enquete) {
    echo "<p>Não há enquete ativa no momento.</p>";
    exit();
}

// Buscar opções
$opcoes = $conn->query("SELECT * FROM opcoes_enquete WHERE enquete_id = " . $enquete['id']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $opcao_id = $_POST['opcao'] ?? 0;
    $votou = $_COOKIE['votou_enquete_' . $enquete['id']] ?? false;

    if (!$votou && $opcao_id) {
        $conn->query("UPDATE opcoes_enquete SET votos = votos + 1 WHERE id = $opcao_id");
        setcookie("votou_enquete_" . $enquete['id'], true, time() + (3600 * 24 * 30)); // 30 dias
        header("Location: votar_enquete_site.php?enquete_id=" . $enquete['id']);
        exit();
    }
}

// Total de votos
$total_votos = $conn->query("SELECT SUM(votos) as total FROM opcoes_enquete WHERE enquete_id = " . $enquete['id'])->fetch_assoc()['total'] ?? 0;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Votação - Enquete</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light py-5">
<div class="container bg-white p-4 rounded shadow-sm" style="max-width: 600px;">
  <h4><?php echo $enquete['pergunta']; ?></h4>
  <form method="POST" class="my-3">
    <?php while ($row = $opcoes->fetch_assoc()): ?>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="opcao" value="<?php echo $row['id']; ?>" id="op<?php echo $row['id']; ?>">
        <label class="form-check-label" for="op<?php echo $row['id']; ?>">
          <?php echo $row['opcao']; ?>
        </label>
      </div>
    <?php endwhile; ?>
    <button type="submit" class="btn btn-primary mt-3">Votar</button>
  </form>

  <h5>Resultado parcial:</h5>
  <?php
  $res = $conn->query("SELECT * FROM opcoes_enquete WHERE enquete_id = " . $enquete['id']);
  while ($row = $res->fetch_assoc()):
    $percentual = $total_votos ? round(($row['votos'] / $total_votos) * 100) : 0;
  ?>
    <div class="mb-2">
      <strong><?php echo $row['opcao']; ?></strong>
      <div class="progress">
        <div class="progress-bar" role="progressbar" style="width: <?php echo $percentual; ?>%">
          <?php echo $percentual; ?>%
        </div>
      </div>
    </div>
  <?php endwhile; ?>
</div>
</body>
</html>
