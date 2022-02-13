<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

class InputObjectBuilder extends TypeBuilder
{
    /** @var list<array<string, mixed>>|callable():list<array<string, mixed>> */
    private $fields = [];

    /**
     * @return static
     */
    public static function create(string $name): self
    {
        return new static($name);
    }

    /**
     * @param list<array<string, mixed>>|callable():list<array<string, mixed>> $fields
     *
     * @return $this
     */
    public function setFields(callable|array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /**
     * @psalm-return array<string, mixed>
     */
    public function build(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'fields' => $this->fields,
        ];
    }
}
