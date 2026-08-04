<?php

namespace App\Exceptions;

use Exception;

class BusinessRuleException extends Exception
{
    protected string $ruleCode;

    public function __construct(string $message, string $ruleCode = '', int $code = 422, ?Exception $previous = null)
    {
        $this->ruleCode = $ruleCode;
        parent::__construct($message, $code, $previous);
    }

    public function getRuleCode(): string
    {
        return $this->ruleCode;
    }
}
