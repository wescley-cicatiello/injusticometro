<?php
session_start();
require_once "../register/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST["email"];
    $senha = $_POST["password"];

    $conn = (new Database())->conectar();
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":email", $email);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($senha, $usuario["password"])) {
        // Login bem-sucedido
        $_SESSION["usuario_id"] = $usuario["id"];
        $_SESSION["nome"] = $usuario["name"];
        header("Location: dashboard.php");
        exit;
    } else {
        echo "<script>
                alert('E-mail ou senha incorretos!');
                window.location.href = 'login.php';
              </script>";
    }
}
?>