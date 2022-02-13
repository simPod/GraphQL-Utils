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
    public static function createFromException(Throwable $exception, int $debugFlag = DebugFlag::NONE, string|null $internalErrorMessage = null): array
    {
        $arrayError = parent::createFromException($exception, $debugFlag, $internalErrorMessage);

        if ($exception instanceof \GraphQL\Error\Error && $exception->getPrevious() instanceof Error) {
            $arrayError['extensions']['type'] = $exception->getPrevious()->getType();
        }

        return $arrayError;
    }
}
