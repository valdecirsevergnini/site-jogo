<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$erro = $sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nome = trim($_POST['nome']);
  $descricao = trim($_POST['descricao']);

  if (!empty($nome) && isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
    $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
    $nomeImagem = uniqid() . "." . $ext;

    // Caminho corrigido para salvar na pasta /img (fora de /includes)
    $caminhoImagem = __DIR__ . "/../../img/" . $nomeImagem;

    if (move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoImagem)) {
      $stmt = $conn->prepare("INSERT INTO patrocinadores (nome, imagem, descricao) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $nome, $nomeImagem, $descricao);
      if ($stmt->execute()) {
        $sucesso = "Patrocinador cadastrado com sucesso!";
      } else {
        $erro = "Erro ao salvar no banco de dados.";
      }
    } else {
      $erro = "Erro ao mover o arquivo. Verifique permissões da pasta /img.";
    }
  } else {
    $erro = "Preencha o nome e selecione uma imagem válida.";
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Novo Patrocinador</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
  <div class="container">
    <h3 class="mb-4 text-center">Novo Patrocinador</h3>

    <?php if ($erro): ?>
      <div class="alert alert-danger"><?= $erro ?></div>
    <?php endif; ?>

    <?php if ($sucesso): ?>
      <div class="alert alert-success"><?= $sucesso ?></div>
      <a href="patrocinadores.php" class="btn btn-success">Voltar à Lista</a>
    <?php else: ?>
      <form method="POST" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Nome</label>
          <input type="text" name="nome" class="form-control" required>
        </div>

        <div class="col-md-6">
          <label class="form-label">Imagem</label>
          <input type="file" name="imagem" accept="image/*" class="form-control" required>
        </div>

        <div class="col-12">
          <label class="form-label">Descrição</label>
          <textarea name="descricao" class="form-control" rows="3"></textarea>
        </div>

        <div class="col-12 d-flex gap-2 flex-wrap">
          <button type="submit" class="btn btn-primary">Salvar</button>
          <a href="patrocinadores.php" class="btn btn-secondary">Cancelar</a>
        </div>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
