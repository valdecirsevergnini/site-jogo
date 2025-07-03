<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

date_default_timezone_set("America/Sao_Paulo");

$dataSelecionada = $_GET['data'] ?? date('Y-m-d');

// Excluir voto
if (isset($_GET['excluir'])) {
    $idExcluir = intval($_GET['excluir']);
    $conn->query("DELETE FROM votos_melhor_em_campo WHERE id = $idExcluir");
    header("Location: melhor_em_campo_painel.php?data=$dataSelecionada");
    exit;
}

// Datas disponíveis
$datas = $conn->query("SELECT DISTINCT data_jogo FROM votos_melhor_em_campo ORDER BY data_jogo DESC");

// Consulta de votos detalhados
$votos = $conn->query("
    SELECT 
        v.id,
        v.ip_votante,
        v.data_jogo,
        v.posicao,
        v.voto_nulo,
        j.nome,
        j.foto,
        (SELECT COUNT(*) 
         FROM votos_melhor_em_campo 
         WHERE jogador_id = v.jogador_id 
         AND data_jogo = '$dataSelecionada' 
         AND posicao = v.posicao) as total_votos
    FROM votos_melhor_em_campo v
    LEFT JOIN jogadores j ON j.id = v.jogador_id
    WHERE v.data_jogo = '$dataSelecionada'
    ORDER BY v.posicao, j.nome
");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Votos Melhor em Campo - Painel</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light p-4">
<div class="container">
  <h3 class="mb-4">Painel - Votos Melhor em Campo</h3>

  <!-- Filtro por data -->
  <form method="GET" class="row mb-4">
    <div class="col-md-6">
      <label class="form-label">Filtrar por data</label>
      <select name="data" class="form-select" onchange="this.form.submit()">
        <?php while ($d = $datas->fetch_assoc()): ?>
          <option value="<?= $d['data_jogo'] ?>" <?= ($dataSelecionada === $d['data_jogo']) ? 'selected' : '' ?>>
            <?= date('d/m/Y', strtotime($d['data_jogo'])) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
  </form>

  <!-- Tabela de votos -->
  <div class="card shadow-sm">
    <div class="card-header bg-warning fw-bold">
      Votos do dia <?= date('d/m/Y', strtotime($dataSelecionada)) ?>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-striped mb-0 align-middle">
        <thead class="table-dark">
          <tr>
            <th>#</th>
            <th>Jogador</th>
            <th>Foto</th>
            <th>Posição</th>
            <th>Total de Votos</th>
            <th>IP do Votante</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($votos->num_rows > 0): ?>
            <?php while ($v = $votos->fetch_assoc()): ?>
              <tr>
                <td><?= $v['id'] ?></td>
                <td>
                  <?= $v['voto_nulo'] ? '<span class="text-muted">Voto Nulo</span>' : htmlspecialchars($v['nome']) ?>
                </td>
                <td>
                  <?php if (!$v['voto_nulo']): ?>
                    <img src="../img/<?= htmlspecialchars($v['foto']) ?>" alt="<?= htmlspecialchars($v['nome']) ?>" style="height: 45px; width: 45px; border-radius: 50%; object-fit: cover;">
                  <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($v['posicao']) ?></td>
                <td><span class="badge bg-primary"><?= intval($v['total_votos']) ?> voto(s)</span></td>
                <td><?= htmlspecialchars($v['ip_votante']) ?></td>
                <td>
                  <a href="?data=<?= $dataSelecionada ?>&excluir=<?= $v['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir este voto?')">Excluir</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center">Nenhum voto registrado nesta data.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Voltar -->
  <div class="mt-4">
    <a href="dashboard.php" class="btn btn-secondary">Voltar ao Painel</a>
  </div>
</div>
</body>
</html>
