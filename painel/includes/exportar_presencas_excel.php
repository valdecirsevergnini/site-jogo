<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=presencas_" . date("Y-m-d_H-i-s") . ".xls");
header("Pragma: no-cache");
header("Expires: 0");

$dataSelecionada = $_GET['data'] ?? date('Y-m-d');
$filtro_nome = $_GET['filtro_nome'] ?? '';

$filtro_sql = $filtro_nome ? "AND nome LIKE '%$filtro_nome%'" : '';
$resultado = $conn->query("SELECT nome FROM presencas WHERE data_jogo = '$dataSelecionada' $filtro_sql ORDER BY nome");

echo "<table border='1'>";
echo "<tr><th>Data</th><th>Nome</th></tr>";

while ($row = $resultado->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . date('d/m/Y', strtotime($dataSelecionada)) . "</td>";
    echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
    echo "</tr>";
}

echo "</table>";
