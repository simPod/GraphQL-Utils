<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SimPod\GraphQLUtils\Builder\EnumBuilder;
use SimPod\GraphQLUtils\Builder\FieldBuilder;
use SimPod\GraphQLUtils\Builder\InterfaceBuilder;
use SimPod\GraphQLUtils\Exception\InvalidArgument;

final class InterfaceBuilderTest extends TestCase
{
    public function testCreate(): void
    {
        $name        = 'InterfaceA';
        $description = 'Description';

        $interfaceA = new class () extends InterfaceType {
            public function __construct()
            {
                $builder = InterfaceBuilder::create('InterfaceA');

                parent::__construct($builder->build());
            }
        };

        $interface = InterfaceBuilder::create($name)
            ->addInterface($interfaceA)
            ->setDescription($description)
            ->setFields(
                [
                    FieldBuilder::create('SomeField', Type::string())->build(),
                ],
            )
            ->setResolveType(
                static fn (bool $value): Type => $value ? Type::string() : Type::int()
            )
            ->build();

        $resolveInfoReflection = new ReflectionClass(ResolveInfo::class);

        $resolveInfo = $resolveInfoReflection->newInstanceWithoutConstructor();

        self::assertArrayHasKey('name', $interface);
        self::assertSame($name, $interface['name']);
        self::assertArrayHasKey('description', $interface);
        self::assertSame($description, $interface['description']);
        self::assertIsArray($interface['fields']);
        self::assertCount(1, $interface['fields']);
        self::assertArrayHasKey('resolveType', $interface);
        self::assertIsCallable($interface['resolveType']);
        self::assertSame(Type::string(), $interface['resolveType'](true, null, $resolveInfo));
        self::assertSame(Type::int(), $interface['resolveType'](false, null, $resolveInfo));
    }

    public function testInvalidValue(): void
    {
        $this->expectException(InvalidArgument::class);

        EnumBuilder::create('Enum')->addValue('invalid-value');
    }
}
