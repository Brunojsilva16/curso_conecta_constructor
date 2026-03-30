<?php

namespace App\Models;

use App\Database\DataSource;

class UserModel
{
    private DataSource $db;
    protected string $table = 'users_app';

    public function __construct()
    {
        // O DataSource é obtido via Singleton
        $this->db = DataSource::getInstance();
    }

    /**
     * Busca um usuário pelo email.
     * @param string $email
     * @return array|null
     */
    public function findByEmail(string $email): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE email = :email";
        return $this->db->selectOne($sql, ['email' => $email]); //
    }

    /**
     * Busca um usuário pelo ID.
     * @param int $id
     * @return array|null
     */
    public function findById(int $id): ?array
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        return $this->db->selectOne($sql, ['id' => $id]);
    }

    /**
     * Cria um novo usuário no banco de dados.
     * @param array $data Contendo 'name', 'email', 'password_hash'.
     * @return string|false O ID do último usuário inserido ou false em caso de falha.
     */
    public function create(array $data): string|false
    {
        $sql = "INSERT INTO {$this->table} (name, email, password_hash, subscription_plan) VALUES (:name, :email, :password_hash, 'none')";
        
        $params = [
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => $data['password_hash'],
        ];

        try {
            return $this->db->insertWithLastId($sql, $params);
        } catch (\PDOException $e) {
            // Pode ser útil logar o erro em um ambiente real
            // error_log($e->getMessage());
            return false;
        }
    }

    /**
     * Atualiza o token de redefinição de senha de um usuário.
     * * @param int $userId O ID do usuário.
     * @param string|null $token O token gerado (ou null para limpar).
     * @param string|null $expires A data de expiração (ou null para limpar).
     * @return bool
     */
    public function updatePasswordResetToken(int $userId, ?string $token, ?string $expires): bool
    {
        $sql = "UPDATE {$this->table} SET password_reset_token = :token, password_reset_expires = :expires WHERE id = :id";
        return $this->db->execute($sql, [ //
            'token' => $token,
            'expires' => $expires,
            'id' => $userId
        ]);
    }
    
    /**
     * Atualiza o nome e o CPF (e outros dados não sensíveis) do usuário.
     * * @param int $userId ID do usuário
     * @param array $data Array associativo contendo 'name' e 'cpf'.
     * @return bool
     */
    public function updateProfile(int $userId, array $data): bool
    {
        // Campos permitidos para atualização
        $allowedFields = ['name', 'cpf'];
        $updateParts = [];
        $params = ['id' => $userId];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedFields)) {
                $updateParts[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }
        }

        if (empty($updateParts)) {
            // Nenhum campo válido fornecido para atualização
            return false;
        }

        $sql = "UPDATE {$this->table} SET " . implode(', ', $updateParts) . " WHERE id = :id";
        
        // Execute a query. Retorna true se a query for executada (mesmo que 0 linhas sejam alteradas)
        return $this->db->execute($sql, $params);
    }
    
    /**
     * (MÉTODO NOVO) Encontra um utilizador por um token de reset válido.
     */
    public function findByResetToken(string $token): ?array
    {
        $sql = "SELECT * FROM {$this->table} 
                WHERE password_reset_token = :token 
                AND password_reset_expires > NOW()";
        return $this->db->selectOne($sql, ['token' => $token]);
    }
    
    /**
     * (MÉTODO NOVO) Atualiza a senha do utilizador.
     */
    public function updatePassword(int $userId, string $passwordHash): bool
    {
        $sql = "UPDATE {$this->table} SET password_hash = :password_hash WHERE id = :id";
        return $this->db->execute($sql, [
            'password_hash' => $passwordHash,
            'id' => $userId
        ]);
    }
}