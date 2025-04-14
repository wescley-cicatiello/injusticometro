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

// REQUISIÇÃO PARA A API DA RIOT account info


$url = "https://$region.api.riotgames.com/riot/account/v1/accounts/by-riot-id/$summonerName/$tagline?api_key=$apiKey";


$ch = curl_init($url);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

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
$puuid = $map["puuid"];

// REQUISIÇÃO PARA A API DA RIOT match history


$matchHistoyId_url = "https://$region.api.riotgames.com/lol/match/v5/matches/by-puuid/$puuid/ids?type=ranked&start=0&count=20&api_key=$apiKey";

$ch_matchHistory = curl_init($matchHistoyId_url);

curl_setopt($ch_matchHistory, CURLOPT_RETURNTRANSFER, true);

$httpCode_matchHistory = curl_getinfo($ch_matchHistory, CURLINFO_HTTP_CODE);
$err_matchHistory = curl_error($ch_matchHistory);

$response_matchHistory = curl_exec($ch_matchHistory);
$map_matchHistory = json_decode($response_matchHistory, true);

curl_close($ch_matchHistory);

var_dump("Código nº: $httpCode_matchHistory");
if ($err_matchHistory != null && strlen($err_matchHistory) > 0) var_dump("Erro: $err_matchHistory");

curl_close($ch_matchHistory);

var_dump($map_matchHistory);

$varteste = $map_matchHistory[0]; //TODO: change var name

$match_url = "https://$region.api.riotgames.com/lol/match/v5/matches/$varteste?api_key=$apiKey";

$ch_matchData = curl_init($match_url);

curl_setopt($ch_matchData, CURLOPT_RETURNTRANSFER, true);

$httpCode_matchData = curl_getinfo($ch_matchData, CURLINFO_HTTP_CODE);
$err_matchData = curl_error($ch_matchData);

$response_matchData = curl_exec($ch_matchData);
$map_matchData = json_decode($response_matchData, true);

curl_close($ch_matchData);

var_dump("Código nº: $httpCode_matchData");
if ($err_matchData != null && strlen($err_matchData) > 0) var_dump("Erro: $err_matchData");

curl_close($ch_matchData);

var_dump($map_matchData);























?>
