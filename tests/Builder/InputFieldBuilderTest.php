<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Builder\InputFieldBuilder;

final class InputFieldBuilderTest extends TestCase
{
    public function testCreate(): void
    {
        $field = InputFieldBuilder::create('SomeField', Type::string())
            ->setDefaultValue(null)
            ->setDescription('SomeDescription')
            ->setDeprecationReason('Reason')
            ->build();

        self::assertSame('SomeField', $field['name']);
        self::assertArrayHasKey('defaultValue', $field);
        self::assertNull($field['defaultValue']);
        self::assertArrayHasKey('description', $field);
        self::assertSame('SomeDescription', $field['description']);
        self::assertArrayHasKey('deprecationReason', $field);
        self::assertSame('Reason', $field['deprecationReason']);
    }

    public function testCreateWithoutDefaultValue(): void
    {
        $field = InputFieldBuilder::create('SomeField', Type::string())
            ->build();

        self::assertArrayNotHasKey('defaultValue', $field);
    }
}
