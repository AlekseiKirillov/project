<?php

declare(strict_types=1);

namespace App\Payment;

use App\Infrastructure\Uuid\Uuid;

final readonly class IssueInvoice
{
    /**
     * @param positive-int $amount
     */
    public function __construct(
        public Uuid $invoiceId,
        public Uuid $recipientId,
        public int $amount,
        public string $description = '',
    ) {
    }
}
