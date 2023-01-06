<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SimPod\GraphQLUtils\Builder\ObjectBuilder;
use SimPod\GraphQLUtils\Builder\UnionBuilder;

final class UnionBuilderTest extends TestCase
{
    public function testCreate(): void
    {
        $name = 'InterfaceA';

        $description = 'Description';

        $typeA = new ObjectType(ObjectBuilder::create('TypeA')->build());
        $typeB = new ObjectType(ObjectBuilder::create('TypeB')->build());

        $union = UnionBuilder::create($name, [$typeA, $typeB])
            ->setDescription($description)
            ->setResolveType(
                /** @param mixed $value */
                static function ($value) use ($typeA, $typeB): ObjectType {
                    self::assertIsBool($value);

                    return $value ? $typeA : $typeB;
                },
            )
            ->build();

        self::assertArrayHasKey('name', $union);
        self::assertSame($name, $union['name']);
        self::assertArrayHasKey('description', $union);
        self::assertSame($description, $union['description']);

        self::assertIsArray($union['types']);
        self::assertCount(2, $union['types']);

        self::assertArrayHasKey('resolveType', $union);
        self::assertIsCallable($union['resolveType']);

        $resolveInfoReflection = new ReflectionClass(ResolveInfo::class);
        $resolveInfo           = $resolveInfoReflection->newInstanceWithoutConstructor();

        self::assertSame($typeA, $union['resolveType'](true, null, $resolveInfo));
        self::assertSame($typeB, $union['resolveType'](false, null, $resolveInfo));
    }
}
