<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use GraphQL\Type\Definition\InputObjectType;

/**
 * @see               InputObjectType
 *
 * @phpstan-import-type FieldConfig from InputObjectType
 * @phpstan-import-type InputObjectConfig from InputObjectType
 */
class InputObjectBuilder extends TypeBuilder
{
    /** @var callable():array<FieldConfig>|array<FieldConfig> */
    private $fields = [];

    final private function __construct(private string|null $name)
    {
    }

    /** @return static */
    public static function create(string $name): self
    {
        return new static($name);
    }

    /**
     * @param callable():array<FieldConfig>|array<FieldConfig> $fields
     *
     * @return $this
     */
    public function setFields(callable|array $fields): self
    {
        $this->fields = $fields;

        return $this;
    }

    /** @phpstan-return InputObjectConfig */
    public function build(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'fields' => $this->fields,
        ];
    }
}
