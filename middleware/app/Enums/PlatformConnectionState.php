<?php

namespace App\Enums;


enum PlatformConnectionState: string
{
    case ENABLED =  'enabled';
    case DISABLED = 'disabled';
    case ARCHIVED = 'archived';
}
