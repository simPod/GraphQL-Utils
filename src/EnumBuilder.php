<?php

declare(strict_types=1);

namespace SimPod\GraphQL\Utils;

use function assert;
use function preg_match;

final class EnumBuilder extends TypeBuilder
{
    /** @var mixed[][] */
    private $values = [];

    public static function createFromName(string $name) : self
    {
        return new static($name);
    }

    public function addValue(string $value) : self
    {
        assert(preg_match(self::VALID_NAME_PATTERN, $value));

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
