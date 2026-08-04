<?php

namespace App\Enums;

enum ConsultantStatus: string
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case VACATION = 'vacation';
}
