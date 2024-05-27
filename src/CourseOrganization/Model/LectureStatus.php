<?php

declare(strict_types=1);

namespace App\CourseOrganization\Model;

enum LectureStatus
{
    case SCHEDULED;
    case STARTED;
    case FINISHED;
}
