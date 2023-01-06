<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

abstract class TypeBuilder
{
    public const VALID_NAME_PATTERN = '~^[_a-zA-Z][_a-zA-Z0-9]*$~';

    protected string|null $description = null;

    /** @return $this */
    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
