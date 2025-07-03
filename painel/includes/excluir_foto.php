<?php
include_once(__DIR__ . "/auth.php");
include_once(__DIR__ . "/conexao.php");

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    // Busca o nome da imagem no banco
    $res = $conn->query("SELECT imagem FROM album_fotos WHERE id = $id");
    $foto = $res->fetch_assoc();

    // Exclui do banco
    $conn->query("DELETE FROM album_fotos WHERE id = $id");

    // Remove o arquivo físico da imagem
    if ($foto && !empty($foto['imagem'])) {
        $caminho = __DIR__ . "/../../img/" . $foto['imagem'];
        if (file_exists($caminho)) {
            unlink($caminho);
        }
    }

    header("Location: album_fotos.php?ok=1");
    exit;
} else {
    echo "<p>Foto inválida ou não informada.</p>";
}
