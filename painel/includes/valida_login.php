<?php
session_start();
include_once(__DIR__ . "/conexao.php");

$usuario = $_POST['usuario'] ?? '';
$senha = $_POST['senha'] ?? '';

if ($usuario === '' || $senha === '') {
    $_SESSION['erro_login'] = "Preencha usuário e senha.";
    header("Location: login.php");
    exit();
}

// Busca na nova tabela 'usuarios'
$sql = "SELECT * FROM usuarios WHERE usuario = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $dados = $result->fetch_assoc();

    // Verificação com SHA1
    if ($dados['senha'] === sha1($senha)) {
        $_SESSION['usuario_logado'] = $dados['usuario'];
        header("Location: dashboard.php");
        exit();
    }
}

$_SESSION['erro_login'] = "Usuário ou senha inválidos.";
header("Location: login.php");
exit();
