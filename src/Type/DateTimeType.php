<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Type;

use DateTimeImmutable;
use DateTimeInterface;
use GraphQL\Error\InvariantViolation;
use GraphQL\Language\AST\StringValueNode;
use GraphQL\Language\AST\ValueNode;
use GraphQL\Type\Definition\CustomScalarType;
use GraphQL\Utils\Utils;
use InvalidArgumentException;
use function sprintf;

class DateTimeType extends CustomScalarType
{
    private const NAME        = 'DateTime';
    private const DESCRIPTION = 'The `DateTime` scalar type represents time data, represented as an ISO-8601 encoded UTC date string.';

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
    public function serialize($value) : string
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
    public function parseValue($value) : DateTimeImmutable
    {
        $datetime = DateTimeImmutable::createFromFormat(DateTimeInterface::ATOM, $value);

        if ($datetime === false) {
            throw new InvalidArgumentException(
                sprintf(
                    'DateTime type expects input value to be of ATOM format (%s).',
                    (new DateTimeImmutable())->format(DateTimeInterface::ATOM)
                )
            );
        }

        return $datetime;
    }

    /**
     * @param ValueNode    $valueNode
     * @param mixed[]|null $variables
     *
     * @phpcsSuppress SlevomatCodingStandard.TypeHints.TypeHintDeclaration.MissingParameterTypeHint
     */
    public function parseLiteral($valueNode, ?array $variables = null) : ?DateTimeImmutable
    {
        if (! $valueNode instanceof StringValueNode) {
            return null;
        }

        return $this->parseValue($valueNode->value);
    }
}
