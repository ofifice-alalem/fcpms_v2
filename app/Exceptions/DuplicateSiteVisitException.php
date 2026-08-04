<?php

namespace App\Exceptions;

class DuplicateSiteVisitException extends BusinessRuleException
{
    public function __construct(string $message = 'لا يمكن تسجيل أكثر من زيارة لنفس الموقع في اليوم الواحد.', string $ruleCode = 'BR-023')
    {
        parent::__construct($message, $ruleCode, 422);
    }
}
