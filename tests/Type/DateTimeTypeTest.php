<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Type;

use DateTimeInterface;
use DateTimeZone;
use Generator;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\BooleanValueNode;
use GraphQL\Language\AST\StringValueNode;
use PHPUnit\Framework\TestCase;
use Safe\DateTime;
use Safe\DateTimeImmutable;
use SimPod\GraphQLUtils\Exception\InvalidArgument;
use SimPod\GraphQLUtils\Type\DateTimeType;

final class DateTimeTypeTest extends TestCase
{
    /** @dataProvider dateProviderSerialize */
    public function testSerialize(string $expectedValue, DateTimeInterface $dateTime): void
    {
        $dateTimeType = new DateTimeType();
        $value        = $dateTimeType->serialize($dateTime);

        self::assertSame($expectedValue, $value);
    }

    /** @return Generator<int, array{string, DateTimeInterface}> */
    public static function dateProviderSerialize(): Generator
    {
        yield ['2018-12-31T01:02:03+00:00', new DateTime('2018-12-31 01:02:03')];
        yield ['2018-12-31T01:02:03+00:00', new DateTimeImmutable('2018-12-31 01:02:03')];
    }

    public function testSerializeInvalidType(): void
    {
        $this->expectException(InvariantViolation::class);
        $this->expectExceptionMessage('DateTime is not an instance of DateTimeImmutable nor DateTime: "non datetimetype"');

        $dateTimeType = new DateTimeType();
        $dateTimeType->serialize('non datetimetype');
    }

    /** @dataProvider dataProviderParseValue */
    public function testParseValue(string $valueToParse, DateTimeImmutable $expected): void
    {
        $dateTimeType = new DateTimeType();

        self::assertSame(
            $expected->format(DateTimeInterface::ATOM),
            $dateTimeType->parseValue($valueToParse)->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::ATOM),
        );
    }

    /** @return Generator<string, array{string, DateTimeImmutable}> */
    public static function dataProviderParseValue(): Generator
    {
        // Datetime with hours, minutes and seconds
        yield 'timezone #1' => ['2016-11-01T00:00:00-11:00', new DateTimeImmutable('2016-11-01 11:00:00')];
        yield 'timezone #2' => ['2017-01-07T00:00:00+01:20', new DateTimeImmutable('2017-01-06 22:40')];
        yield 'January last day' => ['2000-01-31T00:00:00Z', new DateTimeImmutable('2000-01-31 00:00')];
        yield 'February last day' => ['2001-02-28T00:00:00Z', new DateTimeImmutable('2001-02-28 00:00')];
        yield 'February leap year last day' => ['2000-02-29T00:00:00Z', new DateTimeImmutable('2000-02-29 00:00')];
        yield 'March last day' => ['2000-03-31T00:00:00Z', new DateTimeImmutable('2000-03-31 00:00')];
        yield 'April last day' => ['2000-04-30T00:00:00Z', new DateTimeImmutable('2000-04-30 00:00')];
        yield 'May last day' => ['2000-05-31T00:00:00Z', new DateTimeImmutable('2000-05-31 00:00')];
        yield 'June last day' => ['2000-06-30T00:00:00Z', new DateTimeImmutable('2000-06-30 00:00')];
        yield 'July last day' => ['2000-07-31T00:00:00Z', new DateTimeImmutable('2000-07-31 00:00')];
        yield 'August last day' => ['2000-08-31T00:00:00Z', new DateTimeImmutable('2000-08-31 00:00')];
        yield 'September last day' => ['2000-09-30T00:00:00Z', new DateTimeImmutable('2000-09-30 00:00')];
        yield 'Octover last day' => ['2000-10-31T00:00:00Z', new DateTimeImmutable('2000-10-31 00:00')];
        yield 'November last day' => ['2000-11-30T00:00:00Z', new DateTimeImmutable('2000-11-30 00:00')];
        yield 'December last day' => ['2000-12-31T00:00:00Z', new DateTimeImmutable('2000-12-31 00:00')];

        // Datetime with hours, minutes, seconds and fractional seconds
        yield '1 fraction' => ['2016-02-01T00:00:00.1Z', new DateTimeImmutable('2016-02-01 00:00:00.100')];
        yield '3 0fractions' => ['2016-02-01T00:00:00.000Z', new DateTimeImmutable('2016-02-01')];
        yield '3 fractions' => ['2016-02-01T00:00:00.990Z', new DateTimeImmutable('2016-02-01 00:00:00.990')];
        yield '6 fractions' => ['2016-02-01T00:00:00.23498Z', new DateTimeImmutable('2016-02-01 00:00:00.23498')];
        yield 'fractions with timezone' => ['2017-01-07T11:25:00.450+01:00', new DateTimeImmutable('2017-01-07 10:25:0.450')];
    }

    /** @dataProvider dataProviderParseValueInvalidFormatOrValue */
    public function testParseValueInvalidFormatOrValue(string $value): void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('DateTime type expects input value to be ISO 8601 compliant.');

        $dateTimeType = new DateTimeType();
        $dateTimeType->parseValue($value);
    }

    /** @return Generator<int, array{string}> */
    public static function dataProviderParseValueInvalidFormatOrValue(): Generator
    {
        yield ['2021-02-29T00:00:00Z'];
        yield ['1900-02-29T00:00:00Z'];
        yield ['2017-01-001T00:00:00Z'];
        yield ['2017-02-31T00:00:00Z'];
        yield ['2018-11-31T00:00:00Z'];
        yield ['2019-02-29T00:00:00Z'];
        yield ['2020-02-30T00:00:00Z'];
        yield ['not-datetime string'];
    }

    public function testParseLiteral(): void
    {
        $dateTimeType = new DateTimeType();
        $actual       = $dateTimeType->parseLiteral(new StringValueNode(['value' => '2018-12-31T01:02:03+00:00']));

        self::assertNotNull($actual);
        self::assertSame(
            (new DateTimeImmutable('2018-12-31 01:02:03'))->format(DateTimeInterface::ATOM),
            $actual->format(DateTimeInterface::ATOM),
        );
    }

    public function testParseLiteralIncompatibleNode(): void
    {
        $dateTimeType = new DateTimeType();

        self::assertNull($dateTimeType->parseLiteral(new BooleanValueNode(['value' => false])));
    }
}
