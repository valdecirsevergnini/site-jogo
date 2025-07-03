<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Buscar a imagem para excluir do diretório
    $busca = $conn->query("SELECT imagem FROM patrocinadores WHERE id = $id");
    $patrocinador = $busca->fetch_assoc();

    // Exclui o registro do banco
    $conn->query("DELETE FROM patrocinadores WHERE id = $id");

    // Remove a imagem do diretório se existir
    $caminhoImagem = realpath(__DIR__ . "/../../img/") . "/" . $patrocinador['imagem'];
    if ($patrocinador && !empty($patrocinador['imagem']) && file_exists($caminhoImagem)) {
        unlink($caminhoImagem);
    }

    header("Location: patrocinadores.php?ok=1");
    exit;
} else {
    echo "<p class='text-danger'>Patrocinador inválido.</p>";
}
?>
