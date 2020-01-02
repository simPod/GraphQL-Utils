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
        $this->expectExceptionMessage('DateTime is not an instance of DateTimeImmutable nor DateTime:');

        $dateTimeType = new DateTimeType();
        $dateTimeType->serialize('non datetimetype');
    }

    /**
     * @dataProvider dataProviderParseValue
     */
    public function testParseValue(string $valueToParse, DateTimeImmutable $expected) : void
    {
        $dateTimeType = new DateTimeType();

        self::assertSame(
            $expected->format(DateTimeInterface::ATOM),
            $dateTimeType->parseValue($valueToParse)->setTimezone(new DateTimeZone('UTC'))->format(DateTimeInterface::ATOM)
        );
    }

    /**
     * @return mixed[][]
     */
    public function dataProviderParseValue() : iterable
    {
        // Datetime with hours, minutes and seconds
        yield ['2016-02-01T00:00:15Z', new DateTimeImmutable('2016-02-01 0:0:15')];
        yield ['2016-11-01T00:00:00-11:00', new DateTimeImmutable('2016-11-01 11:00:00')];
        yield ['2017-01-07T11:25:00+01:00', new DateTimeImmutable('2017-01-07 10:25')];
        yield ['2017-01-07T00:00:00+01:20', new DateTimeImmutable('2017-01-06 22:40')];
        // Datetime with hours, minutes, seconds and fractional seconds
        yield ['2016-02-01T00:00:00.1Z', new DateTimeImmutable('2016-02-01 00:00:00.100')];
        yield ['2016-02-01T00:00:00.000Z', new DateTimeImmutable('2016-02-01')];
        yield ['2016-02-01T00:00:00.990Z', new DateTimeImmutable('2016-02-01 00:00:00.990')];
        yield ['2016-02-01T00:00:00.23498Z', new DateTimeImmutable('2016-02-01 00:00:00.23498')];
        yield ['2017-01-07T11:25:00.450+01:00', new DateTimeImmutable('2017-01-07 10:25:0.450')];
    }

    /**
     * @dataProvider dataProviderParseValueInvalidFormatOrValue
     */
    public function testParseValueInvalidFormatOrValue(string $value) : void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('DateTime type expects input value to be ISO 8601 compliant.');

        $dateTimeType = new DateTimeType();
        $dateTimeType->parseValue($value);
    }

    /**
     * @return string[][]
     */
    public function dataProviderParseValueInvalidFormatOrValue() : iterable
    {
        yield ['2017-01-001T00:00:00Z'];
        yield ['2017-02-31T00:00:00Z'];
        yield ['not-datetime string'];
        yield ['2018-11-31T00:00:00Z'];
        yield ['2019-02-29T00:00:00Z'];
        yield ['2020-02-30T00:00:00Z'];
    }

    public function testParseLiteral() : void
    {
        $dateTimeType = new DateTimeType();
        $actual       = $dateTimeType->parseLiteral(new StringValueNode(['value' => '2018-12-31T01:02:03+00:00']));

        self::assertNotNull($actual);
        self::assertSame(
            (new DateTimeImmutable('2018-12-31 01:02:03'))->format(DateTimeInterface::ATOM),
            $actual->format(DateTimeInterface::ATOM)
        );
    }

    public function testParseLiteralIncompatibleNode() : void
    {
        $dateTimeType = new DateTimeType();

        self::assertNull($dateTimeType->parseLiteral(new BooleanValueNode(['value' => false])));
    }
}
