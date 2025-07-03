<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$diretores = $conn->query("SELECT * FROM diretoria ORDER BY nome");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Diretoria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3 p-md-5">
<div class="container">
  <h3 class="mb-4 text-center">Membros da Diretoria</h3>

  <div class="mb-3 text-end">
    <a href="nova_diretoria.php" class="btn btn-success">+ Novo Membro</a>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Voltar ao Painel</a>
  </div>

  <div class="table-responsive">
    <table class="table table-bordered table-striped align-middle text-center">
      <thead class="table-dark">
        <tr>
          <th>Foto</th>
          <th>Nome</th>
          <th>Cargo</th>
          <th>Descrição</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($diretores->num_rows > 0): ?>
          <?php while ($d = $diretores->fetch_assoc()): ?>
            <tr>
              <td><img src="../img/<?= $d['foto'] ?>" alt="<?= $d['nome'] ?>" style="height: 60px;" class="rounded-circle"></td>
              <td><?= htmlspecialchars($d['nome']) ?></td>
              <td><?= htmlspecialchars($d['cargo']) ?></td>
              <td><?= htmlspecialchars($d['descricao']) ?></td>
              <td>
                <a href="editar_diretoria.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning">Editar</a>
                <a href="excluir_diretoria.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir este membro?')">Excluir</a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="5" class="text-muted">Nenhum membro cadastrado.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
</body>
</html>
