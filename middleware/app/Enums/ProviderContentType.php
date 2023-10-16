<?php

namespace App\Enums;

enum ProviderContentType: string
{
    case COURSE = 'course';
    case EXAM = 'exam';
    case CERTIFICATE = 'certificate';
}
