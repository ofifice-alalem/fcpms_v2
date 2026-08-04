<?php

namespace App\Exceptions;

class DuplicateDailyRecordException extends BusinessRuleException
{
    public function __construct(string $message = 'لكل استشاري سجل يومي واحد فقط لكل تاريخ.', string $ruleCode = 'BR-024')
    {
        parent::__construct($message, $ruleCode, 422);
    }
}
