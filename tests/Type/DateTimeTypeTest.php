<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Type;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Generator;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\StringValueNode;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Type\DateTimeType;

final class DateTimeTypeTest extends TestCase
{
    /**
     * @dataProvider dateProviderSerialize
     */
    public function testSerialize(string $expectedValue, DateTimeInterface $dateTime) : void
    {
        $dateTimeType = new DateTimeType();
        $value        = $dateTimeType->serialize($dateTime);

        self::assertSame($expectedValue, $value);
    }

    /**
     * @return mixed[][]|Generator
     */
    public function dateProviderSerialize() : iterable
    {
        yield ['2018-12-31T01:02:03+00:00', new DateTime('2018-12-31 01:02:03')];
        yield ['2018-12-31T01:02:03+00:00', new DateTimeImmutable('2018-12-31 01:02:03')];
    }

    /**
     * @dataProvider dateProviderSerialize
     */
    public function testSerializeInvalidType() : void
    {
        $this->expectException(InvariantViolation::class);

        $dateTimeType = new DateTimeType();
        $dateTimeType->serialize('non datetimetype');
    }

    public function testParseValue() : void
    {
        $dateTimeType = new DateTimeType();

        self::assertEquals(
            new DateTimeImmutable('2018-12-31 01:02:03'),
            $dateTimeType->parseValue('2018-12-31T01:02:03+00:00')
        );
    }
    public function testParseValueInvalidFormat() : void
    {
        $this->expectException(InvalidArgumentException::class);

        $dateTimeType = new DateTimeType();
        $dateTimeType->parseValue('not-datetime string');
    }

    public function testParseLiteral() : void
    {
        $dateTimeType = new DateTimeType();

        self::assertEquals(
            new DateTimeImmutable('2018-12-31 01:02:03'),
            $dateTimeType->parseLiteral(new StringValueNode(['value' => '2018-12-31T01:02:03+00:00']))
        );
    }

    public function testParseLiteralIncompatibleNode() : void
    {
        $dateTimeType = new DateTimeType();

        self::assertNull($dateTimeType->parseLiteral(new BooleanValueNode(['value' => false])));
    }
}
