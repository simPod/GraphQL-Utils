<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

class ObjectBuilder extends TypeBuilder
{
    /** @var callable|mixed[][] */
    private $fields = [];

    /**
     * @return static
     */
    public static function create(string $name) : self
    {
        return new static($name);
    }

    /**
     * @param callable|mixed[][] $fields
     *
     * @return static
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
