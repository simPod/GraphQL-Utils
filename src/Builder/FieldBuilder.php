<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;

class FieldBuilder
{
    /** @var array<string, mixed|array<string|mixed>> */
    private $parameters = [];

    final private function __construct(string $name, Type $type)
    {
        $this->parameters['name'] = $name;
        $this->parameters['type'] = $type;
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
        $this->parameters['description'] = $description;

        return $this;
    }

    /**
     * @return static
     */
    public function addArgument(string $name, Type $type, ?string $description = null): self
    {
        $this->parameters['args'][$name] = ['type' => $type];

        if ($description !== null) {
            $this->parameters['args'][$name]['description'] = $description;
        }

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
        $this->parameters['resolve'] = $resolver;

        return $this;
    }

    /**
     * @return static
     */
    public function setDeprecationReason(string $reason): self
    {
        $this->parameters['deprecationReason'] = $reason;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function build(): array
    {
        return $this->parameters;
    }
}
