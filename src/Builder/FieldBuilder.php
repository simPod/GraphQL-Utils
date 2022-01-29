<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class FieldBuilder
{
    private string $name;

    private Type $type;

    private string|null $description = null;

    private string|null $deprecationReason = null;

    /** @psalm-var callable(mixed, array<mixed>, mixed, ResolveInfo) : mixed|null */
    private $resolve;

    /** @psalm-var array<string, array<string, mixed>>|null */
    private array|null $args = null;

    final private function __construct(string $name, Type $type)
    {
        $this->name = $name;
        $this->type = $type;
    }

    /**
     * @return static
     */
    public static function create(string $name, Type $type): self
    {
        return new static($name, $type);
    }

    /**
     * @return static
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return static
     */
    public function addArgument(string $name, Type $type, ?string $description = null, mixed $defaultValue = null): self
    {
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

        $this->args[$name] = $value;

        return $this;
    }

    /**
     * @see ResolveInfo
     *
     * @param callable(mixed, array<mixed>, mixed, ResolveInfo) : mixed $resolver
     *
     * @return static
     */
    public function setResolver(callable $resolver): self
    {
        $this->resolve = $resolver;

        return $this;
    }

    /**
     * @return static
     */
    public function setDeprecationReason(string $reason): self
    {
        $this->deprecationReason = $reason;

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
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
