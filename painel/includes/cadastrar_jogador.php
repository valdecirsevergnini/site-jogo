<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $posicao = $_POST['posicao'];
    $pontuacao = $_POST['pontuacao'];
    $foto = 'avatar_padrao.png'; // fallback padrão

    // Upload da imagem
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $extensao = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        $permitidas = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extensao, $permitidas)) {
            $novo_nome = uniqid() . "." . $extensao;
            $caminhoDestino = __DIR__ . "../../img/" . $novo_nome;

            if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminhoDestino)) {
                $foto = $novo_nome;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO jogadores (nome, posicao, pontuacao, foto) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $nome, $posicao, $pontuacao, $foto);
    $stmt->execute();

    header("Location: jogadores.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastrar Jogador</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #f8f9fa; padding: 2rem; }
    .container { max-width: 600px; }
  </style>
</head>
<body>
<div class="container">
  <h3>Adicionar Novo Jogador</h3>
  <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
      <label class="form-label">Nome</label>
      <input type="text" name="nome" class="form-control" required>
    </div>
    <div class="mb-3">
      <label class="form-label">Posição</label>
      <select name="posicao" class="form-select" required>
        <option value="Goleiro">Goleiro</option>
        <option value="Zagueiro">Zagueiro</option>
        <option value="Lateral">Lateral</option>
        <option value="Meia">Meia</option>
        <option value="Atacante">Atacante</option>
        <option value="Volante">Volante</option>
      </select>
    </div>
    <div class="mb-3">
      <label class="form-label">Pontuação inicial</label>
      <input type="number" name="pontuacao" class="form-control" value="0">
    </div>
    <div class="mb-3">
      <label class="form-label">Foto do jogador</label>
      <input type="file" name="foto" class="form-control">
    </div>
    <button type="submit" class="btn btn-success">Cadastrar</button>
    <a href="jogadores.php" class="btn btn-secondary">Voltar</a>
  </form>
</div>
</body>
</html>
