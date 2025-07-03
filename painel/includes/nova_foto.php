<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$erro = $sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $descricao = trim($_POST['descricao']);
  $data_upload = date('Y-m-d H:i:s');

  if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $nomeImagem = uniqid() . "." . $ext;
    $caminho = __DIR__ . "/../../img/" . $nomeImagem; // Corrigido para caminho absoluto

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminho)) {
      $stmt = $conn->prepare("INSERT INTO album_fotos (imagem, descricao, data_upload) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $nomeImagem, $descricao, $data_upload);
      if ($stmt->execute()) {
        $sucesso = "Foto adicionada com sucesso!";
      } else {
        $erro = "Erro ao salvar no banco de dados.";
      }
    } else {
      $erro = "Erro ao mover a imagem.";
    }
  } else {
    $erro = "Selecione uma imagem válida.";
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Nova Foto</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
  <h3 class="mb-4 text-center">Nova Foto do Álbum</h3>

  <?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
  <?php endif; ?>

  <?php if ($sucesso): ?>
    <div class="alert alert-success"><?= $sucesso ?></div>
    <a href="album_fotos.php" class="btn btn-success">Voltar ao Álbum</a>
  <?php else: ?>
    <form method="POST" enctype="multipart/form-data" class="row g-3">
      <div class="col-12">
        <label class="form-label">Foto</label>
        <input type="file" name="imagem" class="form-control" accept="image/*" required>
      </div>
      <div class="col-12">
        <label class="form-label">Descrição (opcional)</label>
        <textarea name="descricao" class="form-control" rows="2" placeholder="Breve descrição da imagem"></textarea>
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="album_fotos.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
