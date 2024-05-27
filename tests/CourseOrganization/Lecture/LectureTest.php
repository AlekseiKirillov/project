<?php

declare(strict_types=1);

namespace App\Tests\CourseOrganization\Lecture;

use App\CourseOrganization\Lecture\Error\CannotFinishNotStartedLecture;
use App\CourseOrganization\Lecture\Error\CannotRescheduleStartedLecture;
use App\CourseOrganization\Lecture\Error\CannotStartFinishedLecture;
use App\CourseOrganization\Lecture\Lecture;
use App\CourseOrganization\Lecture\LectureFinished;
use App\CourseOrganization\Lecture\LectureRescheduled;
use App\CourseOrganization\Lecture\LectureScheduled;
use App\CourseOrganization\Lecture\LectureStarted;
use App\Infrastructure\Uuid\Uuid;
use App\Tests\Infrastructure\MessageBus\MessageBusStub;
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

    public function testItDoesNotStartAgainStartedLecture(): void
    {
        $messageBus = new MessageBusStub();
        $lecture = Lecture::schedule(Uuid::v7(), Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());

        $lecture->start(new \DateTimeImmutable(), $messageBus);

        self::assertCount(0, $messageBus->messages);
    }

    public function testItCannotStartFinishedLecture(): void
    {
        $lecture = Lecture::schedule(Uuid::v7(), Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());
        $lecture->finish(new \DateTimeImmutable(), new MessageBusStub());

        $this->expectExceptionObject(new CannotStartFinishedLecture());

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

    public function testItDoesNotFinishFinishedLecture(): void
    {
        $messageBus = new MessageBusStub();
        $lecture = Lecture::schedule(Uuid::v7(), Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());
        $lecture->finish(new \DateTimeImmutable(), new MessageBusStub());

        $lecture->finish(new \DateTimeImmutable(), $messageBus);

        self::assertCount(0, $messageBus->messages);
    }
}
