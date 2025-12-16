<?php

namespace App\Domain\Auth\Enums;

enum UserRole: string
{
    case ADMIN = 'admin';
    case CONSULTANT = 'consultor';
    case USER = 'user';
}
