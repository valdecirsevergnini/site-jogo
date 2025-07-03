<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$patrocinadores = $conn->query("SELECT * FROM patrocinadores ORDER BY nome ASC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Patrocinadores</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
  <h3 class="mb-4 text-center">Patrocinadores</h3>

  <?php if (isset($_GET['ok']) && $_GET['ok'] == 1): ?>
    <div class="alert alert-success text-center">Patrocinador excluído com sucesso!</div>
  <?php endif; ?>

  <div class="mb-3 text-end">
    <a href="novo_patrocinador.php" class="btn btn-success">+ Novo Patrocinador</a>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Voltar ao Painel</a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-striped text-center align-middle">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Imagem</th>
          <th>Nome</th>
          <th>Descrição</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($patrocinadores->num_rows > 0): ?>
          <?php while ($p = $patrocinadores->fetch_assoc()): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td>
                <?php if (!empty($p['imagem'])): ?>
                  <img src="../img/<?= $p['imagem'] ?>" alt="<?= $p['nome'] ?>" height="50">
                <?php else: ?>
                  <span class="text-muted">Sem imagem</span>
                <?php endif; ?>
              </td>
              <td><?= htmlspecialchars($p['nome']) ?></td>
              <td><?= htmlspecialchars($p['descricao']) ?></td>
              <td>
                <a href="editar_patrocinador.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="excluir_patrocinador.php?id=<?= $p['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja realmente excluir este patrocinador?')">Excluir</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-muted">Nenhum patrocinador cadastrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
