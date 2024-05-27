<?php

declare(strict_types=1);

namespace App\Tests\CourseOrganization\Model;

use App\CourseOrganization\Model\CannotFinishNotStartedLecture;
use App\CourseOrganization\Model\CannotRescheduleStartedLecture;
use App\CourseOrganization\Model\CannotStartAlreadyStartedLecture;
use App\CourseOrganization\Model\Lecture;
use App\CourseOrganization\Model\LectureFinished;
use App\CourseOrganization\Model\LectureRescheduled;
use App\CourseOrganization\Model\LectureScheduled;
use App\CourseOrganization\Model\LectureStarted;
use App\Tests\Infrastructure\MessageBus\MessageBusStub;
use App\Infrastructure\Uuid\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Lecture::class)]
final class LectureTest extends TestCase
{
    public function testItSchedulesWithEvent(): void
    {
        $messageBus = new MessageBusStub();
        $lectureId = Uuid::v7();
        $groupId = Uuid::v7();
        $scheduledStartTime = new \DateTimeImmutable();

        Lecture::schedule($lectureId, $groupId, $scheduledStartTime, $messageBus);

        self::assertEquals([new LectureScheduled($lectureId, $groupId, $scheduledStartTime)], $messageBus->messages);
    }

    public function testItReschedulesWithEvent(): void
    {
        $messageBus = new MessageBusStub();
        $lectureId = Uuid::v7();
        $lecture = Lecture::schedule($lectureId, Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $newScheduledStartTime = new \DateTimeImmutable();
        $at = new \DateTimeImmutable();

        $lecture->reschedule($at, $newScheduledStartTime, $messageBus);

        self::assertEquals([new LectureRescheduled($lectureId, $at, $newScheduledStartTime)], $messageBus->messages);
    }

    public function testItCannotRescheduleStartedLecture(): void
    {
        $lecture = Lecture::schedule(Uuid::v7(), Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());

        $this->expectExceptionObject(new CannotRescheduleStartedLecture());

        $lecture->reschedule(new \DateTimeImmutable(), new \DateTimeImmutable(), new MessageBusStub());
    }

    public function testItCannotRescheduleFinishedLecture(): void
    {
        $lecture = Lecture::schedule(Uuid::v7(), Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());
        $lecture->finish(new \DateTimeImmutable(), new MessageBusStub());

        $this->expectExceptionObject(new CannotRescheduleStartedLecture());

        $lecture->reschedule(new \DateTimeImmutable(), new \DateTimeImmutable(), new MessageBusStub());
    }

    public function testItStartsLectureWithEvent(): void
    {
        $messageBus = new MessageBusStub();
        $lectureId = Uuid::v7();
        $at = new \DateTimeImmutable();
        $lecture = Lecture::schedule($lectureId, Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());

        $lecture->start($at, $messageBus);

        self::assertEquals([new LectureStarted($lectureId, $at)], $messageBus->messages);
    }

    public function testItCannotStartStartedLecture(): void
    {
        $lecture = Lecture::schedule(Uuid::v7(), Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());

        $this->expectExceptionObject(new CannotStartAlreadyStartedLecture());

        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());
    }

    public function testItCannotStartFinishedLecture(): void
    {
        $lecture = Lecture::schedule(Uuid::v7(), Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());
        $lecture->finish(new \DateTimeImmutable(), new MessageBusStub());

        $this->expectExceptionObject(new CannotStartAlreadyStartedLecture());

        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());
    }

    public function testItFinishesLectureWithEvent(): void
    {
        $messageBus = new MessageBusStub();
        $lectureId = Uuid::v7();
        $at = new \DateTimeImmutable();
        $lecture = Lecture::schedule($lectureId, Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());

        $lecture->finish($at, $messageBus);

        self::assertEquals([new LectureFinished($lectureId, $at)], $messageBus->messages);
    }

    public function testItCannotFinishScheduledLecture(): void
    {
        $lecture = Lecture::schedule(Uuid::v7(), Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());

        $this->expectExceptionObject(new CannotFinishNotStartedLecture());

        $lecture->finish(new \DateTimeImmutable(), new MessageBusStub());
    }

    public function testItCannotFinishFinishedLecture(): void
    {
        $lecture = Lecture::schedule(Uuid::v7(), Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());
        $lecture->finish(new \DateTimeImmutable(), new MessageBusStub());

        $this->expectExceptionObject(new CannotFinishNotStartedLecture());

        $lecture->finish(new \DateTimeImmutable(), new MessageBusStub());
    }
}
