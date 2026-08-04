<?php

namespace App\Exceptions;

class InactiveUserException extends BusinessRuleException
{
    public function __construct(string $message = 'الحساب غير مفعل، لا يمكنك تسجيل الدخول.', string $ruleCode = 'BR-004')
    {
        parent::__construct($message, $ruleCode, 403);
    }
}
