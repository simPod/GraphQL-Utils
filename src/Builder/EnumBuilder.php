<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use SimPod\GraphQLUtils\Exception\InvalidArgument;

use function Safe\preg_match;

class EnumBuilder extends TypeBuilder
{
    /** @var mixed[][] */
    private array $values = [];

    /**
     * @return static
     */
    public static function create(string $name): self
    {
        return new static($name);
    }

    /**
     * @return $this
     */
    public function addValue(string $value, ?string $name = null, ?string $description = null): self
    {
        $name ??= $value;
        if (preg_match(self::VALID_NAME_PATTERN, $name) !== 1) {
            throw InvalidArgument::invalidNameFormat($name);
        }

        $enumDefinition = ['value' => $value];
        if ($description !== null) {
            $enumDefinition['description'] = $description;
        }

        $this->values[$name] = $enumDefinition;

        return $this;
    }

    public function build(): array
    {
        $parameters           = parent::build();
        $parameters['values'] = $this->values;

        return $parameters;
    }
}
