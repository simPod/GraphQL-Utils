<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Error;

use GraphQL\Error\DebugFlag;
use Throwable;

class FormattedError extends \GraphQL\Error\FormattedError
{
    /**
     * {@inheritdoc}
     */
    public static function createFromException(Throwable $exception, int $debug = DebugFlag::NONE, $internalErrorMessage = null): array
    {
        $arrayError = parent::createFromException($exception, $debug, $internalErrorMessage);

        if ($exception instanceof \GraphQL\Error\Error && $exception->getPrevious() instanceof Error) {
            $arrayError['extensions']['type'] = $exception->getPrevious()->getType();
        }

        return $arrayError;
    }
}
