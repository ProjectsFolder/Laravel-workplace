<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Vat implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return preg_match('/^([A-Z]{2})([0-9]*)$/u', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute filed is not valid vat.';
    }

    public function __toString(): string
    {
        return 'vat';
    }
}
