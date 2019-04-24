<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use LogicException;
use function Safe\preg_match;

class EnumBuilder extends TypeBuilder
{
    /** @var mixed[][] */
    private $values = [];

    public static function create(string $name) : self
    {
        return new static($name);
    }

    public function addValue(string $value, ?string $name = null, ?string $description = null) : self
    {
        $name = $name ?? $value;
        if (preg_match(self::VALID_NAME_PATTERN, $name) !== 1) {
            throw new LogicException();
        }

        $enumDefinition = ['value' => $value];
        if ($description !== null) {
            $enumDefinition['description'] = $description;
        }
        $this->values[$name] = $enumDefinition;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function build() : array
    {
        $parameters           = parent::build();
        $parameters['values'] = $this->values;

        return $parameters;
    }
}
