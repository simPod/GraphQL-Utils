<?php

declare(strict_types=1);

namespace SimPod\GraphQL\Utils;

use function assert;
use function is_array;
use function is_callable;

final class ObjectBuilder extends TypeBuilder
{
    /** @var callable|mixed[][] */
    private $fields = [];

    public static function createFromName(string $name) : self
    {
        return new static($name);
    }

    /**
     * @param callable|mixed[][] $fields
     */
    public function setFields($fields) : self
    {
        assert(is_callable($fields) || is_array($fields));

        $this->fields = $fields;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function build() : array
    {
        $parameters           = parent::build();
        $parameters['fields'] = $this->fields;

        return $parameters;
    }
}
