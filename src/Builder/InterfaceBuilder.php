<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\InterfaceType;

/**
 * @see               InterfaceType
 *
 * @psalm-import-type InterfaceConfig from InterfaceType
 */
class InterfaceBuilder extends TypeBuilder
{
    /** @var InterfaceType[] */
    private array $interfaces = [];

    /** @var callable|null */
    private $resolveType;

    /** @var array<FieldDefinition|array<string, mixed>>|callable():array<FieldDefinition|array<string, mixed>> */
    private $fields = [];

    final private function __construct(private string|null $name)
    {
    }

    /** @return static */
    public static function create(string|null $name = null): self
    {
        return new static($name);
    }

    /** @return $this */
    public function addInterface(InterfaceType $interfaceType): self
    {
        $this->interfaces[] = $interfaceType;

        return $this;
    }

    /**
     * @param callable():array<FieldDefinition|array<string, mixed>>|array<FieldDefinition|array<string, mixed>> $fields
     *
     * @return $this
     */
    public function setFields(callable|array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /** @return $this */
    public function setResolveType(callable $resolveType): self
    {
        $this->resolveType = $resolveType;

        return $this;
    }

    /** @psalm-return InterfaceConfig */
    public function build(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'interfaces' => $this->interfaces,
            'fields' => $this->fields,
            'resolveType' => $this->resolveType,
        ];
    }
}
