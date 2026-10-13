<?php

declare(strict_types=1);

namespace App\Service;

use App\Core\Session;
use App\Repository\UserRepository;

final class Auth
{
    private const KEY = 'user_id';

    /** @var array<string, mixed>|null|false false = pas encore chargé */
    private array|null|false $user = false;

    public function __construct(
        private readonly Session $session,
        private readonly UserRepository $users,
    ) {
    }

    /** @return array<string, mixed>|null */
    public function attempt(string $email, string $password): ?array
    {
        $user = $this->users->findForAuth($email);

        // Vérification à temps constant même si l'e-mail est inconnu (anti-énumération).
        $hash = $user['password_hash'] ?? '$2y$10$pEYaIw3eSF/aohNrZReMJeE1CPC9qDP7Z1otWAJZxXeSEdCZM768.';
        if (!password_verify($password, $hash) || $user === null) {
            return null;
        }

        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $this->users->updatePassword((int) $user['id'], $password);
        }

        return $user;
    }

    public function login(int $userId): void
    {
        $this->session->regenerate();
        $this->session->set(self::KEY, $userId);
        $this->user = false;
    }

    public function logout(): void
    {
        $this->session->destroy();
        $this->user = null;
    }

    public function check(): bool
    {
        return $this->user() !== null;
    }

    public function id(): ?int
    {
        return $this->user()['id'] ?? null;
    }

    /** @return array<string, mixed>|null */
    public function user(): ?array
    {
        if ($this->user === false) {
            $id = $this->session->get(self::KEY);
            $this->user = is_int($id) ? $this->users->find($id) : null;
        }

        return $this->user;
    }

    public function refresh(): void
    {
        $this->user = false;
    }
}
