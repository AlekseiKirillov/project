<?php

declare(strict_types=1);

namespace App\StudentEnrollment;

use App\Infrastructure\Uuid\Uuid;

final readonly class StudentEnrolled
{
    public function __construct(
        public Uuid $studentId,
        public Uuid $groupId,
    ) {
    }
}
