<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;

class ObjectBuilder extends TypeBuilder
{
    /** @var InterfaceType[] */
    private array $interfaces = [];

    /** @var callable():array<FieldDefinition|array<string, mixed>>|array<FieldDefinition|array<string, mixed>> */
    private $fields = [];

    /** @var callable(mixed, array<mixed>, mixed, ResolveInfo) : mixed|null */
    private $fieldResolver = null;

    /**
     * @return static
     */
    public static function create(string $name): self
    {
        return new static($name);
    }

    /**
     * @return static
     */
    public function addInterface(InterfaceType $interfaceType): self
    {
        $this->interfaces[] = $interfaceType;

        return $this;
    }

    /**
     * @param callable():array<FieldDefinition|array<string, mixed>>|array<FieldDefinition|array<string, mixed>> $fields
     *
     * @return static
     */
    public function setFields(callable|array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param callable(mixed, array<mixed>, mixed, ResolveInfo) : mixed $fieldResolver
     *
     * @return static
     */
    public function setFieldResolver(callable $fieldResolver): self
    {
        $this->fieldResolver = $fieldResolver;

        return $this;
    }

    public function build(): array
    {
        $parameters                 = parent::build();
        $parameters['interfaces']   = $this->interfaces;
        $parameters['fields']       = $this->fields;
        $parameters['resolveField'] = $this->fieldResolver;

        return $parameters;
    }
}
