<?php
require_once "user.php"; // sua classe Usuario

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $password = $_POST["password"];

    $usuario = new Usuario($name, $email, $password);
    $mensagem = $usuario->cadastrar();

    echo "<script>alert('$mensagem'); window.location.href = 'index.php';</script>";
}
?>
