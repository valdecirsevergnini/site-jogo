<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

date_default_timezone_set("America/Sao_Paulo");
$data = date("Y-m-d");

$dia_semana = date('N'); // 1 (segunda) a 7 (domingo)
$hora_atual = date('H:i');
$permitir_presenca = ($dia_semana >= 1 && $dia_semana <= 6) || ($dia_semana == 6 && $hora_atual < '17:00');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['jogadores'])) {
    $todos = $_POST['jogadores'];

    $dados_jogadores = [];
    foreach ($todos as $nome) {
        $stmt = $conn->prepare("SELECT posicao FROM presencas WHERE nome = ? AND data_jogo = ?");
        $stmt->bind_param("ss", $nome, $data);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        $posicao = $res['posicao'] ?? 'Indefinido';
        $dados_jogadores[] = ['nome' => $nome, 'posicao' => $posicao];
    }

    shuffle($dados_jogadores);

    $azul = $preto = [];
    $ocupados_azul = $ocupados_preto = [];

    foreach ($dados_jogadores as $jogador) {
        $nome = $jogador['nome'];
        $pos = $jogador['posicao'];

        if ($pos === 'Goleiro') {
            if (!in_array('Goleiro', $ocupados_azul)) {
                $azul[] = $jogador;
                $ocupados_azul[] = 'Goleiro';
            } elseif (!in_array('Goleiro', $ocupados_preto)) {
                $preto[] = $jogador;
                $ocupados_preto[] = 'Goleiro';
            }
            continue;
        }

        $qtd_azul = array_count_values($ocupados_azul)[$pos] ?? 0;
        $qtd_preto = array_count_values($ocupados_preto)[$pos] ?? 0;

        if (count($azul) <= count($preto)) {
            if ($qtd_azul <= $qtd_preto) {
                $azul[] = $jogador;
                $ocupados_azul[] = $pos;
            } else {
                $preto[] = $jogador;
                $ocupados_preto[] = $pos;
            }
        } else {
            if ($qtd_preto <= $qtd_azul) {
                $preto[] = $jogador;
                $ocupados_preto[] = $pos;
            } else {
                $azul[] = $jogador;
                $ocupados_azul[] = $pos;
            }
        }
    }

    $conn->query("DELETE FROM sorteios WHERE data = '$data'");

    foreach ($azul as $j) {
        $stmt = $conn->prepare("INSERT INTO sorteios (nome, posicao, time, data) VALUES (?, ?, 'Azul', ?)");
        $stmt->bind_param("sss", $j['nome'], $j['posicao'], $data);
        $stmt->execute();
    }

    foreach ($preto as $j) {
        $stmt = $conn->prepare("INSERT INTO sorteios (nome, posicao, time, data) VALUES (?, ?, 'Preto', ?)");
        $stmt->bind_param("sss", $j['nome'], $j['posicao'], $data);
        $stmt->execute();
    }

    header("Location: sorteios_painel.php?ok=1");
    exit();
}

$presencas = $conn->query("SELECT nome, posicao FROM presencas WHERE data_jogo = '$data' ORDER BY nome");
$nomesConfirmados = [];
$presencas->data_seek(0);
while ($row = $presencas->fetch_assoc()) {
    $nomesConfirmados[] = $row;
}

$sorteados = $conn->query("SELECT nome, posicao, time FROM sorteios WHERE data = '$data'");
$times = ['Azul' => [], 'Preto' => []];
while ($s = $sorteados->fetch_assoc()) {
    $times[$s['time']][] = ['nome' => $s['nome'], 'posicao' => $s['posicao']];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Sorteio de Times</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-4">
<div class="container">
  <h3 class="mb-3">Sorteio de Times - <?= date('d/m/Y'); ?></h3>

  <a href="dashboard.php" class="btn btn-secondary mb-3">⬅ Voltar ao Painel</a>

  <?php if (isset($_GET['ok'])): ?>
    <div class="alert alert-success">Sorteio realizado com sucesso!</div>
  <?php endif; ?>

  <div class="alert alert-info border-primary">
    <strong><?= count($nomesConfirmados) ?> confirmados:</strong><br>
    <?php foreach ($nomesConfirmados as $p): ?>
      <?= htmlspecialchars($p['nome']) ?> <small class="text-muted">(<?= $p['posicao'] ?>)</small><br>
    <?php endforeach; ?>
  </div>

  <?php if ($permitir_presenca): ?>
  <form method="POST" class="mb-4">
    <div class="mb-2">
      <label class="form-label">Jogadores para o sorteio</label>
      <?php
      $presencas = $conn->query("SELECT nome FROM presencas WHERE data_jogo = '$data' ORDER BY nome");
      while ($p = $presencas->fetch_assoc()):
      ?>
        <div class="form-check">
          <input class="form-check-input" type="checkbox" name="jogadores[]" value="<?= $p['nome'] ?>" id="jog_<?= $p['nome'] ?>" checked>
          <label class="form-check-label" for="jog_<?= $p['nome'] ?>"><?= htmlspecialchars($p['nome']) ?></label>
        </div>
      <?php endwhile; ?>
    </div>
    <button class="btn btn-primary mt-2">Sortear Times</button>
  </form>
  <?php else: ?>
    <div class="alert alert-warning">A lista de presença está fechada. Permitido apenas de segunda até sábado às 17h.</div>
  <?php endif; ?>

  <?php if (count($times['Azul']) + count($times['Preto']) > 0): ?>
    <div class="row">
      <div class="col">
        <h5 class="text-info">Time Azul</h5>
        <ul class="list-group">
          <?php foreach ($times['Azul'] as $j): ?>
            <li class="list-group-item"><?= htmlspecialchars($j['nome']) ?> <small class="text-muted">(<?= $j['posicao'] ?>)</small></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="col">
        <h5 class="text-dark">Time Preto</h5>
        <ul class="list-group">
          <?php foreach ($times['Preto'] as $j): ?>
            <li class="list-group-item"><?= htmlspecialchars($j['nome']) ?> <small class="text-muted">(<?= $j['posicao'] ?>)</small></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  <?php endif; ?>
</div>
</body>
</html>
