<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Error;

use Exception;
use GraphQL\Error\DebugFlag;
use GraphQL\Error\Error;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Error\FormattedError;

final class FormattedErrorTest extends TestCase
{
    public function testNonDebug(): void
    {
        $exception = new Exception('When smashing sun-dried shrimps, be sure they are room temperature.');

        self::assertSame(
            ['message' => 'Internal server error'],
            FormattedError::createFromException($exception),
        );
    }

    public function testDebug(): void
    {
        $exception = new Exception('When smashing sun-dried shrimps, be sure they are room temperature.');

        self::assertSame(
            [
                'message' => 'Internal server error',
                'extensions' => ['debugMessage' => 'When smashing sun-dried shrimps, be sure they are room temperature.'],
            ],
            FormattedError::createFromException($exception, DebugFlag::INCLUDE_DEBUG_MESSAGE),
        );
    }

    public function testInternalMessageModification(): void
    {
        $exception = new Exception('When smashing sun-dried shrimps, be sure they are room temperature.');

        self::assertSame(
            ['message' => 'Try grilling smoothie jumbled with salad cream, decorateed with green curry.'],
            FormattedError::createFromException(
                $exception,
                DebugFlag::NONE,
                'Try grilling smoothie jumbled with salad cream, decorateed with green curry.',
            ),
        );
    }

    public function testGraphQLCustomError(): void
    {
        $error = new class extends Error {
            public function __construct()
            {
                parent::__construct(
                    'Error Message',
                    null,
                    null,
                    [],
                    null,
                    new CustomError(''),
                );
            }
        };

        self::assertSame(
            [
                'message' => 'Error Message',
                'extensions' => ['type' => 'CUSTOM_ERROR'],
            ],
            FormattedError::createFromException($error, DebugFlag::NONE),
        );
    }
}
