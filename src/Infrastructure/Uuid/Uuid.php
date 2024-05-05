<?php

declare(strict_types=1);

namespace App\Infrastructure\Uuid;

use Ramsey\Uuid\Uuid as RamseyUuid;

use function Ramsey\Uuid\v7;

/**
 * @psalm-immutable
 */
final readonly class Uuid
{
    /**
     * @param non-empty-string $uuid
     */
    private function __construct(
        private string $uuid,
    ) {
    }

    /**
     * @psalm-pure
     */
    public static function fromString(string $string): self
    {
        if (RamseyUuid::isValid($string)) {
            return new self($string);
        }

        throw new \InvalidArgumentException(sprintf('Expected valid UUID, got "%s"', $string));
    }

    public static function v7(\DateTimeImmutable $time = new \DateTimeImmutable()): self
    {
        return new self(v7($time));
    }

    /**
     * @return non-empty-string
     */
    public function __toString(): string
    {
        return $this->uuid;
    }

    /**
     * @return non-empty-string
     */
    public function toString(): string
    {
        return $this->uuid;
    }

    public function equals(self $uuid): bool
    {
        return $uuid->uuid === $this->uuid;
    }
}
