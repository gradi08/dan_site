<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class UserRepository
{
    public function __construct(private readonly PDO $db)
    {
    }

    /** @return array<string, mixed>|null */
    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT id, name, email, phone, created_at FROM users WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->fetch() ?: null;
    }

    /** @return array<string, mixed>|null Inclut le hash du mot de passe. */
    public function findForAuth(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE LOWER(email) = LOWER(?)');
        $stmt->execute([$email]);

        return $stmt->fetch() ?: null;
    }

    public function passwordHash(int $id): ?string
    {
        $stmt = $this->db->prepare('SELECT password_hash FROM users WHERE id = ?');
        $stmt->execute([$id]);

        return $stmt->fetchColumn() ?: null;
    }

    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE LOWER(email) = LOWER(?) AND id <> ?');
        $stmt->execute([$email, $exceptId ?? 0]);

        return (bool) $stmt->fetchColumn();
    }

    public function create(string $name, string $email, string $phone, string $password): int
    {
        $now = date('Y-m-d H:i:s');
        $stmt = $this->db->prepare(
            'INSERT INTO users (name, email, phone, password_hash, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$name, strtolower($email), $phone, password_hash($password, PASSWORD_DEFAULT), $now, $now]);

        return (int) $this->db->lastInsertId();
    }

    public function updateProfile(int $id, string $name, string $email, string $phone): void
    {
        $stmt = $this->db->prepare('UPDATE users SET name = ?, email = ?, phone = ?, updated_at = ? WHERE id = ?');
        $stmt->execute([$name, strtolower($email), $phone, date('Y-m-d H:i:s'), $id]);
    }

    public function updatePassword(int $id, string $password): void
    {
        $stmt = $this->db->prepare('UPDATE users SET password_hash = ?, updated_at = ? WHERE id = ?');
        $stmt->execute([password_hash($password, PASSWORD_DEFAULT), date('Y-m-d H:i:s'), $id]);
    }
}
