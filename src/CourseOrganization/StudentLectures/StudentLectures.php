<?php

declare(strict_types=1);

namespace App\CourseOrganization\StudentLectures;

use App\Infrastructure\Uuid\Uuid;
use App\Infrastructure\Uuid\UuidMap;

final class StudentLectures
{
    private int $depositedLectures = 0;

    /**
     * @var UuidMap<bool>
     */
    private UuidMap $lectures;

    public function __construct(
        private readonly Uuid $studentId,
    ) {
        /** @var UuidMap<bool> */
        $this->lectures = new UuidMap();
    }

    public function enroll(Uuid $lectureId): void
    {
        $this->lectures[$lectureId] = false;
    }

    /**
     * @param positive-int $number
     */
    public function deposit(int $number): void
    {
        $this->depositedLectures += $number;
    }

    public function withdraw(Uuid $lectureId): void
    {
        --$this->depositedLectures;
        $this->lectures[$lectureId] = true;
    }
}
