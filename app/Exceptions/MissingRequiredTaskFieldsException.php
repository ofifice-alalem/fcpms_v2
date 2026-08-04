<?php

namespace App\Exceptions;

class MissingRequiredTaskFieldsException extends BusinessRuleException
{
    public function __construct(string $message = 'يجب تعبئة جميع المهام والحقول الإلزامية قبل الحفظ.', string $ruleCode = 'BR-041')
    {
        parent::__construct($message, $ruleCode, 422);
    }
}
