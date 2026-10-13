<?php

declare(strict_types=1);

namespace App\Service;

use PDO;

/**
 * Limite les tentatives de connexion par couple (e-mail, IP) pour freiner le brute-force.
 */
final class LoginThrottle
{
    public function __construct(
        private readonly PDO $db,
        private readonly int $maxAttempts = 5,
        private readonly int $decaySeconds = 900,
    ) {
    }

    public function tooManyAttempts(string $email, string $ip): bool
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM login_attempts WHERE throttle_key = ? AND attempted_at > ?');
        $stmt->execute([$this->key($email, $ip), $this->windowStart()]);

        return (int) $stmt->fetchColumn() >= $this->maxAttempts;
    }

    public function hit(string $email, string $ip): void
    {
        $this->db->prepare('INSERT INTO login_attempts (throttle_key, attempted_at) VALUES (?, ?)')
            ->execute([$this->key($email, $ip), date('Y-m-d H:i:s')]);

        // Nettoyage opportuniste des entrées expirées.
        if (random_int(1, 50) === 1) {
            $this->db->prepare('DELETE FROM login_attempts WHERE attempted_at < ?')->execute([$this->windowStart()]);
        }
    }

    public function clear(string $email, string $ip): void
    {
        $this->db->prepare('DELETE FROM login_attempts WHERE throttle_key = ?')->execute([$this->key($email, $ip)]);
    }

    public function minutesRemaining(): int
    {
        return (int) ceil($this->decaySeconds / 60);
    }

    private function key(string $email, string $ip): string
    {
        return hash('sha256', strtolower(trim($email)) . '|' . $ip);
    }

    private function windowStart(): string
    {
        return date('Y-m-d H:i:s', time() - $this->decaySeconds);
    }
}
