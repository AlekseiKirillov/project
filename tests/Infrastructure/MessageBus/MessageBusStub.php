<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\MessageBus;

use App\Infrastructure\MessageBus\MessageBus;

final class MessageBusStub implements MessageBus
{
    /**
     * @psalm-readonly-allow-private-mutation
     * @var list<object>
     */
    public array $messages = [];

    public function dispatch(object $message): void
    {
        $this->messages[] = $message;
    }
}
