<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$id = $_GET['id'] ?? 0;

// Buscar dados atuais do jogador
$sql = "SELECT * FROM jogadores WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();

if (!$dados) {
    echo "Jogador não encontrado.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $posicao = $_POST['posicao'];
    $pontuacao = $_POST['pontuacao'];
    $foto = $dados['foto'] ?: 'avatar_padrao.png';

    // Upload de nova imagem, se houver
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($extensao, $permitidas)) {
            $novo_nome = uniqid() . "." . $extensao;
            $caminho = __DIR__ . "../../img/" . $novo_nome;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
                $foto = $novo_nome;
            }
        }
    }

    $update = $conn->prepare("UPDATE jogadores SET nome = ?, posicao = ?, pontuacao = ?, foto = ? WHERE id = ?");
    $update->bind_param("ssisi", $nome, $posicao, $pontuacao, $foto, $id);
    $update->execute();

    header("Location: jogadores.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Editar Jogador</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #f8f9fa; padding: 2rem; }
    .container { max-width: 600px; }
    .foto-atual {
      width: 80px;
      height: 80px;
      object-fit: cover;
      border-radius: 50%;
    }
  </style>
</head>
<body>
<div class="container">
  <h3>Editar Jogador</h3>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Nome</label>
      <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($dados['nome']) ?>" required>
    </div>

    <div class="mb-3">
      <label class="form-label">Posição</label>
      <select name="posicao" class="form-select" required>
        <?php
        $posicoes = ['Goleiro','Zagueiro','Lateral','Meia','Atacante','Volante'];
        foreach ($posicoes as $p) {
          $selected = ($p === $dados['posicao']) ? 'selected' : '';
          echo "<option value='$p' $selected>$p</option>";
        }
        ?>
      </select>
    </div>

    <div class="mb-3">
      <label class="form-label">Pontuação</label>
      <input type="number" name="pontuacao" class="form-control" value="<?= intval($dados['pontuacao']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label">Foto atual:</label><br>
      <?php
        $foto_exibida = $dados['foto'] ?: 'avatar_padrao.png';
      ?>
      <img src="../img/<?= htmlspecialchars($foto_exibida) ?>" class="foto-atual" alt="Foto do jogador">
    </div>

    <div class="mb-3">
      <label class="form-label">Nova foto (opcional)</label>
      <input type="file" name="foto" class="form-control">
    </div>

    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
    <a href="jogadores.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
</body>
</html>
