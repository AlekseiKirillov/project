<?php

declare(strict_types=1);

namespace App\CourseOrganisation\Model;

use App\Infrastructure\Uuid\Uuid;

final readonly class StudentAddedToGroup
{
    public function __construct(
        public Uuid $groupId,
        public Uuid $studentId,
    ) {
    }
}
