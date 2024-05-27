<?php

declare(strict_types=1);

namespace App\CourseOrganization\GiveAwayLectureIfRescheduledLate;

use App\CourseOrganization\GroupAutomation\GiveAwayLecture;
use App\CourseOrganization\Lecture\LectureRescheduled;
use App\CourseOrganization\Lecture\LectureScheduled;
use App\Infrastructure\MessageBus\MessageBus;
use App\Infrastructure\Uuid\Uuid;

final class GiveAwayLectureIfRescheduledLate
{
    private function __construct(
        private readonly Uuid $lectureId,
        private readonly Uuid $groupId,
        public \DateTimeImmutable $scheduledStartTime,
    ) {
    }

    public static function createFromLectureScheduled(LectureScheduled $lectureScheduled): self
    {
        return new self(
            lectureId: $lectureScheduled->lectureId,
            groupId: $lectureScheduled->groupId,
            scheduledStartTime: $lectureScheduled->scheduledStartTime,
        );
    }

    public function onLectureRescheduled(LectureRescheduled $event, MessageBus $messageBus): void
    {
        if ($event->at > $this->scheduledStartTime->modify('-1 day')) {
            $messageBus->dispatch(new GiveAwayLecture($this->groupId));
        }

        $this->scheduledStartTime = $event->newScheduledStartTime;
    }
}
