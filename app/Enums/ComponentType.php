<?php

namespace App\Enums;

enum ComponentType: string
{
    case CHOICE = 'choice';
    case TEXT = 'text';
    case IMAGE = 'image';
    case NUMBER = 'number';
    case SELECT = 'select';
    case CHECKBOX = 'checkbox';
    case IMAGE_UPLOAD = 'image_upload';
    case DATE = 'date';
}
