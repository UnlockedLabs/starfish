<?php

namespace App\Enums;

enum ProviderResourceType: string
{
    case COURSE = 'course';
    case EXAM = 'exam';
    case CERTIFICATE = 'certificate';
}
