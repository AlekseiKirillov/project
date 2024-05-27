<?php

declare(strict_types=1);

namespace App\CourseOrganization\Model;

use App\Infrastructure\MessageBus\MessageBus;
use App\Infrastructure\Uuid\Uuid;

final class StudentLectureBalance
{
    /**
     * @var int<0, max>
     */
    private int $enrolledLecturesNumber = 0;

    private int $depositedLecturesNumber = 0;

    private function __construct(
        private readonly Uuid $studentId,
    ) {}

    public static function createFromStudentAddedToGroup(StudentAddedToGroup $event): self
    {
        return new self($event->studentId);
    }

    public function enroll(MessageBus $messageBus): void
    {
        ++$this->enrolledLecturesNumber;

        if ($this->depositedLecturesNumber === 0) {
            $messageBus->dispatch(new PaidLecturesFinished($this->studentId));
        }
    }

    /**
     * @param positive-int $number
     */
    public function deposit(int $number, MessageBus $messageBus): void
    {
        $this->depositedLecturesNumber += $number;

        if ($this->depositedLecturesNumber < 0) {
            $messageBus->dispatch(new DebtAppeared($this->studentId, -$this->depositedLecturesNumber));
        }
    }

    /**
     * @throws NoEnrolledLecturesToWithdraw
     */
    public function withdraw(MessageBus $messageBus): void
    {
        if ($this->enrolledLecturesNumber === 0) {
            throw new NoEnrolledLecturesToWithdraw();
        }

        --$this->enrolledLecturesNumber;
        --$this->depositedLecturesNumber;

        if ($this->depositedLecturesNumber < 0) {
            $messageBus->dispatch(new DebtAppeared($this->studentId, -$this->depositedLecturesNumber));
        }

        if ($this->enrolledLecturesNumber > 0 && $this->depositedLecturesNumber === 0) {
            $messageBus->dispatch(new PaidLecturesFinished($this->studentId));
        }
    }
}
