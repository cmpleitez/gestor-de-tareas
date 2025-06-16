<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidDui implements Rule
{
    public function passes($attribute, $value)
    {
        // Validar que sean exactamente 9 dígitos
        if (!preg_match('/^\d{9}$/', $value)) {
            return false;
        }

        // Extraer los primeros 8 dígitos y el dígito verificador
        $digits = substr($value, 0, 8);
        $checkDigit = intval(substr($value, -1));

        // Cálculo del dígito verificador
        $sum = 0;
        for ($i = 0; $i < strlen($digits); $i++) {
            $digit = intval($digits[$i]);
            $sum += (9 - $i) * $digit;
        }
        
        $calculatedCheckDigit = (10 - ($sum % 10)) % 10;
        return $checkDigit === $calculatedCheckDigit;
    }

    public function message()
    {
        return 'El D.U.I. ingresado no es válido.';
    }
} 