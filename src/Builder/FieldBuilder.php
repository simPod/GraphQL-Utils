<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\Argument;
use GraphQL\Type\Definition\FieldDefinition;

/**
 * @see Executor
 * @see FieldDefinition
 * @see Argument
 *
 * @psalm-import-type FieldResolver from Executor
 * @psalm-import-type FieldDefinitionConfig from FieldDefinition
 * @psalm-import-type FieldType from FieldDefinition
 * @psalm-import-type ArgumentListConfig from Argument
 * @psalm-import-type ArgumentType from Argument
 */
class FieldBuilder
{
    /** @psalm-var FieldType */
    private mixed $type;

    private string|null $description = null;

    private string|null $deprecationReason = null;

    /** @psalm-var FieldResolver|null */
    private $resolve;

    /** @psalm-var (ArgumentListConfig&array)|null */
    private array|null $args = null;

    /** @psalm-param FieldType $type */
    final private function __construct(private string $name, $type)
    {
        $this->type = $type;
    }

    /**
     * @psalm-param FieldType $type
     *
     * @return static
     */
    public static function create(string $name, $type): self
    {
        return new static($name, $type);
    }

    /** @return $this */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @psalm-param ArgumentType $type
     *
     * @return $this
     */
    public function addArgument(string $name, $type, string|null $description = null, mixed $defaultValue = null): self
    {
        if ($this->args === null) {
            $this->args = [];
        }

        $value = ['type' => $type];

        if ($description !== null) {
            $value['description'] = $description;
        }

        if ($defaultValue !== null) {
            /** @psalm-suppress MixedAssignment */
            $value['defaultValue'] = $defaultValue;
        }

        $this->args[$name] = $value;

        return $this;
    }

    /**
     * @psalm-param FieldResolver $resolver
     *
     * @return $this
     */
    public function setResolver(callable $resolver): self
    {
        $this->resolve = $resolver;

        return $this;
    }

    /** @return $this */
    public function setDeprecationReason(string $reason): self
    {
        $this->deprecationReason = $reason;

        return $this;
    }

    /** @psalm-return FieldDefinitionConfig */
    public function build(): array
    {
        return [
            'args' => $this->args,
            'name' => $this->name,
            'description' => $this->description,
            'deprecationReason' => $this->deprecationReason,
            'resolve' => $this->resolve,
            'type' => $this->type,
        ];
    }
}
