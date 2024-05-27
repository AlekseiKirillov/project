<?php

declare(strict_types=1);

namespace App\Tests\CourseOrganization\Model;

use App\Tests\Infrastructure\MessageBus\MessageBusStub;
use App\Infrastructure\Uuid\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Group::class)]
final class GroupTest extends TestCase
{
    public function testItAddsStudentWithEvent(): void
    {
        $messageBus = new MessageBusStub();
        $groupId = Uuid::v7();
        $studentId = Uuid::v7();
        $group = Group::create($groupId);

        $group->addStudent($studentId, $messageBus);

        self::assertEquals([new StudentAddedToGroup($groupId, $studentId)], $messageBus->messages);
    }

    public function testItThrowsCannotAddMoreStudentsToGroupAfterTenthStudent(): void
    {
        $messageBus = new MessageBusStub();
        $group = Group::create(Uuid::v7());
        for ($i = 0; $i < 10; ++$i) {
            $group->addStudent(Uuid::v7(), $messageBus);
        }

        $this->expectExceptionObject(new CannotAddMoreStudentsToGroup());

        $group->addStudent(Uuid::v7(), $messageBus);
    }

    public function testItDoesNotAddSameStudentAgain(): void
    {
        $messageBus = new MessageBusStub();
        $groupId = Uuid::v7();
        $studentId = Uuid::v7();
        $group = Group::create($groupId);
        $group->addStudent($studentId, $messageBus);

        $group->addStudent($studentId, $messageBus);

        self::assertCount(1, $messageBus->messages);
    }
}
