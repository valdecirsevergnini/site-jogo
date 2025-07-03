<?php
session_start();

// Redireciona para login se não estiver autenticado
if (!isset($_SESSION['usuario_logado'])) {
    header("Location: login.php");
    exit();
}
?>
