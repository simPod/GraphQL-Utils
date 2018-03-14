<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Error;

use Exception;
use GraphQL\Error\Error;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Error\FormattedError;

final class FormattedErrorTest extends TestCase
{
    public function testNonDebug() : void
    {
        $exception = new Exception('When smashing sun-dried shrimps, be sure they are room temperature.');

        self::assertSame(
            [
                'message'  => 'Internal server error',
                'category' => 'internal',
            ],
            FormattedError::createFromException($exception)
        );
    }

    public function testDebug() : void
    {
        $exception = new Exception('When smashing sun-dried shrimps, be sure they are room temperature.');

        self::assertSame(
            [
                'debugMessage' => 'When smashing sun-dried shrimps, be sure they are room temperature.',
                'message'      => 'Internal server error',
                'category'     => 'internal',
            ],
            FormattedError::createFromException($exception, true)
        );
    }

    public function testInternalMessageModification() : void
    {
        $exception = new Exception('When smashing sun-dried shrimps, be sure they are room temperature.');

        self::assertSame(
            [
                'message'  => 'Try grilling smoothie jumbled with salad cream, decorateed with green curry.',
                'category' => 'internal',
            ],
            FormattedError::createFromException(
                $exception,
                false,
                'Try grilling smoothie jumbled with salad cream, decorateed with green curry.'
            )
        );
    }

    public function testGraphQLCustomError() : void
    {
        $error = new class extends Error
        {
            public function __construct()
            {
                parent::__construct('Error Message',
                    null,
                    null,
                    null,
                    null,
                    new CustomError(''));
            }
        };

        self::assertSame(
            [
                'type'     => 'CUSTOM_ERROR',
                'message'  => 'Error Message',
                'category' => 'graphql',
            ],
            FormattedError::createFromException($error, false)
        );
    }
}
