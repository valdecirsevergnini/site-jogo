<?php
session_start();
// Se já estiver logado, redireciona direto
if (isset($_SESSION['usuario_logado'])) {
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - Boleiros de Sábado</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f3f3f3;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .login-container {
            background-color: #fff;
            border: 1px solid #ccc;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
            width: 100%;
            max-width: 400px;
        }
    </style>
</head>
<body>
<div class="login-container">
    <h3 class="text-center mb-3">Painel Boleiros</h3>
    <?php if (isset($_SESSION['erro_login'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['erro_login']; unset($_SESSION['erro_login']); ?></div>
    <?php endif; ?>
    <form action="valida_login.php" method="POST">
        <div class="mb-3">
            <label for="usuario" class="form-label">Usuário</label>
            <input type="text" class="form-control" name="usuario" required>
        </div>
        <div class="mb-3">
            <label for="senha" class="form-label">Senha</label>
            <input type="password" class="form-control" name="senha" required>
        </div>
        <button type="submit" class="btn btn-dark w-100">Entrar</button>
    </form>
</div>
</body>
</html>
