<?php
class Database {
    private $host = "localhost";
    private $dbname = "injusticometro";
    private $usuario = "root";
    private $password = "";

    public function conectar() {
        try {
            $conn = new PDO("mysql:host=$this->host;dbname=$this->dbname;charset=utf8", $this->usuario, $this->password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;
        } catch (PDOException $e) {
            die("Erro na conexão: " . $e->getMessage());
        }
    }
}
?>