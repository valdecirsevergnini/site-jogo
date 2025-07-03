<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header("Location: patrocinadores.php");
    exit;
}

$erro = $sucesso = "";
$patrocinador = $conn->query("SELECT * FROM patrocinadores WHERE id = $id")->fetch_assoc();
if (!$patrocinador) {
    die("<div class='alert alert-danger'>Patrocinador não encontrado.</div>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);
    $imagem_nome = $patrocinador['imagem'];

    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === 0) {
        $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
        $imagem_nome = uniqid() . "." . $ext;

        $caminhoImagem = __DIR__ . "/../../img/" . $imagem_nome;

        if (!move_uploaded_file($_FILES['imagem']['tmp_name'], $caminhoImagem)) {
            $erro = "Erro ao salvar a nova imagem.";
        }
    }

    if (!$erro) {
        $stmt = $conn->prepare("UPDATE patrocinadores SET nome = ?, imagem = ?, descricao = ? WHERE id = ?");
        $stmt->bind_param("sssi", $nome, $imagem_nome, $descricao, $id);

        if ($stmt->execute()) {
            $sucesso = "Dados atualizados com sucesso!";
            $patrocinador = $conn->query("SELECT * FROM patrocinadores WHERE id = $id")->fetch_assoc(); // recarrega os dados atualizados
        } else {
            $erro = "Erro ao atualizar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Editar Patrocinador</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
  <h3 class="mb-4 text-center">Editar Patrocinador</h3>

  <?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
  <?php elseif ($sucesso): ?>
    <div class="alert alert-success"><?= $sucesso ?></div>
    <a href="patrocinadores.php" class="btn btn-success">Voltar</a>
  <?php endif; ?>

  <?php if (!$sucesso): ?>
    <form method="POST" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($patrocinador['nome']) ?>" class="form-control" required>
      </div>

      <div class="col-md-6">
        <label class="form-label">Nova Imagem (opcional)</label>
        <input type="file" name="imagem" class="form-control">
        <?php if (!empty($patrocinador['imagem'])): ?>
          <div class="mt-2">
            <img src="../../img/<?= $patrocinador['imagem'] ?>" alt="Imagem atual" height="50">
            <small class="text-muted ms-2"><?= $patrocinador['imagem'] ?></small>
          </div>
        <?php endif; ?>
      </div>

      <div class="col-12">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($patrocinador['descricao']) ?></textarea>
      </div>

      <div class="col-12 d-flex gap-2 flex-wrap">
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="patrocinadores.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
