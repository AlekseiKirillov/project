<?php

declare(strict_types=1);

namespace App\CourseOrganization\Lecture;

use App\Infrastructure\Uuid\Uuid;

final readonly class LectureRescheduled
{
    public function __construct(
        public Uuid $lectureId,
        public \DateTimeImmutable $newScheduledStartTime,
        public \DateTimeImmutable $at,
    ) {
    }
}
