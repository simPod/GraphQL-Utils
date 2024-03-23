<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Executor\Executor;
use GraphQL\Type\Definition\Argument;
use GraphQL\Type\Definition\InputObjectField;
use ReflectionProperty;

/**
 * @see               Executor
 * @see               InputObjectField
 * @see               Argument
 *
 * @phpstan-import-type FieldResolver from Executor
 * @phpstan-import-type InputObjectFieldConfig from InputObjectField
 * @phpstan-import-type ArgumentListConfig from Argument
 * @phpstan-import-type ArgumentType from Argument
 */
class InputFieldBuilder
{
    /** @phpstan-var ArgumentType */
    private mixed $type;

    private string|null $deprecationReason = null;

    private string|null $description = null;

    private mixed $defaultValue;

    /** @phpstan-param ArgumentType $type */
    final private function __construct(private string $name, $type)
    {
        $this->type = $type;
    }

    /**
     * @phpstan-param ArgumentType $type
     *
     * @return static
     */
    public static function create(string $name, $type): self
    {
        return new static($name, $type);
    }

    /** @return $this */
    public function setDefaultValue(mixed $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /** @return $this */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /** @return $this */
    public function setDeprecationReason(string|null $deprecationReason): self
    {
        $this->deprecationReason = $deprecationReason;

        return $this;
    }

    /** @phpstan-return InputObjectFieldConfig */
    public function build(): array
    {
        $config = [
            'name' => $this->name,
            'deprecationReason' => $this->deprecationReason,
            'description' => $this->description,
            'type' => $this->type,
        ];

        $property = new ReflectionProperty($this, 'defaultValue');
        if ($property->isInitialized($this)) {
            $config['defaultValue'] = $this->defaultValue;
        }

        return $config;
    }
}
