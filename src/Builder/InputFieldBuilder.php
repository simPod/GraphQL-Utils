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
 * @psalm-import-type FieldResolver from Executor
 * @psalm-import-type InputObjectFieldConfig from InputObjectField
 * @psalm-import-type ArgumentListConfig from Argument
 * @psalm-import-type ArgumentType from Argument
 */
class InputFieldBuilder
{
    /** @psalm-var ArgumentType */
    private mixed $type;

    private string|null $deprecationReason = null;

    private string|null $description = null;

    private mixed $defaultValue;

    /** @psalm-param ArgumentType $type */
    final private function __construct(private string $name, $type)
    {
        $this->type = $type;
    }

    /**
     * @psalm-param ArgumentType $type
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

    /** @psalm-return InputObjectFieldConfig */
    public function build(): array
    {
        $config = [
            'name' => $this->name,
            'deprecationReason' => $this->deprecationReason,
            'description' => $this->description,
            'type' => $this->type,
        ];

        $property = new ReflectionProperty($this, 'defaultValue');
        $property->setAccessible(true);
        if ($property->isInitialized($this)) {
            /** @psalm-suppress MixedAssignment */
            $config['defaultValue'] = $this->defaultValue;
        }

        return $config;
    }
}
