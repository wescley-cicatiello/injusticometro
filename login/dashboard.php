<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Área do Usuário</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo htmlspecialchars($_SESSION["nome"]); ?>!</h1>
    <p>Você está logado com sucesso.</p>
    <a href="logout.php">Sair</a>
</body>
</html>
