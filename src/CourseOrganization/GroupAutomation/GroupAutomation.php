<?php

declare(strict_types=1);

namespace App\CourseOrganization\GroupAutomation;

use App\CourseOrganization\Lecture\LectureScheduled;
use App\CourseOrganization\Lecture\LectureStarted;
use App\CourseOrganization\StudentLectures\AttendLecture;
use App\CourseOrganization\StudentLectures\EnrollInLecture;
use App\Infrastructure\MessageBus\MessageBus;
use App\Infrastructure\Uuid\Uuid;
use App\Infrastructure\Uuid\UuidMap;
use App\StudentEnrollment\StudentEnrolled;

final class GroupAutomation
{
    /**
     * @var list<Uuid>
     */
    private array $studentIds = [];

    /**
     * @var UuidMap<bool>
     */
    private UuidMap $lectureStarted;

    public function __construct(
        private readonly Uuid $groupId,
    ) {
        /** @var UuidMap<bool> */
        $this->lectureStarted = new UuidMap();
    }

    public function onStudentEnrolled(StudentEnrolled $event, MessageBus $messageBus): void
    {
        $this->studentIds[] = $event->studentId;

        foreach ($this->lectureStarted as $lectureId => $started) {
            $messageBus->dispatch(new EnrollInLecture($event->studentId, $lectureId));

            if ($started) {
                $messageBus->dispatch(new AttendLecture($event->studentId, $lectureId));
            }
        }
    }

    public function onLectureScheduled(LectureScheduled $event, MessageBus $messageBus): void
    {
        $this->lectureStarted[$event->lectureId] ??= false;

        foreach ($this->studentIds as $studentId) {
            $messageBus->dispatch(new EnrollInLecture($studentId, $event->lectureId));
        }
    }

    public function onLectureStarted(LectureStarted $event, MessageBus $messageBus): void
    {
        $this->lectureStarted[$event->lectureId] ??= true;

        foreach ($this->studentIds as $studentId) {
            $messageBus->dispatch(new AttendLecture($studentId, $event->lectureId));
        }
    }
}
