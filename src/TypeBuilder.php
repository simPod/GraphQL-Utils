<?php

declare(strict_types=1);

namespace SimPod\GraphQL\Utils;

use GraphQL\Type\Definition\InterfaceType;
use LogicException;
use function preg_match;

abstract class TypeBuilder
{
    protected const VALID_NAME_PATTERN = '~^[_a-zA-Z][_a-zA-Z0-9]*$~';

    /** @var string */
    private $name;

    /** @var string */
    private $description;

    /** @var InterfaceType[] */
    private $interfaces = [];

    protected function __construct(string $name)
    {
        if (preg_match(self::VALID_NAME_PATTERN, $name) !== 1) {
            throw new LogicException();
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
     * @return static
     */
    public function addInterface(InterfaceType $interfaceType) : self
    {
        $this->interfaces[] = $interfaceType;

        return $this;
    }

    /**
     * @return mixed[]
     */
    public function build() : array
    {
        return [
            'name'         => $this->name,
            'description'  => $this->description,
            'interfaces'   => $this->interfaces,
            'resolveField' => [DefaultFieldResolver::class, 'resolve'],
        ];
    }
}
