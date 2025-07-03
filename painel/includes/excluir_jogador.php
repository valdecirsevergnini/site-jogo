<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");


$id = $_GET['id'] ?? 0;

// Buscar nome da foto antes de excluir
$sql = "SELECT foto FROM jogadores WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_assoc();

if ($dados && $dados['foto']) {
    $foto_path = "../img/" . $dados['foto'];
    if (file_exists($foto_path)) {
        unlink($foto_path); // remove a imagem
    }
}

// Excluir do banco
$del = $conn->prepare("DELETE FROM jogadores WHERE id = ?");
$del->bind_param("i", $id);
$del->execute();

header("Location: jogadores.php");
exit();
