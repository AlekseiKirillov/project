<?php

declare(strict_types=1);

namespace App\CourseOrganization\Lecture;

use App\CourseOrganization\Lecture\Error\CannotFinishNotStartedLecture;
use App\CourseOrganization\Lecture\Error\CannotRescheduleStartedLecture;
use App\CourseOrganization\Lecture\Error\CannotStartFinishedLecture;
use App\Infrastructure\MessageBus\MessageBus;
use App\Infrastructure\Uuid\Uuid;

final class Lecture
{
    private function __construct(
        private readonly Uuid $lectureId,
        private LectureStatus $status = LectureStatus::SCHEDULED,
    ) {
    }

    public static function schedule(
        Uuid $lectureId,
        Uuid $groupId,
        \DateTimeImmutable $scheduledStartTime,
        MessageBus $messageBus
    ): self {
        $lecture = new self(lectureId: $lectureId);
        $messageBus->dispatch(
            new LectureScheduled(
                lectureId: $lectureId,
                groupId: $groupId,
                scheduledStartTime: $scheduledStartTime
            )
        );

        return $lecture;
    }

    /**
     * @throws CannotRescheduleStartedLecture
     */
    public function reschedule(
        \DateTimeImmutable $newScheduledStartTime,
        \DateTimeImmutable $at,
        MessageBus $messageBus
    ): void {
        if ($this->status !== LectureStatus::SCHEDULED) {
            throw new CannotRescheduleStartedLecture();
        }

        $messageBus->dispatch(new LectureRescheduled(
            lectureId: $this->lectureId,
            newScheduledStartTime: $newScheduledStartTime,
            at: $at,
        ));
    }

    /**
     * @throws CannotStartFinishedLecture
     */
    public function start(\DateTimeImmutable $at, MessageBus $messageBus): void
    {
        if ($this->status === LectureStatus::STARTED) {
            return;
        }

        if ($this->status === LectureStatus::FINISHED) {
            throw new CannotStartFinishedLecture();
        }

        $this->status = LectureStatus::STARTED;

        $messageBus->dispatch(new LectureStarted(
            lectureId: $this->lectureId,
            at: $at,
        ));
    }

    /**
     * @throws CannotFinishNotStartedLecture
     */
    public function finish(\DateTimeImmutable $at, MessageBus $messageBus): void
    {
        if ($this->status === LectureStatus::FINISHED) {
            return;
        }

        if ($this->status === LectureStatus::SCHEDULED) {
            throw new CannotFinishNotStartedLecture();
        }

        $this->status = LectureStatus::FINISHED;

        $messageBus->dispatch(new LectureFinished(
            lectureId: $this->lectureId,
            at: $at,
        ));
    }
}
