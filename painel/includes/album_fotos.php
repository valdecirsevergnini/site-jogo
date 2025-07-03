<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$fotos = $conn->query("SELECT * FROM album_fotos ORDER BY data_upload DESC");
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Álbum de Fotos</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-3 p-md-5">
<div class="container">
  <h3 class="mb-4 text-center">Álbum de Fotos</h3>

  <div class="mb-3 text-end">
    <a href="nova_foto.php" class="btn btn-success">+ Nova Foto</a>
    <a href="dashboard.php" class="btn btn-secondary">⬅ Voltar ao Painel</a>
  </div>

  <?php if ($fotos->num_rows > 0): ?>
    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4">
      <?php while ($f = $fotos->fetch_assoc()): ?>
        <div class="col">
          <div class="card shadow-sm h-100">
            <img src="../img/<?= $f['imagem'] ?>" class="card-img-top" alt="Foto" style="height: 200px; object-fit: cover;">
            <div class="card-body">
              <p class="card-text"><?= htmlspecialchars($f['descricao']) ?></p>
              <div class="text-end">
                <a href="excluir_foto.php?id=<?= $f['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Deseja excluir esta foto?')">Excluir</a>
              </div>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    </div>
  <?php else: ?>
    <div class="alert alert-info text-center">Nenhuma foto cadastrada ainda.</div>
  <?php endif; ?>
</div>
</body>
</html>
