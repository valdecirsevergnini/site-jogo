<?php include_once("painel/includes/conexao.php"); ?>
<?php
$data_hoje = date("Y-m-d");

// Presenças
$presencas = $conn->query("SELECT nome FROM presencas WHERE data_jogo = '$data_hoje' ORDER BY nome");
$total_presenca = $presencas->num_rows;

// Sorteio
$sorteio = $conn->query("SELECT nome, posicao, time FROM sorteios WHERE data = '$data_hoje'");
$azul = $preto = [];
while ($row = $sorteio->fetch_assoc()) {
    if ($row['time'] === 'Azul') {
        $azul[] = ['nome' => $row['nome'], 'posicao' => $row['posicao']];
    } elseif ($row['time'] === 'Preto') {
        $preto[] = ['nome' => $row['nome'], 'posicao' => $row['posicao']];
    }
}



// Diretoria
$diretoria = $conn->query("SELECT * FROM diretoria");

// Patrocinadores
$patrocinadores = $conn->query("SELECT * FROM patrocinadores");

// Jogadores para Ranking
$jogadores = $conn->query("SELECT * FROM jogadores ORDER BY pontuacao DESC");

// Top 3 Melhor em Campo
$melhores = $conn->query("SELECT j.nome, COUNT(*) as votos 
                          FROM melhores_em_campo m 
                          JOIN jogadores j ON j.id = m.jogador_id 
                          GROUP BY j.nome 
                          ORDER BY votos DESC 
                          LIMIT 3");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Boleiros de Sábado</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --cor-fundo: #f5f5f5;
      --cor-destaque: #fff3e0;
      --cor-borda: #d4af37;
      --cor-escura: #000;
    }
    body { background: var(--cor-fundo); color: #222; font-family: 'Segoe UI', sans-serif; }
    header, footer { background: var(--cor-escura); color: #fff; padding: 1rem; text-align: center; }
    .logo { max-width: 160px; }
    .section-box { background: var(--cor-destaque); border: 2px solid var(--cor-borda); border-radius: 10px; padding: 1rem; margin-bottom: 2rem; }
    .ranking-card .card { border: 1px solid #ccc; }
    .carousel-item img { max-height: 100px; object-fit: contain; }
    .list-group-item { font-size: 0.95rem; }
    .ranking-card .card img { height: 100px; object-fit: cover; border-radius: 50%; margin: auto; margin-top: 10px; }

    #carouselAlbum img:hover {
  transform: scale(1.05);
  transition: transform 0.3s ease;
}

  </style>
</head>
<body>
<header class="bg-dark text-white px-4 py-3">
  <div class="container-fluid">
    <div class="row align-items-center">
      
      <!-- Logo à esquerda -->
      <div class="col-4 text-start">
        <img src="./img/logo.png" alt="Boleiros de Sábado" class="logo" style="max-width: 140px;">
      </div>

      <!-- Título centralizado -->
      <div class="col-4 text-center">
        <h1 class="h4 mb-0">Boleiros de Sábado</h1>
        <small>🍺⚽🍺⚽🍺</small>
      </div>

      <!-- Botões à direita -->
      <div class="col-4 text-end">
        <a href="painel/includes/login.php" class="btn btn-outline-light btn-sm mb-2">Painel Administrativo</a><br>
        <a href="sobre_time.php" class="btn btn-outline-light btn-sm">Sobre o Time</a>
      </div>

    </div>
  </div>
</header>


  <main class="container py-4">
    <h1 class="text-center">Bem-vindo ao Boleiros de Sábado!</h1>
    <p class="text-center">Aqui você encontra tudo sobre nosso time, desde resultados até eventos especiais.</p>

 <!-- Enquete -->
<section class="section-box">
  <h2 class="text-center mb-4">Enquete da Semana</h2>
  <div class="row">
    <!-- Resultados -->
    <div class="col-md-6 mb-3" id="resultadoEnquete">
      <?php
        $enquete = $conn->query("SELECT * FROM enquetes WHERE ativa = 1 ORDER BY data_criacao DESC LIMIT 1")->fetch_assoc();
        if ($enquete):
          $opcoes_resultado = $conn->query("SELECT * FROM opcoes_enquete WHERE enquete_id = {$enquete['id']}");
          $total_votos = 0;
          $dados = [];
          while ($op = $opcoes_resultado->fetch_assoc()) {
            $dados[] = $op;
            $total_votos += $op['votos'];
          }
      ?>
        <h5>Parciais</h5>
        <ul class="list-group">
          <?php foreach ($dados as $op):
            $porcentagem = ($total_votos > 0) ? round(($op['votos'] / $total_votos) * 100, 1) : 0;
          ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <?= htmlspecialchars($op['opcao']) ?>
              <span class="badge bg-primary"><?= $porcentagem ?>% (<?= $op['votos'] ?>)</span>
            </li>
          <?php endforeach; ?>
        </ul>
        <p class="mt-2 text-muted">Total de votos: <strong><?= $total_votos ?></strong></p>
      <?php else: ?>
        <p>Não há enquete ativa.</p>
      <?php endif; ?>
    </div>

    <!-- Formulário -->
    <div class="col-md-6 text-center">
      <?php if ($enquete): ?>
        <p><strong><?= $enquete['pergunta'] ?></strong></p>
        <form id="formEnquete" class="d-inline-block text-start">
          <?php
            $opcoes = $conn->query("SELECT * FROM opcoes_enquete WHERE enquete_id = {$enquete['id']}");
            while ($row = $opcoes->fetch_assoc()):
          ?>
            <div class="form-check">
              <input class="form-check-input" type="radio" name="opcao" value="<?= $row['id'] ?>" id="op<?= $row['id'] ?>" required>
              <label class="form-check-label" for="op<?= $row['id'] ?>"><?= htmlspecialchars($row['opcao']) ?></label>
            </div>
          <?php endwhile; ?>
          <button type="submit" class="btn btn-dark mt-2">Votar</button>
        </form>
        <div id="mensagemEnquete" class="mt-3"></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<script>
document.getElementById('formEnquete').addEventListener('submit', function(e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  fetch('painel/includes/votar_enquete_site.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      document.getElementById('resultadoEnquete').innerHTML = data.html;
    } else {
      alert("Erro ao enviar voto: " + (data.message || 'tente novamente.'));
    }
  })
  .catch(() => alert("Erro ao processar voto."));
});
</script>

