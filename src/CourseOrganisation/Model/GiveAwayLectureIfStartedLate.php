<?php

declare(strict_types=1);

namespace App\CourseOrganization\Model;

use App\Infrastructure\MessageBus\MessageBus;
use App\Infrastructure\Uuid\Uuid;

final class GiveAwayLectureIfStartedLate
{
    private function __construct(
        private readonly Uuid $lectureId,
        private readonly Uuid $groupId,
        public \DateTimeImmutable $scheduledStartTime,
    ) {}

    public static function createFromLectureScheduled(LectureScheduled $lectureScheduled): self
    {
        return new self(
            lectureId: $lectureScheduled->lectureId,
            groupId: $lectureScheduled->groupId,
            scheduledStartTime: $lectureScheduled->scheduledStartTime,
        );
    }

    public function onLectureRescheduled(LectureRescheduled $event): void
    {
        $this->scheduledStartTime = $event->newScheduledStartTime;
    }

    public function onLectureStarted(LectureStarted $event, MessageBus $messageBus): void
    {
        if ($event->at > $this->scheduledStartTime->modify('+15 minutes')) {
            $messageBus->dispatch(new GiveAwayLecture($this->groupId));
        }
    }
}
