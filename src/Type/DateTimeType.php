<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Type;

use DateTimeImmutable;
use DateTimeInterface;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\Node;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Utils\Utils;
use InvalidArgumentException;
use SimPod\GraphQLUtils\Exception\InvalidArgument;

use function assert;
use function is_string;
use function Safe\preg_match;
use function Safe\substr;
use function strpos;

class DateTimeType extends CustomScalarType
{
    private const NAME           = 'DateTime';
    private const DESCRIPTION    = 'The `DateTime` scalar type represents time data, represented as an ISO-8601 encoded UTC date string.';
    private const RFC_3339_REGEX = '~^(\d{4}-(0[1-9]|1[012])-(0[1-9]|[12][\d]|3[01])T([01][\d]|2[0-3]):([0-5][\d]):([0-5][\d]|60))(\.\d{1,})?(([Z])|([+|-]([01][\d]|2[0-3]):[0-5][\d]))$~';

    /** @var string */
    public $name = self::NAME;

    /** @var string */
    public $description = self::DESCRIPTION;

    public function __construct()
    {
        parent::__construct(
            [
                'name'        => self::NAME,
                'description' => self::DESCRIPTION,
            ]
        );
    }

    /**
     * @param mixed $value
     */
    public function serialize($value): string
    {
        if (! $value instanceof DateTimeInterface) {
            throw new InvariantViolation(
                'DateTime is not an instance of DateTimeImmutable nor DateTime: ' . Utils::printSafe($value)
            );
        }

        return $value->format(DateTimeInterface::ATOM);
    }

    /**
     * @param mixed $value
     */
    public function parseValue($value): DateTimeImmutable
    {
        if (! is_string($value)) {
            throw new InvalidArgumentException();
        }

        if (! $this->validateDatetime($value)) {
            throw InvalidArgument::valueNotIso8601Compliant($value);
        }

        return new DateTimeImmutable($value);
    }

    /**
     * @param mixed[]|null $variables
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
     */
    public function parseLiteral(Node $valueNode, ?array $variables = null): ?DateTimeImmutable
    {
        if (! $valueNode instanceof StringValueNode) {
            return null;
        }

        return $this->parseValue($valueNode->value);
    }

    private function validateDatetime(string $value): bool
    {
        if (preg_match(self::RFC_3339_REGEX, $value) !== 1) {
            return false;
        }

        $tPosition = strpos($value, 'T');
        assert($tPosition !== false);

        return $this->validateDate(substr($value, 0, $tPosition));
    }

    private function validateDate(string $date): bool
    {
        // Verify the correct number of days for the month contained in the date-string.
        $year  = (int) substr($date, 0, 4);
        $month = (int) substr($date, 5, 2);
        $day   = (int) substr($date, 8, 2);

        switch ($month) {
            case 2: // February
                if ($this->isLeapYear($year) && $day > 29) {
                    return false;
                }

                return $this->isLeapYear($year) || $day <= 28;

            case 4: // April
            case 6: // June
            case 9: // September
            case 11: // November
                if ($day > 30) {
                    return false;
                }

                break;
        }

        return true;
    }

    private function isLeapYear(int $year): bool
    {
        return ($year % 4 === 0 && $year % 100 !== 0) || $year % 400 === 0;
    }
}
