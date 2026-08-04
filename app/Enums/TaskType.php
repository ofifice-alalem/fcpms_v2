<?php

namespace App\Enums;

enum TaskType: string
{
    case DAILY = 'daily';
    case ON_DEMAND = 'on_demand';
}
