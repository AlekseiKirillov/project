<?php

declare(strict_types=1);

namespace App\CourseOrganisation\Model;

use App\Infrastructure\MessageBus\MessageBus;
use App\Infrastructure\Uuid\Uuid;

final class Group
{
    private const int MAX_STUDENTS = 10;

    /** @var list<Uuid> */
    private array $studentIds = [];

    private function __construct(
        private readonly Uuid $groupId,
    ) {
    }

    public static function create(Uuid $groupId): self
    {
        return new self($groupId);
    }

    /**
     * @throws CannotAddMoreStudentsToGroup
     */
    public function addStudent(Uuid $studentId, MessageBus $messageBus): void
    {
        if (\count($this->studentIds) >= self::MAX_STUDENTS) {
            throw new CannotAddMoreStudentsToGroup();
        }

        foreach ($this->studentIds as $existingStudentId) {
            if ($existingStudentId->equals($studentId)) {
                return;
            }
        }

        $this->studentIds[] = $studentId;
        $messageBus->dispatch(new StudentAddedToGroup($this->groupId, $studentId));
    }
}
