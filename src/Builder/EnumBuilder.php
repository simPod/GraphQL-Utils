<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\EnumType;
use SimPod\GraphQLUtils\Exception\InvalidArgument;

use function preg_match;

/**
 * @see               EnumType
 *
 * @phpstan-import-type EnumValues from EnumType
 * @phpstan-import-type EnumTypeConfig from EnumType
 */
class EnumBuilder extends TypeBuilder
{
    /**
     * TODO @var (EnumValues&array)|callable(): EnumValues&array
     *
     * @var EnumValues&array
     */
    private array $values = [];

    final private function __construct(private string|null $name)
    {
    }

    /** @return static */
    public static function create(string $name): self
    {
        return new static($name);
    }

    /** @return $this */
    public function addValue(
        int|string $value,
        string|null $name = null,
        string|null $description = null,
        string|null $deprecationReason = null,
    ): self {
        $name ??= (string) $value;
        if (preg_match(self::VALID_NAME_PATTERN, $name) !== 1) {
            throw InvalidArgument::invalidNameFormat($name);
        }

        $enumDefinition = ['value' => $value];
        if ($description !== null) {
            $enumDefinition['description'] = $description;
        }

        if ($deprecationReason !== null) {
            $enumDefinition['deprecationReason'] = $deprecationReason;
        }

        $this->values[$name] = $enumDefinition;

        return $this;
    }

    /** @phpstan-return EnumTypeConfig */
    public function build(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'values' => $this->values,
        ];
    }
}
