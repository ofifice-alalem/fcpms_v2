<?php

namespace App\Enums;

enum AttendanceStatus: string
{
    case HOLIDAY = 'holiday';
    case NON_WORKING_DAY = 'non_working_day';
    case LEAVE = 'leave';
    case PRESENT = 'present';
    case ABSENT = 'absent';
}
