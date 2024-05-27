<?php

declare(strict_types=1);

namespace App\CourseOrganization\Lecture;

/**
 * @internal
 * @psalm-internal App\CourseOrganization\Lecture
 */
enum LectureStatus
{
    case SCHEDULED;
    case STARTED;
    case FINISHED;
}
