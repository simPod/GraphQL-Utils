<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Exception;

use Exception;
use GraphQL\Error\ClientAware;
use GraphQL\Utils\Utils;
use SimPod\GraphQLUtils\Builder\TypeBuilder;
use Throwable;

use function sprintf;

final class InvalidArgument extends Exception implements ClientAware
{
    private function __construct(string $message = '', int $code = 0, Throwable|null $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function invalidNameFormat(string $invalidName): self
    {
        return new self(sprintf('Name "%s" does not match pattern "%s"', $invalidName, TypeBuilder::VALID_NAME_PATTERN));
    }

    public static function valueNotIso8601Compliant(mixed $invalidValue): self
    {
        return new self(sprintf(
            'DateTime type expects input value to be ISO 8601 compliant. Given invalid value "%s"',
            Utils::printSafeJson($invalidValue),
        ));
    }

    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return '';
    }
}