<!-- Ranking por Posição (sem mostrar votos nulos como jogador) -->
<section class="section-box">
  <h2 class="text-center mb-4">Ranking por Posição</h2>
  <div class="row ranking-card">
    <?php
    $ranking = $conn->query("
      SELECT j.*, 
             (SELECT COUNT(*) FROM votos_melhor_em_campo v WHERE v.jogador_id = j.id) AS total_votos
      FROM jogadores j 
      WHERE ativo = 1 AND j.nome NOT LIKE 'Voto Nulo%' 
      ORDER BY j.posicao, j.nome
    ");
    while ($j = $ranking->fetch_assoc()):
      $caminhoFisico = __DIR__ . "/img/" . $j['foto'];
      $foto = (!empty($j['foto']) && file_exists($caminhoFisico) && is_file($caminhoFisico)) ? $j['foto'] : 'avatar_padrao.png';
    ?>
      <div class="col-6 col-md-3 col-lg-2 mb-3">
        <div class="card text-center shadow-sm">
          <img src="img/<?= htmlspecialchars($foto) ?>" alt="<?= htmlspecialchars($j['nome']) ?>" class="rounded-circle mt-2" style="height: 90px; object-fit: cover;">
          <div class="card-body p-2">
            <h6 class="mb-0"><?= htmlspecialchars($j['nome']) ?></h6>
            <p class="text-muted small mb-1"><?= htmlspecialchars($j['posicao']) ?></p>
            <span class="badge bg-success mb-1"><?= intval($j['pontuacao']) ?> ponto(s)</span><br>
            <span class="badge bg-primary"><?= intval($j['total_votos']) ?> voto(s)</span>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
  <div class="text-center mt-3">
    <a href="painel/includes/melhor_em_campo_site.php" class="btn btn-warning">Votar Melhor em Campo</a>
  </div>
</section>


<!-- Melhores do Ano - Um por Posição -->
<section class="section-box">
  <h2 class="text-center mb-4">Melhores do Ano (Um por Posição)</h2>
  <div class="row ranking-card">
    <?php
    $query = "
      SELECT j1.*
      FROM jogadores j1
      INNER JOIN (
          SELECT posicao, MAX(pontuacao) AS max_pontuacao
          FROM jogadores
          WHERE ativo = 1 AND nome NOT LIKE 'Voto Nulo%'
          GROUP BY posicao
      ) j2 ON j1.posicao = j2.posicao AND j1.pontuacao = j2.max_pontuacao
      WHERE j1.ativo = 1 AND j1.nome NOT LIKE 'Voto Nulo%'
      GROUP BY j1.posicao
      ORDER BY j1.posicao
    ";

    $melhores_ano = $conn->query($query);

    while ($j = $melhores_ano->fetch_assoc()):
      $caminhoFisico = __DIR__ . "/img/" . $j['foto'];
      $foto = (!empty($j['foto']) && file_exists($caminhoFisico) && is_file($caminhoFisico)) ? $j['foto'] : 'avatar_padrao.png';
    ?>
      <div class="col-6 col-md-4 col-lg-2 mb-3">
        <div class="card text-center shadow-sm">
          <img src="img/<?= htmlspecialchars($foto) ?>" class="rounded-circle mt-2" style="height: 90px; object-fit: cover;">
          <div class="card-body p-2">
            <h6 class="mb-0"><?= htmlspecialchars($j['nome']) ?></h6>
            <p class="text-muted small mb-1"><?= htmlspecialchars($j['posicao']) ?></p>
            <span class="badge bg-warning text-dark"><?= intval($j['pontuacao']) ?> ponto(s)</span>
          </div>
        </div>
      </div>
    <?php endwhile; ?>
  </div>
</section>

<!-- Melhores em Campo por Posição (último jogo real) -->
<section class="section-box mt-5">
  <h2 class="text-center mb-4">Melhor em Campo - Um por Posição</h2>
  <?php
    // Busca a data mais recente com votos válidos
    $ultimaData = $conn->query("
      SELECT MAX(data_jogo) as data_ultima
      FROM votos_melhor_em_campo
      WHERE voto_nulo = 0
    ")->fetch_assoc()['data_ultima'];

    $vencedores_por_posicao = [];

    if ($ultimaData) {
      $query = "
        SELECT j.id, j.nome, j.posicao, j.foto, COUNT(v.id) AS votos
        FROM votos_melhor_em_campo v
        JOIN jogadores j ON j.id = v.jogador_id
        WHERE v.data_jogo = '$ultimaData' AND v.voto_nulo = 0
        GROUP BY j.id, j.nome, j.posicao, j.foto
        ORDER BY j.posicao, votos DESC
      ";
      $resultado = $conn->query($query);

      while ($m = $resultado->fetch_assoc()) {
        $pos = $m['posicao'];
        if (!isset($vencedores_por_posicao[$pos])) {
          $vencedores_por_posicao[$pos] = $m;
        }
      }
    }
  ?>

  <?php if (count($vencedores_por_posicao) > 0): ?>
    <div class="text-center mb-3">
      <strong>Último jogo em: <?= date("d/m/Y", strtotime($ultimaData)) ?></strong>
    </div>
    <div class="row justify-content-center">
      <?php foreach ($vencedores_por_posicao as $j):
        $caminhoFisico = __DIR__ . "/img/" . $j['foto'];
        $foto = (!empty($j['foto']) && file_exists($caminhoFisico) && is_file($caminhoFisico)) ? $j['foto'] : 'avatar_padrao.png';
      ?>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
          <div class="card text-center shadow-sm">
            <img src="img/<?= htmlspecialchars($foto) ?>" class="card-img-top rounded-circle mx-auto mt-3" style="height: 90px; width: 90px; object-fit: cover;" alt="<?= htmlspecialchars($j['nome']) ?>">
            <div class="card-body p-2">
              <h6 class="mb-0"><?= htmlspecialchars($j['nome']) ?></h6>
              <small class="text-muted"><?= htmlspecialchars($j['posicao']) ?></small><br>
              <span class="badge bg-primary mt-1"><?= $j['votos'] ?> voto(s)</span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <p class="text-muted text-center">Nenhum voto registrado ainda em jogos anteriores.</p>
  <?php endif; ?>
</section>

    <!-- Presença -->
    <?php
$data_hoje = date("Y-m-d");
$presencas = $conn->query("SELECT nome FROM presencas WHERE data_jogo = '$data_hoje' ORDER BY nome");
$total_presenca = $presencas->num_rows;
?>

<!-- Bloco visual da presença -->
<section class="section-box">
  <h2>Quem vai no Sábado? <span class="badge bg-dark"><?= $total_presenca ?> confirmados</span></h2>

  <!-- Lista de confirmados com posição -->
<ul class="list-group list-group-flush mb-3">
  <?php
    $presencas = $conn->query("SELECT nome, posicao FROM presencas WHERE data_jogo = '$data_hoje' ORDER BY nome");
    while ($p = $presencas->fetch_assoc()):
  ?>
    <li class="list-group-item d-flex justify-content-between">
      <span><?= htmlspecialchars($p['nome']) ?></span>
      <span class="badge bg-secondary"><?= htmlspecialchars($p['posicao']) ?></span>
    </li>
  <?php endwhile; ?>
</ul>


  <!-- Formulário de confirmação com posição -->
<form method="POST" action="painel/includes/cadastrar_presenca.php" class="row g-2 align-items-end">
  <input type="hidden" name="data_jogo" value="<?= $data_hoje ?>">

  <div class="col-12 col-md-5">
    <label class="form-label">Nome</label>
    <input type="text" name="nome" placeholder="Digite seu nome" class="form-control" required>
  </div>

  <div class="col-12 col-md-4">
    <label class="form-label">Posição</label>
    <select name="posicao" class="form-select" required>
      <option value="">Selecione</option>
      <option value="Goleiro">Goleiro</option>
      <option value="Zagueiro">Zagueiro</option>
      <option value="Lateral">Lateral</option>
      <option value="Volante">Volante</option>
      <option value="Meia">Meia</option>
      <option value="Atacante">Atacante</option>
    </select>
  </div>

  <div class="col-12 col-md-3">
    <button type="submit" class="btn btn-primary w-100">Confirmar</button>
  </div>
</form>

</section>


    <!-- Sorteio -->
    <section class="section-box">
  <h2>Sorteio do Dia</h2>
  <?php if (count($azul) + count($preto) > 0): ?>
    <div class="row">
      <div class="col">
        <h5 class="text-info">Time Azul</h5>
        <ul class="list-group">
          <?php foreach ($azul as $j): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?= htmlspecialchars($j['nome']) ?></span>
              <span class="badge bg-secondary"><?= htmlspecialchars($j['posicao']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="col">
        <h5 class="text-secondary">Time Preto</h5>
        <ul class="list-group">
          <?php foreach ($preto as $j): ?>
            <li class="list-group-item d-flex justify-content-between">
              <span><?= htmlspecialchars($j['nome']) ?></span>
              <span class="badge bg-secondary"><?= htmlspecialchars($j['posicao']) ?></span>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>
  <?php else: ?>
    <p class="text-muted">O sorteio ainda não foi realizado hoje.</p>
  <?php endif; ?>
</section>


   <!-- Patrocinadores (Carrossel) -->
<section class="section-box bg-white shadow-lg">
  <h2 class="text-center mb-4 fw-bold">Patrocinadores</h2>
  <div id="carouselPatrocinadores" class="carousel slide carousel-fade" data-bs-ride="carousel">
    <div class="carousel-inner">
      <?php
      $patrocinadores->data_seek(0); // Reinicia ponteiro
      $patrocinador_list = $patrocinadores->fetch_all(MYSQLI_ASSOC);
      $chunks = array_chunk($patrocinador_list, 6); // 6 por slide
      foreach ($chunks as $index => $grupo): ?>
        <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
          <div class="d-flex justify-content-center flex-wrap gap-4 animate-slide">
            <?php foreach ($grupo as $pat): ?>
              <div class="text-center">
                <img src="img/<?= $pat['imagem'] ?>" alt="<?= $pat['nome'] ?>" class="img-fluid rounded shadow-sm" style="height: 90px; object-fit: contain;">
                <p class="mt-2 mb-0 fw-semibold small text-dark"><?= $pat['nome'] ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselPatrocinadores" data-bs-slide="prev">
      <span class="carousel-control-prev-icon bg-dark rounded-circle" aria-hidden="true"></span>
      <span class="visually-hidden">Anterior</span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselPatrocinadores" data-bs-slide="next">
      <span class="carousel-control-next-icon bg-dark rounded-circle" aria-hidden="true"></span>
      <span class="visually-hidden">Próximo</span>
    </button>
  </div>
</section>

<!-- Estilo para degradê, animações e responsividade -->
<style>
  body {
    background: linear-gradient(to right, #f5f7fa, #c3cfe2);
  }
  .section-box {
    background: rgba(255, 255, 255, 0.95);
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    border: none;
  }
  .animate-slide {
    animation: fadeIn 0.7s ease-in-out;
  }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
  }
</style>

<!-- Ativa o carrossel automaticamente -->
<script>
  const carouselElement = document.querySelector('#carouselPatrocinadores');
  if (carouselElement) {
    new bootstrap.Carousel(carouselElement, {
      interval: 4000,
      ride: 'carousel',
      pause: false,
      wrap: true
    });
  }
</script>



    <!-- Diretoria -->
    <section class="section-box">
      <h2>Diretoria</h2>
      <div class="row">
        <?php while ($d = $diretoria->fetch_assoc()): ?>
          <div class="col-md-6 text-center">
            <img src="img/<?= $d['foto'] ?>" class="rounded-circle mb-2" style="width: 120px;">
            <h5><?= $d['nome'] ?></h5>
            <p class="text-muted small"><?= $d['cargo'] ?></p>
            <p><?= $d['descricao'] ?></p>
          </div>
        <?php endwhile; ?>
      </div>
    </section>

    <!-- Álbum de Fotos -->
<section class="section-box">
  <h2 class="text-center mb-4">Álbum de Fotos</h2>
  <div id="carouselAlbum" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <?php
      $fotos = $conn->query("SELECT * FROM album_fotos ORDER BY data_upload DESC");
      $fotos_lista = $fotos->fetch_all(MYSQLI_ASSOC);
      $grupos = array_chunk($fotos_lista, 6);
      foreach ($grupos as $i => $grupo): ?>
        <div class="carousel-item <?= $i === 0 ? 'active' : '' ?>">
          <div class="row g-3 justify-content-center">
            <?php foreach ($grupo as $foto): ?>
              <div class="col-6 col-md-4 col-lg-2 text-center">
                <img src="img/<?= $foto['imagem'] ?>" alt="<?= $foto['descricao'] ?>" class="img-fluid rounded shadow-sm" style="height: 100px; object-fit: cover;">
                <p class="small mt-1"><?= htmlspecialchars($foto['descricao']) ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#carouselAlbum" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#carouselAlbum" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>
</section>


  </main>
  <footer>
  <p>
  <a href="https://wa.me/5554999333305" target="_blank" style="color: #fff; text-decoration: none;">
    FenixDev &copy; Todos os direitos reservados
  </a>
</p>

  </footer>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  const carouselAlbum = document.querySelector('#carouselAlbum');
  if (carouselAlbum) {
    new bootstrap.Carousel(carouselAlbum, {
      interval: 5000,
      ride: 'carousel'
    });
  }
</script>
<!-- Botão WhatsApp Fixo -->
<!-- Botão WhatsApp Fixo -->
<a href="https://wa.me/5554991399381" class="whatsapp-float" target="_blank" title="Fale conosco no WhatsApp">
  <img src="img/whatsapp-icon.svg" alt="WhatsApp" />
</a>

<style>
    .whatsapp-float {
  position: fixed;
  width: 60px;
  height: 60px;
  bottom: 20px;
  right: 20px;
  background-color: rgba(37, 211, 102, 0.6); /* Verde claro com transparência */
  border-radius: 50%;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background-color 0.3s ease;
}

.whatsapp-float:hover {
  background-color: #25d366; /* Verde WhatsApp sólido */
}

.whatsapp-float img {
  width: 32px;
  height: 32px;
}

</style>



</body>
</html>
