<?php

class RiotKeyManager
{
    private PDO $pdo;
    private string $encryptionKey;

    public function __construct(PDO $pdo, string $encryptionKey)
    {
        $this->pdo = $pdo;
        $this->encryptionKey = $encryptionKey;
    }

    // Função para gerar UUID v4
    private function generateUUIDv4(): string
    {
        $data = random_bytes(16);

        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40); // versão 4
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80); // variante RFC 4122

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function storeApiKey(string $apiKey): bool
    {
        // 🔁 Apaga todas as chaves anteriores
        $this->pdo->exec("DELETE FROM riot_api_keys");

        $id = $this->generateUUIDv4();
        $iv = random_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encryptedKey = openssl_encrypt($apiKey, 'aes-256-cbc', $this->encryptionKey, 0, $iv);

        $stmt = $this->pdo->prepare("INSERT INTO riot_api_keys (id, encrypted_key, iv) VALUES (:id, :key, :iv)");
        return $stmt->execute([
            ':id' => $id,
            ':key' => $encryptedKey,
            ':iv' => $iv
        ]);
    }


    public function getLatestApiKey(): ?string
    {
        $stmt = $this->pdo->query("SELECT encrypted_key, iv FROM riot_api_keys ORDER BY created_at DESC LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) return null;

        return openssl_decrypt(
            $result['encrypted_key'],
            'aes-256-cbc',
            $this->encryptionKey,
            0,
            $result['iv']
        );
    }
}
