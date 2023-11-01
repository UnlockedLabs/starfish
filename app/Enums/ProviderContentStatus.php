<?php

namespace App\Enums;

enum ProviderContentStatus: string
{
    case COMPLETED   = 'completed';
    case IN_PROGRESS = 'in_progress';
    case NOT_STARTED = 'not_started';
}
