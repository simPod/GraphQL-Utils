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
 * @phpstan-import-type FieldResolver from Executor
 * @phpstan-import-type FieldDefinitionConfig from FieldDefinition
 * @phpstan-import-type FieldType from FieldDefinition
 * @phpstan-import-type ArgumentListConfig from Argument
 * @phpstan-import-type ArgumentType from Argument
 */
class FieldBuilder
{
    /** @phpstan-var FieldType */
    private mixed $type;

    private string|null $description = null;

    private string|null $deprecationReason = null;

    /** @phpstan-var FieldResolver|null */
    private $resolve;

    /** @phpstan-var (ArgumentListConfig&array)|null */
    private array|null $args = null;

    /** @phpstan-param FieldType $type */
    final private function __construct(private string $name, $type)
    {
        $this->type = $type;
    }

    /**
     * @phpstan-param FieldType $type
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
     * @phpstan-param ArgumentType $type
     *
     * @return $this
     */
    public function addArgument(
        string $name,
        $type,
        string|null $description = null,
        mixed $defaultValue = null,
        string|null $deprecationReason = null,
    ): self {
        if ($this->args === null) {
            $this->args = [];
        }

        $value = ['type' => $type];

        if ($description !== null) {
            $value['description'] = $description;
        }

        if ($defaultValue !== null) {
            $value['defaultValue'] = $defaultValue;
        }

        if ($deprecationReason !== null) {
            $value['deprecationReason'] = $deprecationReason;
        }

        $this->args[$name] = $value;

        return $this;
    }

    /**
     * @phpstan-param FieldResolver $resolver
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

    /** @phpstan-return FieldDefinitionConfig */
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
