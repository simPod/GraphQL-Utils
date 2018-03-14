<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Error;

final class CustomError extends \SimPod\GraphQLUtils\Error\Error
{
    public function getType() : string
    {
        return 'CUSTOM_ERROR';
    }
}
