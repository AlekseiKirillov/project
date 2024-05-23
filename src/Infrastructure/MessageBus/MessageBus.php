<?php

declare(strict_types=1);

namespace App\Infrastructure\MessageBus;

interface MessageBus
{
    public function dispatch(object $message): void;
}
