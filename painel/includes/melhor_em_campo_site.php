<?php
include_once(__DIR__ . "/conexao.php");

date_default_timezone_set("America/Sao_Paulo");
$data_hoje = date("Y-m-d");
$ip = $_SERVER['REMOTE_ADDR'];

// Verifica se já votou hoje
$verifica = $conn->prepare("SELECT 1 FROM votos_melhor_em_campo WHERE ip_votante = ? AND data_jogo = ?");
$verifica->bind_param("ss", $ip, $data_hoje);
$verifica->execute();
$ja_votou = $verifica->get_result()->num_rows > 0;

// Posições do futebol
$posicoes = ["Goleiro", "Zagueiro", "Lateral", "Volante", "Meia", "Atacante"];

// Enviar votos
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$ja_votou) {
    foreach ($posicoes as $posicao) {
        $campo = "pos_" . strtolower($posicao);
        $valor = $_POST[$campo] ?? '';

        $voto_nulo = ($valor === 'nulo') ? 1 : 0;
        $jogador_id = ($voto_nulo) ? null : intval($valor);

        if (!$voto_nulo && $jogador_id) {
            // Verifica se jogador existe
            $check = $conn->prepare("SELECT 1 FROM jogadores WHERE id = ?");
            $check->bind_param("i", $jogador_id);
            $check->execute();
            $exists = $check->get_result()->num_rows > 0;

            if (!$exists) {
                // Cria jogador automaticamente
                $info = $conn->prepare("SELECT nome, posicao FROM presencas WHERE id = ?");
                $info->bind_param("i", $jogador_id);
                $info->execute();
                $dados = $info->get_result()->fetch_assoc();

                if ($dados) {
                    $nome = $dados['nome'];
                    $pos = $dados['posicao'];
                    $insertJogador = $conn->prepare("INSERT INTO jogadores (id, nome, posicao, foto, pontuacao) VALUES (?, ?, ?, '', 1)");
                    $insertJogador->bind_param("iss", $jogador_id, $nome, $pos);
                    $insertJogador->execute();
                }
            } else {
                // Se já existe, incrementa pontuação
                $conn->query("UPDATE jogadores SET pontuacao = pontuacao + 1 WHERE id = $jogador_id");
            }
        }

        // Salva o voto
        $stmt = $conn->prepare("INSERT INTO votos_melhor_em_campo (jogador_id, posicao, data_jogo, voto_nulo, ip_votante) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("issis", $jogador_id, $posicao, $data_hoje, $voto_nulo, $ip);
        $stmt->execute();
    }

    header("Location: melhor_em_campo_site.php?ok=1");
    exit();
}

// Buscar jogadores por posição
$jogadores_por_posicao = [];
foreach ($posicoes as $p) {
    $stmt = $conn->prepare("SELECT id, nome FROM presencas WHERE data_jogo = ? AND posicao = ? ORDER BY nome");
    $stmt->bind_param("ss", $data_hoje, $p);
    $stmt->execute();
    $jogadores_por_posicao[$p] = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Top votados por posição
$top = $conn->query("
    SELECT j.nome, j.posicao, COUNT(*) as votos
    FROM votos_melhor_em_campo v
    JOIN jogadores j ON j.id = v.jogador_id
    WHERE v.data_jogo = '$data_hoje' AND v.voto_nulo = 0
    GROUP BY v.posicao, j.id
    ORDER BY v.posicao, votos DESC
");

$top_votos = [];
while ($t = $top->fetch_assoc()) {
    $pos = $t['posicao'];
    if (!isset($top_votos[$pos])) {
        $top_votos[$pos] = $t;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Melhor em Campo</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">
  <div class="container bg-white p-4 rounded shadow-sm" style="max-width: 700px;">
    <h3 class="text-center mb-3">Votação - Melhor em Campo</h3>

    <?php if (isset($_GET['ok'])): ?>
      <div class="alert alert-success text-center">✅ Votos registrados com sucesso!</div>
    <?php elseif ($ja_votou): ?>
      <div class="alert alert-warning text-center">⚠️ Você já votou hoje.</div>
    <?php endif; ?>

    <?php if (!$ja_votou): ?>
    <form method="POST" onsubmit="return validarVoto();">
      <?php foreach ($posicoes as $p): ?>
        <div class="mb-3">
          <label class="form-label"><?= $p ?></label>
          <select name="pos_<?= strtolower($p) ?>" class="form-select" required>
            <option value="">Selecione</option>
            <?php foreach ($jogadores_por_posicao[$p] as $j): ?>
              <option value="<?= $j['id'] ?>"><?= htmlspecialchars($j['nome']) ?></option>
            <?php endforeach; ?>
            <option value="nulo">Voto Nulo</option>
          </select>
        </div>
      <?php endforeach; ?>
      <button class="btn btn-primary w-100 mt-3">Enviar Votos</button>
    </form>
    <?php endif; ?>

    <div class="mt-5">
      <h5 class="text-center mb-3">Mais Votado por Posição (<?= date("d/m/Y", strtotime($data_hoje)) ?>)</h5>
      <ul class="list-group">
        <?php foreach ($posicoes as $p): ?>
          <?php if (isset($top_votos[$p])): ?>
            <li class="list-group-item d-flex justify-content-between">
              <strong><?= $p ?></strong>
              <span><?= htmlspecialchars($top_votos[$p]['nome']) ?> - <?= $top_votos[$p]['votos'] ?> voto(s)</span>
            </li>
          <?php else: ?>
            <li class="list-group-item d-flex justify-content-between">
              <strong><?= $p ?></strong>
              <span class="text-muted">Sem votos</span>
            </li>
          <?php endif; ?>
        <?php endforeach; ?>
      </ul>
    </div>

    <div class="text-center mt-4">
      <a href="../../index.php" class="btn btn-secondary">← Voltar para o site</a>
    </div>
  </div>

  <script>
    function validarVoto() {
      const selects = document.querySelectorAll("select");
      for (let s of selects) {
        if (!s.value) {
          alert("Por favor, preencha todos os votos.");
          return false;
        }
      }
      return true;
    }
  </script>
</body>
</html>
