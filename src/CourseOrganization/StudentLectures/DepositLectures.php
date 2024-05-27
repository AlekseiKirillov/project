<?php

declare(strict_types=1);

namespace App\CourseOrganization\StudentLectures;

use App\Infrastructure\Uuid\Uuid;

final readonly class DepositLectures
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
