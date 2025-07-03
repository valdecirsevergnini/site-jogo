<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Buscar a imagem para deletar do diretório
    $busca = $conn->query("SELECT foto FROM diretoria WHERE id = $id");
    $diretor = $busca->fetch_assoc();

    // Exclui o registro do banco
    $conn->query("DELETE FROM diretoria WHERE id = $id");

    // Remove a imagem se existir
    if ($diretor && !empty($diretor['foto'])) {
        $caminho = dirname(__DIR__, 2) . "/img/" . $diretor['foto'];
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }

    header("Location: diretoria.php?ok=1");
    exit;
} else {
    echo "<p class='text-danger'>ID inválido.</p>";
}
