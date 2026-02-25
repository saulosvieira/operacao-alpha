<?php

namespace App\Domain\Edduz\Enums;

enum WebhookProcessingStatus: string
{
    case SUCCESS = 'success';
    case ERROR = 'error';
    case DUPLICATE = 'duplicate';
    case INVALID_TOKEN = 'invalid_token';
}
