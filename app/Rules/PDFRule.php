<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PDFRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  string  $attribute  (string, ?string = null): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value->getMimeType() !== 'application/pdf') {
            $fail('The ' . $attribute . ' must be a PDF file.');
        }
    }
}
