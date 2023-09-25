<?php

namespace App\Enum;


enum PlatformConnectionType: string
{
    case ENABLED = 'enabled';
    case DISABLED = 'disabled';
    case ARCHIVED = 'archived';
}
