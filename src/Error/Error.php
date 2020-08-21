<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Error;

abstract class Error extends \GraphQL\Error\Error
{
    abstract public function getType(): string;
}
