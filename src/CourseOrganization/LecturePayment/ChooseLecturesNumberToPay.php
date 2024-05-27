<?php

declare(strict_types=1);

namespace App\CourseOrganization\LecturePayment;

use App\Infrastructure\Uuid\Uuid;

final readonly class ChooseLecturesNumberToPay
{
    /**
     * @param positive-int $lecturesNumber
     */
    public function __construct(
        public Uuid $studentId,
        public int $lecturesNumber,
    ) {
    }
}
