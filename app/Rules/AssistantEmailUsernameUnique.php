<?php

declare(strict_types=1);

namespace App\Rules;

use App\Models\User;
use Closure;
use Filament\Forms\Form;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Validates the assistant "email username" field: local part only, full email unique on users.email.
 *
 * @author CKD
 *
 * @created 2026-04-24
 */
final class AssistantEmailUsernameUnique implements ValidationRule
{
    public function __construct(
        private readonly Form $form,
        private readonly string $emailSuffix,
    ) {}

    /**
     * @param  Closure(string): void  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $prefix = is_string($value) ? trim($value) : '';

        if ($prefix === '') {
            return;
        }

        if (str_contains($prefix, '@')) {
            $fail('Enter only the username before '.$this->emailSuffix.'.');

            return;
        }

        $email = strtolower($prefix).strtolower($this->emailSuffix);

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fail('The username is not a valid email local part.');

            return;
        }

        $query = User::query()->where('email', $email);

        $record = $this->form->getRecord();

        if ($record !== null) {
            $query->where('id', '!=', $record->getKey());
        }

        if ($query->exists()) {
            $fail(__('validation.unique', ['attribute' => __('validation.attributes.email')]));
        }
    }
}
