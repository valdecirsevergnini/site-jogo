<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $cargo = trim($_POST['cargo']);
    $descricao = trim($_POST['descricao']);
    $foto = '';

    if (!empty($nome) && !empty($cargo) && isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = uniqid() . "." . $ext;

        // Caminho absoluto seguro
        $destino = dirname(__DIR__, 2) . "/img/" . $foto;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
            $stmt = $conn->prepare("INSERT INTO diretoria (nome, cargo, descricao, foto) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $nome, $cargo, $descricao, $foto);
            if ($stmt->execute()) {
                $sucesso = "Membro da diretoria cadastrado com sucesso!";
            } else {
                $erro = "Erro ao salvar no banco de dados.";
            }
        } else {
            $erro = "Erro ao mover a imagem.";
        }
    } else {
        $erro = "Preencha todos os campos e selecione uma imagem.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Novo Membro da Diretoria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
  <h3 class="mb-4 text-center">Novo Membro da Diretoria</h3>

  <?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
  <?php elseif ($sucesso): ?>
    <div class="alert alert-success"><?= $sucesso ?></div>
    <a href="diretoria.php" class="btn btn-success">Voltar à Lista</a>
  <?php else: ?>
    <form method="POST" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Cargo</label>
        <input type="text" name="cargo" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label">Foto</label>
        <input type="file" name="foto" accept="image/*" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control" rows="3"></textarea>
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="diretoria.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
