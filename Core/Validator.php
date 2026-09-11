<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Lightweight rule-based validator.
 *
 * Usage:
 *   $v = new Validator($data, [
 *       'email' => 'required|email',
 *       'name'  => 'required|min:2|max:100',
 *   ]);
 *   if ($v->fails()) { $errors = $v->errors(); }
 */
final class Validator
{
    private array $errors = [];

    public function __construct(
        private readonly array $data,
        private readonly array $rules
    ) {
        $this->run();
    }

    private function run(): void
    {
        foreach ($this->rules as $field => $ruleString) {
            $rules = explode('|', $ruleString);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->applyRule($field, $value, $rule);
            }
        }
    }

    private function applyRule(string $field, mixed $value, string $rule): void
    {
        $parameter = null;

        if (str_contains($rule, ':')) {
            [$rule, $parameter] = explode(':', $rule, 2);
        }

        $isEmpty = $value === null || $value === '';

        match ($rule) {
            'required' => $isEmpty ? $this->addError($field, 'This field is required.') : null,
            'email' => (!$isEmpty && !filter_var($value, FILTER_VALIDATE_EMAIL))
                ? $this->addError($field, 'Enter a valid email address.') : null,
            'numeric' => (!$isEmpty && !is_numeric($value))
                ? $this->addError($field, 'This field must be numeric.') : null,
            'min' => (!$isEmpty && is_string($value) && mb_strlen($value) < (int) $parameter)
                ? $this->addError($field, "Minimum length is {$parameter} characters.") : null,
            'max' => (!$isEmpty && is_string($value) && mb_strlen($value) > (int) $parameter)
                ? $this->addError($field, "Maximum length is {$parameter} characters.") : null,
            'url' => (!$isEmpty && !filter_var($value, FILTER_VALIDATE_URL))
                ? $this->addError($field, 'Enter a valid URL.') : null,
            'slug' => (!$isEmpty && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', (string) $value))
                ? $this->addError($field, 'Use lowercase letters, numbers and hyphens only.') : null,
            'in' => (!$isEmpty && !in_array($value, explode(',', (string) $parameter), true))
                ? $this->addError($field, 'The selected value is invalid.') : null,
            default => null,
        };
    }

    private function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    public function fails(): bool
    {
        return $this->errors !== [];
    }

    public function passes(): bool
    {
        return !$this->fails();
    }

    public function errors(): array
    {
        return $this->errors;
    }

    public function firstError(string $field): ?string
    {
        return $this->errors[$field][0] ?? null;
    }
}
