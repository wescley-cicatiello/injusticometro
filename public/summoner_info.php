<?php
require '../riotkeymanager/riot_key_manager.php';

// VALIDA POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['region']) || empty($_POST['summoner'])) {
    die("❌ Região e nome do invocador são obrigatórios.");
}

$region = $_POST['region'];
$summonerName = trim($_POST['summoner']);

// CONFIGURAÇÕES DO BANCO
$dbHost = 'localhost';
$dbName = 'injusticometro';
$dbUser = 'root';
$dbPass = '';
$encryptionKey = getenv('ENCRYPTION_KEY') ?: 'uma-chave-secreta-de-32-bytes!!';

// CONEXÃO COM PDO
try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erro ao conectar no banco de dados: " . $e->getMessage());
}

// RECUPERA A CHAVE DA API
$keyManager = new RiotKeyManager($pdo, $encryptionKey);
$apiKey = $keyManager->getLatestApiKey();

if (!$apiKey) {
    die("❌ Não foi possível recuperar a chave da API.");
}

// REQUISIÇÃO PARA A API DA RIOT
$url = "https://$region.api.riotgames.com/lol/summoner/v4/summoners/by-name/" . urlencode($summonerName);

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "X-Riot-Token: $apiKey"
    ]
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// TRATA E MOSTRA O RESULTADO
if ($httpCode === 200) {
    $data = json_decode($response, true);

    echo "<h2>🔍 Resultado para: " . htmlspecialchars($summonerName) . "</h2>";
    echo "<ul>";
    echo "<li><strong>ID:</strong> {$data['id']}</li>";
    echo "<li><strong>PUUID:</strong> {$data['puuid']}</li>";
    echo "<li><strong>Nível:</strong> {$data['summonerLevel']}</li>";
    echo "</ul>";
} else {
    echo "❌ Erro ao buscar dados do invocador. Código HTTP: $httpCode<br>";
    echo "Resposta: <pre>$response</pre>";
}

echo "<pre>API Key (descriptografada): $apiKey</pre>";

?>
