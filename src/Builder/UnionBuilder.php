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
 * @phpstan-import-type ResolveType from AbstractType
 * @phpstan-import-type ObjectTypeReference from UnionType
 * @phpstan-import-type UnionConfig from UnionType
 * @phpstan-type Types iterable<ObjectTypeReference>|callable(): iterable<ObjectTypeReference>
 */
class UnionBuilder extends TypeBuilder
{
    /** @phpstan-var ResolveType|null */
    private $resolveType = null;

    /** @phpstan-var Types */
    private $types;

    /** @phpstan-param Types $types */
    final private function __construct(iterable|callable $types, private string|null $name = null)
    {
        $this->types = $types;
    }

    /**
     * @phpstan-param Types $types
     *
     * @return static
     */
    public static function create(string|null $name, iterable|callable $types): self
    {
        return new static($types, $name);
    }

    /**
     * @phpstan-param ResolveType $resolveType
     *
     * @return $this
     */
    public function setResolveType(callable $resolveType): self
    {
        $this->resolveType = $resolveType;

        return $this;
    }

    /** @phpstan-return UnionConfig */
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
