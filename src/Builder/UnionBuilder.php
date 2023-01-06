<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\AbstractType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\UnionType;

/**
 * @see               AbstractType
 * @see               ObjectType
 * @see               UnionType
 *
 * @psalm-import-type ResolveType from AbstractType
 * @psalm-import-type ObjectTypeReference from UnionType
 * @psalm-import-type UnionConfig from UnionType
 * @psalm-type Types iterable<ObjectTypeReference>|callable(): iterable<ObjectTypeReference>
 */
class UnionBuilder extends TypeBuilder
{
    private string|null $name;
    /** @psalm-var ResolveType|null */
    private $resolveType = null;

    /** @psalm-var Types */
    private $types;

    /** @psalm-param Types $types */
    final private function __construct(iterable|callable $types, ?string $name = null)
    {
        $this->types = $types;
        $this->name  = $name;
    }

    /**
     * @psalm-param Types $types
     *
     * @return static
     */
    public static function create(?string $name, iterable|callable $types): self
    {
        return new static($types, $name);
    }

    /**
     * @psalm-param ResolveType $resolveType
     *
     * @return $this
     */
    public function setResolveType(callable $resolveType): self
    {
        $this->resolveType = $resolveType;

        return $this;
    }

    /**
     * @psalm-return UnionConfig
     */
    public function build(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'types' => $this->types,
            'resolveType' => $this->resolveType,
        ];
    }
}
