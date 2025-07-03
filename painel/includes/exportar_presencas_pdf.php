<?php
require_once __DIR__ . '/../../vendor/autoload.php';




include_once(__DIR__ . "/conexao.php");

use Dompdf\Dompdf;
use Dompdf\Options;

$dataSelecionada = $_GET['data'] ?? date('Y-m-d');
$filtro_nome = $_GET['filtro_nome'] ?? '';

$query = "SELECT * FROM presencas WHERE data_jogo = '$dataSelecionada'";
if ($filtro_nome) {
    $query .= " AND nome LIKE '%$filtro_nome%'";
}
$query .= " ORDER BY nome";

$resultado = $conn->query($query);

// Gerar HTML para o PDF
$html = "<h2 style='text-align:center;'>Lista de Presença - " . date('d/m/Y', strtotime($dataSelecionada)) . "</h2>";
$html .= "<table border='1' width='100%' cellspacing='0' cellpadding='5'>";
$html .= "<thead><tr><th>#</th><th>Nome</th></tr></thead><tbody>";

$cont = 1;
while ($row = $resultado->fetch_assoc()) {
    $html .= "<tr><td>$cont</td><td>" . htmlspecialchars($row['nome']) . "</td></tr>";
    $cont++;
}
$html .= "</tbody></table>";

if ($cont == 1) {
    $html .= "<p style='text-align:center;'>Nenhuma presença registrada para esta data.</p>";
}

// Configurações DomPDF
$options = new Options();
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Forçar download
$dompdf->stream("presencas_" . $dataSelecionada . ".pdf", ["Attachment" => true]);
exit;
