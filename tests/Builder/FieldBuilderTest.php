<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SimPod\GraphQLUtils\Builder\FieldBuilder;
use SimPod\GraphQLUtils\Tests\Builder\fixture\Fields;

final class FieldBuilderTest extends TestCase
{
    public function testCreate(): void
    {
        $field = FieldBuilder::create('SomeField', Type::string())
            ->setDeprecationReason('Deprecated')
            ->setDescription('SomeDescription')
            ->setResolver(static fn (): string => 'Resolver result')
            ->addArgument('arg1', Type::int(), 'Argument Description', 1, 'Reason')
            ->build();

        self::assertSame('SomeField', $field['name']);
        self::assertArrayHasKey('deprecationReason', $field);
        self::assertSame('Deprecated', $field['deprecationReason']);
        self::assertArrayHasKey('description', $field);
        self::assertSame('SomeDescription', $field['description']);

        self::assertArrayHasKey('resolve', $field);
        self::assertIsCallable($field['resolve']);

        $resolveInfoReflection = new ReflectionClass(ResolveInfo::class);
        $resolveInfo = $resolveInfoReflection->newInstanceWithoutConstructor();

        self::assertSame('Resolver result', $field['resolve'](null, [], null, $resolveInfo));

        self::assertArrayHasKey('args', $field);
        self::assertIsArray($field['args']);
        self::assertCount(1, $field['args']);
        $args = $field['args'];
        self::assertArrayHasKey('arg1', $args);
        self::assertIsArray($args['arg1']);
        $arg = $args['arg1'];
        self::assertArrayHasKey('type', $arg);
        self::assertSame(Type::int(), $arg['type']);
        self::assertArrayHasKey('description', $arg);
        self::assertSame('Argument Description', $arg['description']);
        self::assertArrayHasKey('deprecationReason', $arg);
        self::assertSame('Reason', $arg['deprecationReason']);
        self::assertArrayHasKey('defaultValue', $arg);
        self::assertSame(1, $arg['defaultValue']);
    }

    public function testCreateFromEnum(): void
    {
        $field = FieldBuilder::create(Fields::Field1, Type::string())
            ->build();

        self::assertSame('Field1', $field['name']);
    }
}
