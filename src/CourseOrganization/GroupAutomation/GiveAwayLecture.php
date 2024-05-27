<?php

declare(strict_types=1);

namespace App\CourseOrganization\GroupAutomation;

use App\Infrastructure\Uuid\Uuid;

final readonly class GiveAwayLecture
{
    public function __construct(
        public Uuid $groupId,
    ) {
    }
}
