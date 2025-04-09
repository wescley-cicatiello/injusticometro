<?php
require 'riot_key_manager.php';

// ⚙️ Configurações do banco
$dbHost = 'localhost';
$dbName = 'injusticometro';
$dbUser = 'root';
$dbPass = '';
$encryptionKey = getenv('ENCRYPTION_KEY') ?: 'uma-chave-secreta-de-32-bytes!!'; // Precisa ter 32 caracteres

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['chave'])) {
    $apiKey = trim($_POST['chave']);

    try {
        $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $keyManager = new RiotKeyManager($pdo, $encryptionKey);
        if ($keyManager->storeApiKey($apiKey)) {
            echo "✅ Chave armazenada com sucesso!";
        } else {
            echo "❌ Falha ao armazenar a chave.";
        }
    } catch (PDOException $e) {
        echo "Erro na conexão com o banco: " . $e->getMessage();
    }
} else {
    echo "❌ Nenhuma chave recebida.";
}
?>