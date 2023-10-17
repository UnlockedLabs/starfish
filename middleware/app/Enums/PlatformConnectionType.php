<?php

namespace App\Enums;


enum PlatformConnectionType: string
{
    case ENABLED =  'enabled';
    case DISABLED = 'disabled';
    case ARCHIVED = 'archived';
}
