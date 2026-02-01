<?php

namespace App\Core;

class Validator
{
    private const MSG_REQUIRED = 'El campo %s es obligatorio';
    private const MSG_EMAIL = 'El formato del correo es inválido';
    private const MSG_MIN_LENGTH = 'El campo %s debe tener al menos %d caracteres';
    private const MSG_MAX_LENGTH = 'El campo %s no debe exceder %d caracteres';
    private const MSG_NUMERIC = 'El campo %s debe ser numérico';
    private const MSG_ALPHA = 'El campo %s solo puede contener letras';
    private const MSG_ALPHANUMERIC = 'El campo %s solo puede contener letras y números';
    
    private const ZERO_STRING = '0';

    private array $errors = [];
    private array $data = [];
    private array $validatedFields = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function required(string $field, ?string $message = null): self
    {
        $this->markFieldAsValidated($field);
        
        if ($this->isFieldEmpty($field)) {
            $this->addError($field, $message ?? sprintf(self::MSG_REQUIRED, $field));
        }

        return $this;
    }

    public function email(string $field, ?string $message = null): self
    {
        $this->markFieldAsValidated($field);
        $value = $this->getValue($field);
        
        if ($this->hasValue($value) && !$this->isValidEmail($value)) {
            $this->addError($field, $message ?? self::MSG_EMAIL);
        }

        return $this;
    }

    public function min(string $field, int $min, ?string $message = null): self
    {
        $this->markFieldAsValidated($field);
        $value = $this->getValue($field);
        
        if ($this->hasValue($value) && $this->getLength($value) < $min) {
            $this->addError($field, $message ?? sprintf(self::MSG_MIN_LENGTH, $field, $min));
        }

        return $this;
    }

    public function max(string $field, int $max, ?string $message = null): self
    {
        $this->markFieldAsValidated($field);
        $value = $this->getValue($field);
        
        if ($this->hasValue($value) && $this->getLength($value) > $max) {
            $this->addError($field, $message ?? sprintf(self::MSG_MAX_LENGTH, $field, $max));
        }

        return $this;
    }

    public function numeric(string $field, ?string $message = null): self
    {
        $this->markFieldAsValidated($field);
        $value = $this->getValue($field);
        
        if ($this->hasValue($value) && !$this->isNumeric($value)) {
            $this->addError($field, $message ?? sprintf(self::MSG_NUMERIC, $field));
        }

        return $this;
    }

    public function alpha(string $field, ?string $message = null): self
    {
        $this->markFieldAsValidated($field);
        $value = $this->getValue($field);
        
        if ($this->hasValue($value) && !$this->isAlpha($value)) {
            $this->addError($field, $message ?? sprintf(self::MSG_ALPHA, $field));
        }

        return $this;
    }

    public function alphanumeric(string $field, ?string $message = null): self
    {
        $this->markFieldAsValidated($field);
        $value = $this->getValue($field);
        
        if ($this->hasValue($value) && !$this->isAlphanumeric($value)) {
            $this->addError($field, $message ?? sprintf(self::MSG_ALPHANUMERIC, $field));
        }

        return $this;
    }

    public function pattern(string $field, string $pattern, string $message): self
    {
        $this->markFieldAsValidated($field);
        $value = $this->getValue($field);
        
        if ($this->hasValue($value) && !$this->matchesPattern($value, $pattern)) {
            $this->addError($field, $message);
        }

        return $this;
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    public function fails(): bool
    {
        return !$this->passes();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function validated(): array
    {
        return $this->sanitizeValidatedData();
    }

    public static function make(array $data, array $rules): array
    {
        $validator = new self($data);
        $validator->applyRules($rules);

        return [$validator->validated(), $validator->errors()];
    }

    private function getValue(string $field): mixed
    {
        return $this->data[$field] ?? null;
    }

    private function hasValue(mixed $value): bool
    {
        return !empty($value) || $value === self::ZERO_STRING;
    }

    private function isFieldEmpty(string $field): bool
    {
        $value = $this->getValue($field);
        return empty($value) && $value !== self::ZERO_STRING;
    }

    private function isValidEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    private function getLength(string $value): int
    {
        return mb_strlen($value);
    }

    private function isNumeric(mixed $value): bool
    {
        return is_numeric($value);
    }

    private function isAlpha(string $value): bool
    {
        return ctype_alpha($value);
    }

    private function isAlphanumeric(string $value): bool
    {
        return ctype_alnum($value);
    }

    private function matchesPattern(string $value, string $pattern): bool
    {
        return preg_match($pattern, $value) === 1;
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field] = $message;
    }

    private function markFieldAsValidated(string $field): void
    {
        if (!in_array($field, $this->validatedFields, true)) {
            $this->validatedFields[] = $field;
        }
    }

    private function sanitizeValidatedData(): array
    {
        $validated = [];
        
        foreach ($this->validatedFields as $field) {
            if (array_key_exists($field, $this->data)) {
                $validated[$field] = $this->sanitizeValue($this->data[$field]);
            }
        }

        return $validated;
    }

    private function sanitizeData(): array
    {
        $validated = [];
        
        foreach ($this->data as $key => $value) {
            $validated[$key] = $this->sanitizeValue($value);
        }

        return $validated;
    }

    private function sanitizeValue(mixed $value): mixed
    {
        return is_string($value) ? trim($value) : $value;
    }

    private function applyRules(array $rules): void
    {
        foreach ($rules as $field => $fieldRules) {
            $this->applyFieldRules($field, $fieldRules);
        }
    }

    private function applyFieldRules(string $field, array $fieldRules): void
    {
        foreach ($fieldRules as $rule) {
            $this->applyRule($field, $rule);
        }
    }

    private function applyRule(string $field, string|array $rule): void
    {
        if (is_string($rule)) {
            $this->$rule($field);
            return;
        }

        if (is_array($rule)) {
            $this->applyArrayRule($field, $rule);
        }
    }

    private function applyArrayRule(string $field, array $rule): void
    {
        $method = $rule[0];
        $params = array_slice($rule, 1);
        $this->$method($field, ...$params);
    }
}
