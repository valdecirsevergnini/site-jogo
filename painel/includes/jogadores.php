<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

// Buscar jogadores
$sql = "SELECT * FROM jogadores ORDER BY nome ASC";
$resultado = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Jogadores - Painel</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #f8f9fa; padding: 2rem; }
    .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
    img.foto-jogador {
      width: 50px;
      height: 50px;
      object-fit: cover;
      border-radius: 50%;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="header-bar">
      <h3>Jogadores Cadastrados</h3>
      <a href="cadastrar_jogador.php" class="btn btn-success">+ Adicionar Jogador</a>
    </div>

    <table class="table table-bordered table-striped">
      <thead>
        <tr>
          <th>Foto</th>
          <th>Nome</th>
          <th>Posição</th>
          <th>Pontuação</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php while($row = $resultado->fetch_assoc()): ?>
        <tr>
          <td>
            <?php
              $foto = !empty($row['foto']) ? $row['foto'] : 'avatar_padrao.png';
              $caminhoFoto = "../../img/" . $foto; // CORRIGIDO: estamos na pasta "includes"
            ?>
            <img src="<?= htmlspecialchars($caminhoFoto) ?>" alt="Foto" class="foto-jogador"
                 onerror="this.onerror=null; this.src='../img/avatar_padrao.png'">
          </td>
          <td><?= htmlspecialchars($row['nome']) ?></td>
          <td><?= htmlspecialchars($row['posicao']) ?></td>
          <td><?= intval($row['pontuacao']) ?></td>
          <td>
            <a href="editar_jogador.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Editar</a>
            <a href="excluir_jogador.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir jogador?');">Excluir</a>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <div class="text-center mt-4">
  <a href="https://fenixdev.tech/boleiros/painel/includes/dashboard.php" class="btn btn-secondary">← Voltar ao Menu Principal</a>
</div>


</body>
</html>
