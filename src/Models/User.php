<?php
declare(strict_types=1);

namespace Models;

class User extends Model {
    protected string $table = 'users';
    protected array $fillable = [
        'username',
        'email',
        'password',
        'primary_currency_id',
        'is_admin'
    ];
    
    public function findByUsername(string $username): ?array {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch() ?: null;
    }
    
    public function findByEmail(string $email): ?array {
        $stmt = $this->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch() ?: null;
    }
    
    public static function hashPassword(string $password): string {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }
    
    public static function verifyPassword(string $password, string $hash): bool {
        return password_verify($password, $hash);
    }
}
