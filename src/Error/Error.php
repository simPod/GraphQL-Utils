<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Error;

/** @deprecated Use {@see ProvidesExtensions} */
abstract class Error extends \GraphQL\Error\Error
{
    abstract public function getType(): string;
}
