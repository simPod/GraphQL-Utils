<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Error;

/**
 * @inheritdoc
 */
class FormattedError extends \GraphQL\Error\FormattedError
{
    /**
     * @inheritdoc
     */
    public static function createFromException($e, $debug = false, $internalErrorMessage = null) : array
    {
        $arrayError = parent::createFromException($e, $debug, $internalErrorMessage);

        if ($e instanceof \GraphQL\Error\Error && $e->getPrevious() instanceof Error) {
            $type       = ['type' => $e->getPrevious()->getType()];
            $arrayError = $type + $arrayError;
        }

        return $arrayError;
    }
}
