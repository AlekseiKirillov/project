<?php

declare(strict_types=1);

namespace App\Tests\Infrastructure\Uuid;

use App\Infrastructure\Uuid\Uuid;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Rfc4122\UuidV7;
use Ramsey\Uuid\Uuid as RamseyUuid;

use function Ramsey\Uuid\v7;

#[CoversClass(Uuid::class)]
final class UuidTest extends TestCase
{
    public function testItCorrectlyReturnsEqualsForEqualUuids(): void
    {
        $uuid = Uuid::v7();
        $uuid2 = Uuid::fromString($uuid->toString());

        self::assertTrue($uuid->equals($uuid2));
    }

    public function testItCorrectlyReturnsEqualsForNotEqualUuids(): void
    {
        $uuid = Uuid::v7();
        $uuid2 = Uuid::v7();

        self::assertFalse($uuid->equals($uuid2));
    }

    public function testItCorrectlyRepresentsItselfAsString(): void
    {
        $validUuidString = v7();

        $uuid = Uuid::fromString($validUuidString);

        self::assertSame($validUuidString, (string) $uuid);
    }

    public function testItIsCorrectlyCreatedFromValidString(): void
    {
        $validUuidString = v7();

        $uuid = Uuid::fromString($validUuidString);

        self::assertSame($validUuidString, $uuid->toString());
    }

    public function testItCannotBeCreatedFromInvalidString(): void
    {
        $invalidUuidString = 'not_a_valid_uuid';

        $this->expectExceptionObject(
            new \InvalidArgumentException(sprintf('Expected valid UUID, got "%s"', $invalidUuidString)),
        );

        Uuid::fromString($invalidUuidString);
    }

    public function testItCorrectlyReturnsItselfFromV7(): Uuid
    {
        $uuid = Uuid::v7();

        self::assertInstanceOf(Uuid::class, $uuid);

        return $uuid;
    }

    public function testItGrowsMonotonously(): void
    {
        $time = new \DateTimeImmutable();

        $uuid = Uuid::v7($time)->toString();
        $uuid2 = Uuid::v7($time->modify('+1 ms'))->toString();

        self::assertGreaterThan($uuid, $uuid2);
    }

    #[Depends('testItCorrectlyReturnsItselfFromV7')]
    public function testItReturnsValidUuidV7(Uuid $uuid): void
    {
        self::assertTrue(RamseyUuid::isValid($uuid->toString()));
    }

    public function testItCorrectlyReturnsItselfFromString(): void
    {
        $uuid = Uuid::fromString(v7());

        self::assertInstanceOf(Uuid::class, $uuid);
    }

    public function testItGeneratesValidUuidVersion7(): void
    {
        $uuid = Uuid::v7();

        $ramseyUuid = RamseyUuid::getFactory()->fromString($uuid->toString());

        self::assertInstanceOf(UuidV7::class, $ramseyUuid);
    }

    public function testItGeneratesValidUuidV7FromTime(): void
    {
        $time = new \DateTimeImmutable('2022-11-03T16:42:58.485', new \DateTimeZone('UTC'));
        $uuid = Uuid::v7($time);

        $ramseyUuid = RamseyUuid::getFactory()->fromString($uuid->toString());

        self::assertInstanceOf(UuidV7::class, $ramseyUuid);
        self::assertEquals($time, $ramseyUuid->getDateTime());
    }
}
