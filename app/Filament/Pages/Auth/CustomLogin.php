<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Contracts\Support\Htmlable;

class CustomLogin extends BaseLogin
{
    // Removes the "Sign in to your account" heading on the login card
    public function getHeading(): string|Htmlable
    {
        return '';
    }

    // Removes the "Remember me" checkbox by overriding the default form schema
    protected function getForms(): array
    {
        return [
            'form' => $this->form(
                $this->makeForm()
                    ->schema([
                        $this->getEmailFormComponent(),
                        $this->getPasswordFormComponent(),
                        // Intentionally omitted $this->getRememberFormComponent()
                    ])
                    ->statePath('data')
            ),
        ];
    }
}
