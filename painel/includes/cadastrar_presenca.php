<?php
include_once(__DIR__ . "/conexao.php");

date_default_timezone_set('America/Sao_Paulo');

$data_jogo = $_POST['data_jogo'] ?? date('Y-m-d');
$nome = trim($_POST['nome'] ?? '');
$posicao = trim($_POST['posicao'] ?? '');

$hoje = date('Y-m-d');
$hora_atual = date('H:i');
$dia_semana = date('w'); // 6 = sábado

if ($dia_semana == 6 && $hora_atual >= '17:00') {
    echo "<script>
        alert('As confirmações estão encerradas. Prazo final: sábado até às 17h.');
        window.location.href='../../index.php';
    </script>";
    exit;
}

if ($nome !== '' && $posicao !== '') {
    $stmt = $conn->prepare("INSERT INTO presencas (nome, posicao, data_jogo) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nome, $posicao, $data_jogo);
    $stmt->execute();
    $stmt->close();

    // Exibe toast com Bootstrap
    echo '
    <!DOCTYPE html>
    <html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title>Confirmação</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body {
                background-color: #f3f3f3;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
            }
            .toast-container {
                position: fixed;
                top: 1rem;
                right: 1rem;
                z-index: 9999;
            }
        </style>
    </head>
    <body>
        <div class="toast-container">
            <div id="presencaToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        Presença confirmada! ⚽<br>
                        Lembre-se: se não puder ir, avise a diretoria com pelo menos 3h de antecedência.<br>
                        Por bom senso, quem não costuma faltar tem preferência para iniciar na partida. Bom jogo!
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const toastEl = document.getElementById("presencaToast");
            const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
            toast.show();
            setTimeout(() => {
                window.location.href = "../../index.php";
            }, 5200);
        </script>
    </body>
    </html>';
    exit;
} else {
    echo "<script>alert('Erro: Nome e posição são obrigatórios.'); window.location.href='../../index.php';</script>";
}
?>
