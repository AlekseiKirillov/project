<?php

declare(strict_types=1);

namespace App\Payment;

use App\Infrastructure\Uuid\Uuid;

final readonly class InvoicePaid
{
    public function __construct(
        public Uuid $invoiceId,
    ) {
    }
}
