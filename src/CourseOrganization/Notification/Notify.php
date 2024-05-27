<?php

declare(strict_types=1);

namespace App\Notification;

use App\Infrastructure\Uuid\Uuid;

final readonly class Notify
{
    public function __construct(
        public Uuid $recipientId,
        public string $message,
    ) {
    }
}
