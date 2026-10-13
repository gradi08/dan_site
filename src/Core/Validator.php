<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Validation déclarative : ['email' => 'required|email|max:100'].
 */
final class Validator
{
    /** @var array<string, string> */
    private array $errors = [];

    /**
     * @param array<string, mixed>  $data
     * @param array<string, string> $rules
     * @param array<string, string> $labels
     */
    public function __construct(
        private readonly array $data,
        private readonly array $rules,
        private readonly array $labels = [],
    ) {
    }

    /**
     * @param array<string, mixed>  $data
     * @param array<string, string> $rules
     * @param array<string, string> $labels
     */
    public static function make(array $data, array $rules, array $labels = []): self
    {
        $validator = new self($data, $rules, $labels);
        $validator->run();

        return $validator;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    /** @return array<string, string> */
    public function errors(): array
    {
        return $this->errors;
    }

    public function addError(string $field, string $message): void
    {
        $this->errors[$field] ??= $message;
    }

    private function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $value = $this->data[$field] ?? null;
            $value = is_string($value) ? trim($value) : $value;
            $label = $this->labels[$field] ?? $field;

            foreach (explode('|', $ruleString) as $rule) {
                [$name, $param] = array_pad(explode(':', $rule, 2), 2, null);
                $message = $this->check($name, $param, $value, $field, $label);
                if ($message !== null) {
                    $this->addError($field, $message);
                    break;
                }
            }
        }
    }

    private function check(string $rule, ?string $param, mixed $value, string $field, string $label): ?string
    {
        $empty = $value === null || $value === '';

        return match ($rule) {
            'required' => $empty ? "Le champ « $label » est obligatoire." : null,
            'email'    => !$empty && !filter_var($value, FILTER_VALIDATE_EMAIL)
                ? "Saisissez une adresse e-mail valide (ex. nom@exemple.com)." : null,
            'min'      => !$empty && mb_strlen((string) $value) < (int) $param
                ? "« $label » doit contenir au moins $param caractères." : null,
            'max'      => !$empty && mb_strlen((string) $value) > (int) $param
                ? "« $label » ne peut pas dépasser $param caractères." : null,
            'same'     => $value !== ($this->data[(string) $param] ?? null)
                ? 'Les deux mots de passe ne correspondent pas.' : null,
            'in'       => !$empty && !in_array($value, explode(',', (string) $param), true)
                ? "La valeur de « $label » n'est pas autorisée." : null,
            'phone'    => !$empty && !preg_match('/^\+?[0-9 ().-]{8,20}$/', (string) $value)
                ? 'Saisissez un numéro de téléphone valide (ex. 0812345678).' : null,
            default    => throw new \InvalidArgumentException("Règle inconnue : $rule ($field)"),
        };
    }
}
