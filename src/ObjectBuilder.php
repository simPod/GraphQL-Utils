<?php

declare(strict_types=1);

namespace SimPod\GraphQL\Utils;

class ObjectBuilder extends TypeBuilder
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
