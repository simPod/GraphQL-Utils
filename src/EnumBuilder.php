<?php

declare(strict_types=1);

namespace SimPod\GraphQL\Utils;

use LogicException;
use function preg_match;

class EnumBuilder extends TypeBuilder
{
    /** @var mixed[][] */
    private $values = [];

    public static function createFromName(string $name) : self
    {
        return new static($name);
    }

    public function addValue(string $value) : self
    {
        if (preg_match(self::VALID_NAME_PATTERN, $value) !== 1) {
            throw new LogicException();
        }

        $this->values[$value] = ['value' => $value];

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
