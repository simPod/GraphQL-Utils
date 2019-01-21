<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\Type;
use LogicException;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Builder\EnumBuilder;
use SimPod\GraphQLUtils\Builder\FieldBuilder;
use SimPod\GraphQLUtils\Builder\InterfaceBuilder;

final class InterfaceBuilderTest extends TestCase
{
    public function testCreate() : void
    {
        $name = 'InterfaceA';

        $builder     = InterfaceBuilder::create($name);
        $description = 'Description';
        $interface   = $builder
            ->setDescription($description)
            ->setFields([
                FieldBuilder::create('SomeField', Type::string())->build(),
            ])
            ->setResolveType(
                static function (bool $value) : Type {
                    if ($value) {
                        return Type::string();
                    }

                    return Type::int();
                }
            )
            ->build();

        self::assertSame($name, $interface['name']);
        self::assertSame($description, $interface['description']);
        self::assertArrayHasKey('fields', $interface);
        self::assertIsArray($interface['fields']);
        self::assertCount(1, $interface['fields']);
        self::assertArrayHasKey('resolveType', $interface);
        self::assertSame(Type::string(), $interface['resolveType'](true));
        self::assertSame(Type::int(), $interface['resolveType'](false));
    }

    public function testInvalidValue() : void
    {
        $this->expectException(LogicException::class);

        EnumBuilder::create('Enum')->addValue('invalid-value');
    }
}
