<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class StrongPassword implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  Closure(string, ?string=): PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // 1. Cek apakah pengguna menggunakan kata sandi contoh
        if ($value === 'BalmonLampung24') {
            $fail('Jangan gunakan sandi contoh. Silakan buat sandi Anda sendiri.');
            return;
        }
    }
}
