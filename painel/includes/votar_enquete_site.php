<?php
include_once(__DIR__ . "/conexao.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['opcao'])) {
    $opcao_id = intval($_POST['opcao']);

    // Verifica se a opção existe
    $opcao_res = $conn->query("SELECT enquete_id FROM opcoes_enquete WHERE id = $opcao_id");
    if (!$opcao_res || $opcao_res->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Opção inválida.']);
        exit;
    }

    // Atualiza o voto
    $conn->query("UPDATE opcoes_enquete SET votos = votos + 1 WHERE id = $opcao_id");

    $opcao = $opcao_res->fetch_assoc();
    $enquete_id = $opcao['enquete_id'];

    $opcoes = $conn->query("SELECT * FROM opcoes_enquete WHERE enquete_id = $enquete_id");
    $total = 0;
    $dados = [];

    while ($o = $opcoes->fetch_assoc()) {
        $dados[] = $o;
        $total += $o['votos'];
    }

    ob_start(); ?>
    <h5>Parciais</h5>
    <ul class="list-group">
      <?php foreach ($dados as $op):
        $pct = $total > 0 ? round(($op['votos'] / $total) * 100, 1) : 0;
      ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?= htmlspecialchars($op['opcao']) ?>
          <span class="badge bg-primary"><?= $pct ?>% (<?= $op['votos'] ?>)</span>
        </li>
      <?php endforeach; ?>
    </ul>
    <p class="mt-2 text-muted">Total de votos: <strong><?= $total ?></strong></p>
    <?php $html = ob_get_clean();

    echo json_encode([
      'success' => true,
      'html' => $html
    ]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Requisição inválida ou parâmetro ausente.']);
