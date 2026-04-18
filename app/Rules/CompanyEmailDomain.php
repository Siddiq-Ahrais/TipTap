<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class CompanyEmailDomain implements ValidationRule
{
    public function __construct(
        private readonly string $allowedDomain = ''
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = strtolower(trim((string) $value));

        if (! str_contains($email, '@')) {
            $fail('The :attribute must contain a valid company email domain.');

            return;
        }

        [, $domain] = explode('@', $email, 2);

        $expectedDomain = strtolower(
            $this->allowedDomain !== ''
                ? $this->allowedDomain
                : (string) config('app.company_email_domain', 'company.com')
        );

        if ($domain !== $expectedDomain) {
            $fail("Only {$expectedDomain} email addresses are allowed.");
        }
    }
}
