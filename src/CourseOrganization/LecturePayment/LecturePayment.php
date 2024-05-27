<?php

declare(strict_types=1);

namespace App\CourseOrganization\LecturePayment;

use App\CourseOrganization\StudentLectures\DepositLectures;
use App\Infrastructure\MessageBus\MessageBus;
use App\Infrastructure\Uuid\Uuid;
use App\Payment\InvoicePaid;
use App\Payment\IssueInvoice;

final readonly class LecturePayment
{
    private const int LECTURE_PRICE = 3000;

    /**
     * @param positive-int $lecturesNumber
     */
    private function __construct(
        private Uuid $paymentId,
        private Uuid $studentId,
        private int $lecturesNumber,
    ) {
    }

    /**
     * @param positive-int $lecturesNumber
     */
    public static function issue(
        Uuid $paymentId,
        Uuid $studentId,
        int $lecturesNumber,
        MessageBus $messageBus,
    ): self {
        $messageBus->dispatch(new IssueInvoice($paymentId, $studentId, self::LECTURE_PRICE * $lecturesNumber));

        return new self($paymentId, $studentId, $lecturesNumber);
    }

    public function onInvoicePaid(InvoicePaid $event, MessageBus $messageBus): void
    {
        $messageBus->dispatch(new DepositLectures($this->studentId, $this->lecturesNumber));
    }
}
