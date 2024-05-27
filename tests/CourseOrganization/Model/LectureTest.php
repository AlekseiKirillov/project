<?php

declare(strict_types=1);

namespace App\Tests\CourseOrganization\Model;

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

    public function testItThrowsCannotRescheduledAlreadyStartedLecture(): void
    {
        $messageBus = new MessageBusStub();
        $lectureId = Uuid::v7();
        $lecture = Lecture::schedule($lectureId, Uuid::v7(), new \DateTimeImmutable(), new MessageBusStub());
        $lecture->start(new \DateTimeImmutable(), new MessageBusStub());

        $this->expectExceptionObject(new CannotRescheduleStartedLecture());

        $lecture->reschedule(new \DateTimeImmutable(), new \DateTimeImmutable(), $messageBus);
    }
}
