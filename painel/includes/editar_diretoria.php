<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: diretoria.php");
    exit;
}

$erro = $sucesso = "";

// Buscar dados existentes
$diretor = $conn->query("SELECT * FROM diretoria WHERE id = $id")->fetch_assoc();
if (!$diretor) {
    die("<div class='alert alert-danger'>Membro não encontrado.</div>");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $cargo = trim($_POST['cargo']);
    $descricao = trim($_POST['descricao']);
    $foto = $diretor['foto'];

    if (!empty($_FILES['foto']['name']) && $_FILES['foto']['error'] === 0) {
        $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $foto = uniqid() . "." . $ext;
        $caminho = dirname(__DIR__, 2) . "/img/" . $foto;

        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
            $erro = "Erro ao mover nova imagem.";
        } else {
            // remove foto antiga
            $antiga = dirname(__DIR__, 2) . "/img/" . $diretor['foto'];
            if (file_exists($antiga)) unlink($antiga);
        }
    }

    if (!$erro) {
        $stmt = $conn->prepare("UPDATE diretoria SET nome = ?, cargo = ?, descricao = ?, foto = ? WHERE id = ?");
        $stmt->bind_param("ssssi", $nome, $cargo, $descricao, $foto, $id);
        if ($stmt->execute()) {
            $sucesso = "Dados atualizados com sucesso!";
        } else {
            $erro = "Erro ao atualizar o banco de dados.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Membro da Diretoria</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
  <h3 class="mb-4 text-center">Editar Membro da Diretoria</h3>

  <?php if ($erro): ?>
    <div class="alert alert-danger"><?= $erro ?></div>
  <?php elseif ($sucesso): ?>
    <div class="alert alert-success"><?= $sucesso ?></div>
    <a href="diretoria.php" class="btn btn-success">Voltar</a>
  <?php else: ?>
    <form method="POST" enctype="multipart/form-data" class="row g-3">
      <div class="col-md-6">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" value="<?= htmlspecialchars($diretor['nome']) ?>" class="form-control" required>
      </div>
      <div class="col-md-6">
        <label class="form-label">Cargo</label>
        <input type="text" name="cargo" value="<?= htmlspecialchars($diretor['cargo']) ?>" class="form-control" required>
      </div>
      <div class="col-12">
        <label class="form-label">Foto (nova - opcional)</label>
        <input type="file" name="foto" class="form-control">
        <small class="text-muted">Atual: <?= htmlspecialchars($diretor['foto']) ?></small>
      </div>
      <div class="col-12">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control" rows="3"><?= htmlspecialchars($diretor['descricao']) ?></textarea>
      </div>
      <div class="col-12 d-flex gap-2">
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="diretoria.php" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  <?php endif; ?>
</div>
</body>
</html>
