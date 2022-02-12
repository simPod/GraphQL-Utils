<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class UnionBuilder extends TypeBuilder
{
    /** @var callable(object, mixed, ResolveInfo)|null */
    private $resolveType;

    /** @var ObjectType[]|null */
    private ?array $types = null;

    /**
     * @return static
     */
    public static function create(string $name): self
    {
        return new static($name);
    }

    /**
     * @see ResolveInfo Force Jetbrains IDE use
     *
     * @param callable(mixed):ObjectType $resolveType
     *
     * @return $this
     */
    public function setResolveType(callable $resolveType): self
    {
        $this->resolveType = $resolveType;

        return $this;
    }

    /**
     * @param ObjectType[] $types
     *
     * @return $this
     */
    public function setTypes(array $types): self
    {
        $this->types = $types;

        return $this;
    }

    public function build(): array
    {
        $parameters                = parent::build();
        $parameters['types']       = $this->types;
        $parameters['resolveType'] = $this->resolveType;

        return $parameters;
    }
}
