<?php

declare(strict_types=1);

namespace App\Http;

final class HttpException extends \RuntimeException
{
    public function __construct(private readonly int $status, string $message = '')
    {
        parent::__construct($message, $status);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public static function notFound(string $message = ''): self
    {
        return new self(404, $message);
    }

    public static function forbidden(string $message = ''): self
    {
        return new self(403, $message);
    }
}
