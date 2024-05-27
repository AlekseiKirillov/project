<?php

declare(strict_types=1);

namespace App\CourseOrganization\Model;

use App\Infrastructure\Uuid\Uuid;

final readonly class DebtAppeared
{
    /**
     * @param positive-int $number
     */
    public function __construct(
        public Uuid $studentId,
        public int $number,
    ) {}
}
