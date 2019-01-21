<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\InterfaceType;

class ObjectBuilder extends TypeBuilder
{
    /** @var InterfaceType[] */
    private $interfaces = [];

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
     * @return static
     */
    public function addInterface(InterfaceType $interfaceType) : self
    {
        $this->interfaces[] = $interfaceType;

        return $this;
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
        $parameters               = parent::build();
        $parameters['interfaces'] = $this->interfaces;
        $parameters['fields']     = $this->fields;

        return $parameters;
    }
}
