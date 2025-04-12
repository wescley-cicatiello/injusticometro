<?php
require '../riotkeymanager/riot_key_manager.php';

// VALIDA POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['region']) || empty($_POST['summoner']) || empty($_POST['tagline'])) {
    die("❌ Região e nome do invocador são obrigatórios.");
}

$region = $_POST['region'];
$summonerName = trim($_POST['summoner']);
$tagline = $_POST['tagline'];

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


$url = "https://$region.api.riotgames.com/riot/account/v1/accounts/by-riot-id/$summonerName/$tagline?api_key=$apiKey";


$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// curl_setopt_array($ch, [
//     CURLOPT_RETURNTRANSFER => true
// ]);

$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);

$response = curl_exec($ch);
$map = json_decode($response, true);

curl_close($ch);

var_dump("Código nº: $httpCode");
if ($err != null && strlen($err) > 0) var_dump("Erro: $err");

var_dump($map);
var_dump($map["puuid"]);
var_dump($map["gameName"]);

https://br1.api.riotgames.com/lol/summoner/v4/summoners/by-puuid/ZbqamuYdo-QsQJWhtpoAVmiQKkCpVrwDY7YJqA_d6sh1pOB52I_g8oSCWex6F79IhFsZcBIOY5YP6A?api_key=RGAPI-67cfb242-4bcb-4b07-a997-e370a6056f0a




















// TRATA E MOSTRA O RESULTADO
// if ($httpCode === 200) {
//     $data = json_decode($response, true);
//         var_dump($response);
//     echo "<h2>🔍 Resultado para: " . htmlspecialchars($summonerName) . "</h2>";
//     echo "<ul>";
//     echo "<li><strong>ID:</strong> {$data['id']}</li>";
//     echo "<li><strong>PUUID:</strong> {$data['puuid']}</li>";
//     echo "<li><strong>Nível:</strong> {$data['summonerLevel']}</li>";
//     echo "</ul>";
// } else {
//     echo "❌ Erro ao buscar dados do invocador. Código HTTP: $httpCode<br>";
//     echo "Resposta: <pre>$response</pre>";
// }

?>
