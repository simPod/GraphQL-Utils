<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\Type;

class FieldBuilder
{
    /** @var mixed[] */
    private $parameters;

    private function __construct(string $name, Type $type)
    {
        $this->parameters['name'] = $name;
        $this->parameters['type'] = $type;
    }

    public static function create(string $name, Type $type) : self
    {
        return new self($name, $type);
    }

    public function setDescription(string $description) : self
    {
        $this->parameters['description'] = $description;

        return $this;
    }

    public function addArgument(string $name, Type $type, ?string $description = null) : self
    {
        $this->parameters['args'][$name] = ['type' => $type];

        if ($description !== null) {
            $this->parameters['args'][$name]['description'] = $description;
        }

        return $this;
    }

    public function setResolver(callable $callback) : self
    {
        $this->parameters['resolve'] = $callback;

        return $this;
    }

    public function setDeprecationReason(string $reason) : self
    {
        $this->parameters['deprecationReason'] = $reason;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function build() : array
    {
        return $this->parameters;
    }
}
