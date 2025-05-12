<?php

namespace App\Validators;

use Illuminate\Validation\Validator;

class BranchValidator extends Validator
{
    public function validateMinWorkHours($attribute, $value, $parameters)
    {
        $day = explode('_', $attribute)[0]; // получаем день из названия поля (monday, tuesday и т.д.)
        $openField = $day . '_open';
        $openTime = $this->data[$openField] ?? null;
        
        if (!$openTime || !$value) {
            return true;
        }
        
        $start = strtotime($openTime);
        $end = strtotime($value);
        $diffHours = ($end - $start) / 3600;
        
        return $diffHours >= 2; // Минимум 2 часа
    }
}