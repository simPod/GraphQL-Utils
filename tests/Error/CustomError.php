<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Error;

use SimPod\GraphQLUtils\Error\Error;

final class CustomError extends Error
{
    public function getType(): string
    {
        return 'CUSTOM_ERROR';
    }
}
