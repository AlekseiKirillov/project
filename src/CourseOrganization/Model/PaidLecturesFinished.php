<?php

declare(strict_types=1);

namespace App\CourseOrganization\Model;

use App\Infrastructure\Uuid\Uuid;

final readonly class PaidLecturesFinished
{
    public function __construct(
        public Uuid $studentId,
    ) {}
}
