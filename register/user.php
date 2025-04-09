<?php
require_once "config.php";

class Usuario {
    private $name;
    private $email;
    private $password;
    private $conn;

    public function __construct($name, $email, $password) {
        $this->name = $name;
        $this->email = $email;
        $this->password = password_hash($password, PASSWORD_DEFAULT);
        $this->conn = (new Database())->conectar();
    }

    public function cadastrar() {
        // Verifica se o nome já existe
        $checkNome = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE name = :name");
        $checkNome->bindParam(':name', $this->name);
        $checkNome->execute();
        if ($checkNome->fetchColumn() > 0) {
            return "Nome já cadastrado.";
        }
    
        // Verifica se o email já existe
        $checkEmail = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $checkEmail->bindParam(':email', $this->email);
        $checkEmail->execute();
        if ($checkEmail->fetchColumn() > 0) {
            return "E-mail já cadastrado.";
        }
    
        // Se passou nas duas verificações, realiza o cadastro
        $sql = "INSERT INTO users (id, name, email, password) VALUES (:id, :name, :email, :password)";
        $stmt = $this->conn->prepare($sql);
        $id = $this->gerarUUID();
    
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
    
        if ($stmt->execute()) {
            return "Cadastro realizado com sucesso!";
        } else {
            return "Erro ao cadastrar.";
        }
    }
    private function gerarUUID() {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // versão 4
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variante
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
    
}
?>