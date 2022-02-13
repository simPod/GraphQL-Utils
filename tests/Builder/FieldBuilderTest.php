<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Builder\FieldBuilder;

final class FieldBuilderTest extends TestCase
{
    public function testCreate(): void
    {
        $field = FieldBuilder::create('SomeField', Type::string())
            ->setDeprecationReason('Deprecated')
            ->setDescription('SomeDescription')
            ->setResolver(static fn (): string => 'Resolver result')
            ->addArgument('arg1', Type::int(), 'Argument Description', 1)
            ->build();

        self::assertSame('SomeField', $field['name']);
        self::assertArrayHasKey('deprecationReason', $field);
        self::assertSame('Deprecated', $field['deprecationReason']);
        self::assertArrayHasKey('description', $field);
        self::assertSame('SomeDescription', $field['description']);

        self::assertArrayHasKey('resolve', $field);
        self::assertIsCallable($field['resolve']);
        self::assertSame('Resolver result', $field['resolve']());

        self::assertIsArray($field['args']);
        self::assertCount(1, $field['args']);
        $args = $field['args'];
        self::assertArrayHasKey('arg1', $args);
        self::assertSame(Type::int(), $args['arg1']['type']);
        self::assertSame('Argument Description', $args['arg1']['description']);
        self::assertSame(1, $args['arg1']['defaultValue']);
    }
}
