<?php

declare(strict_types=1);

namespace App\CourseOrganization\StudentLectures;

use App\Infrastructure\Uuid\Uuid;

final readonly class AttendLecture
{
    public function __construct(
        public Uuid $studentId,
        public Uuid $lectureId,
    ) {
    }
}
