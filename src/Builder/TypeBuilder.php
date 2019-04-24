<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Builder;

use SimPod\GraphQLUtils\Exception\InvalidArgument;
use function Safe\preg_match;

abstract class TypeBuilder
{
    public const VALID_NAME_PATTERN = '~^[_a-zA-Z][_a-zA-Z0-9]*$~';

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    protected function __construct(string $name)
    {
        if (preg_match(self::VALID_NAME_PATTERN, $name) !== 1) {
            throw InvalidArgument::invalidNameFormat($name);
        }

        $this->name = $name;
    }

    /**
     * @return static
     */
    public function setDescription(string $description) : self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function build() : array
    {
        return [
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }
}
