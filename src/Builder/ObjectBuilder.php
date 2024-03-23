<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

use function is_callable;

/**
 * @see               FieldDefinition
 * @see               ObjectType
 *
 * @phpstan-import-type FieldDefinitionConfig from FieldDefinition
 * @phpstan-import-type ObjectConfig from ObjectType
 */
class ObjectBuilder extends TypeBuilder
{
    /** @var InterfaceType[] */
    private array $interfaces = [];

    /** @var array<FieldDefinition|FieldDefinitionConfig>|callable():array<FieldDefinition|FieldDefinitionConfig> */
    private $fields = [];

    /** @var callable(mixed, array<mixed>, mixed, ResolveInfo) : mixed|null */
    private $fieldResolver;

    final private function __construct(private string|null $name)
    {
    }

    /** @return static */
    public static function create(string $name): self
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
     * @param array<FieldDefinition|FieldDefinitionConfig>|callable():array<FieldDefinition|FieldDefinitionConfig> $fields
     *
     * @return $this
     */
    public function setFields(callable|array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @param FieldDefinition|FieldDefinitionConfig $field
     *
     * @return $this
     */
    public function addField(FieldDefinition|array $field): self
    {
        if (is_callable($this->fields)) {
            $originalFields = $this->fields;
            $closure        = static function () use ($field, $originalFields): array {
                $originalFields   = $originalFields();
                $originalFields[] = $field;

                return $originalFields;
            };
            $this->fields   = $closure;
        } else {
            $this->fields[] = $field;
        }

        return $this;
    }

    /**
     * @param callable(mixed, array<mixed>, mixed, ResolveInfo) : mixed $fieldResolver
     *
     * @return $this
     */
    public function setFieldResolver(callable $fieldResolver): self
    {
        $this->fieldResolver = $fieldResolver;

        return $this;
    }

    /** @phpstan-return ObjectConfig */
    public function build(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'interfaces' => $this->interfaces,
            'fields' => $this->fields,
            'resolveField' => $this->fieldResolver,
        ];
    }
}
