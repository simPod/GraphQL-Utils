<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

class InterfaceBuilder extends ObjectBuilder
{
    /** @var callable|null */
    private $resolveType;

    /**
     * @return static
     */
    public function setResolveType(callable $resolveType) : self
    {
        $this->resolveType = $resolveType;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function build() : array
    {
        $parameters                = parent::build();
        $parameters['resolveType'] = $this->resolveType;

        return $parameters;
    }
}
