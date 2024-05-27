<?php

declare(strict_types=1);

namespace App\CourseOrganization\Lecture;

use App\Infrastructure\Uuid\Uuid;

final readonly class LectureScheduled
{
    public function __construct(
        public Uuid $lectureId,
        public Uuid $groupId,
        public \DateTimeImmutable $scheduledStartTime,
    ) {
    }
}
