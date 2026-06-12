<?php

declare(strict_types=1);

namespace App\Core;

class Validator
{
    private array $errors = [];
    private array $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, ?string $label = null): self
    {
        $label = $label ?? $field;
        if (empty($this->data[$field]) && $this->data[$field] !== '0') {
            $this->errors[$field][] = "{$label} is required.";
        }
        return $this;
    }

    public function email(string $field, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "{$label} must be a valid email address.";
        }
        return $this;
    }

    public function min(string $field, int $length, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && mb_strlen($value) < $length) {
            $this->errors[$field][] = "{$label} must be at least {$length} characters.";
        }
        return $this;
    }

    public function max(string $field, int $length, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && mb_strlen($value) > $length) {
            $this->errors[$field][] = "{$label} must not exceed {$length} characters.";
        }
        return $this;
    }

    public function in(string $field, array $allowed, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !in_array($value, $allowed, true)) {
            $this->errors[$field][] = "{$label} must be one of: " . implode(', ', $allowed) . ".";
        }
        return $this;
    }

    public function confirmed(string $field, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->data[$field] ?? '';
        $confirmation = $this->data[$field . '_confirmation'] ?? '';
        if ($value !== '' && $value !== $confirmation) {
            $this->errors[$field][] = "{$label} confirmation does not match.";
        }
        return $this;
    }

    public function date(string $field, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !strtotime($value)) {
            $this->errors[$field][] = "{$label} must be a valid date.";
        }
        return $this;
    }

    public function afterOrEqual(string $field, string $date, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && strtotime($value) < strtotime($date)) {
            $this->errors[$field][] = "{$label} must be a date after or equal to {$date}.";
        }
        return $this;
    }

    public function numeric(string $field, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !is_numeric($value)) {
            $this->errors[$field][] = "{$label} must be a number.";
        }
        return $this;
    }

    public function string(string $field, ?string $label = null): self
    {
        $label = $label ?? $field;
        $value = $this->data[$field] ?? '';
        if ($value !== '' && !is_string($value)) {
            $this->errors[$field][] = "{$label} must be a string.";
        }
        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(): ?string
    {
        foreach ($this->errors as $field => $msgs) {
            return $msgs[0] ?? null;
        }
        return null;
    }
}
