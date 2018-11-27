<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Error;

/**
 * {@inheritdoc}
 */
abstract class Error extends \GraphQL\Error\Error
{
    abstract public function getType() : string;
}
