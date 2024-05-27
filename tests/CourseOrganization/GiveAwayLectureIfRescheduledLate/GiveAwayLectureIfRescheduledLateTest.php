<?php

declare(strict_types=1);

namespace App\Tests\CourseOrganization\GiveAwayLectureIfRescheduledLate;

use App\CourseOrganization\GiveAwayLectureIfRescheduledLate\GiveAwayLectureIfRescheduledLate;
use App\CourseOrganization\Lecture\LectureRescheduled;
use App\CourseOrganization\Lecture\LectureScheduled;
use App\Infrastructure\Uuid\Uuid;
use App\Tests\Infrastructure\MessageBus\MessageBusStub;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

#[CoversClass(GiveAwayLectureIfRescheduledLate::class)]
final class GiveAwayLectureIfRescheduledLateTest extends TestCase
{
    #[TestWith([new \DateTimeImmutable('28.11.2023 19:30')])]
    #[TestWith([new \DateTimeImmutable('27.11.2023 19:30')])]
    public function testItDoesNotGiveAwayLectureIfRescheduledInTime(\DateTimeImmutable $rescheduledAt): void
    {
        $messageBus = new MessageBusStub();
        $lectureId = Uuid::v7();
        $groupId = Uuid::v7();
        $saga = GiveAwayLectureIfRescheduledLate::createFromLectureScheduled(new LectureScheduled(
            lectureId: $lectureId,
            groupId: $groupId,
            scheduledStartTime: new \DateTimeImmutable('29.11.2023 19:30'),
        ));

        $saga->onLectureRescheduled(
            new LectureRescheduled($lectureId, new \DateTimeImmutable(), $rescheduledAt),
            $messageBus,
        );

        self::assertCount(0, $messageBus->messages);
    }

    #[TestWith([new \DateTimeImmutable('28.11.2023 19:30:01')])]
    #[TestWith([new \DateTimeImmutable('29.11.2023 19:30')])]
    public function testItGivesAwayLectureIfRescheduledLate(\DateTimeImmutable $rescheduledAt): void
    {
        $messageBus = new MessageBusStub();
        $lectureId = Uuid::v7();
        $groupId = Uuid::v7();
        $saga = GiveAwayLectureIfRescheduledLate::createFromLectureScheduled(new LectureScheduled(
            lectureId: $lectureId,
            groupId: $groupId,
            scheduledStartTime: new \DateTimeImmutable('29.11.2023 19:30'),
        ));

        $saga->onLectureRescheduled(
            new LectureRescheduled($lectureId, new \DateTimeImmutable(), $rescheduledAt),
            $messageBus,
        );

        self::assertCount(1, $messageBus->messages);
    }

    public function testItGivesAwayTwiceIfLectureRescheduledLateTwice(): void
    {
        $messageBus = new MessageBusStub();
        $lectureId = Uuid::v7();
        $groupId = Uuid::v7();
        $saga = GiveAwayLectureIfRescheduledLate::createFromLectureScheduled(new LectureScheduled(
            lectureId: $lectureId,
            groupId: $groupId,
            scheduledStartTime: new \DateTimeImmutable('29.11.2023 19:30'),
        ));
        $saga->onLectureRescheduled(
            new LectureRescheduled(
                $lectureId,
                new \DateTimeImmutable('30.11.2023 19:30'),
                new \DateTimeImmutable('29.11.2023 19:20')
            ),
            $messageBus,
        );

        $saga->onLectureRescheduled(
            new LectureRescheduled(
                $lectureId,
                new \DateTimeImmutable(),
                new \DateTimeImmutable('30.11.2023 19:20')
            ),
            $messageBus,
        );

        self::assertCount(2, $messageBus->messages);
    }
}
