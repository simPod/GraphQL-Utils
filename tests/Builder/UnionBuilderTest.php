<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\ObjectType;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Builder\ObjectBuilder;
use SimPod\GraphQLUtils\Builder\UnionBuilder;

final class UnionBuilderTest extends TestCase
{
    public function testCreate(): void
    {
        $name = 'InterfaceA';

        $builder     = UnionBuilder::create($name);
        $description = 'Description';

        $typeA = new ObjectType(ObjectBuilder::create('TypeA')->build());
        $typeB = new ObjectType(ObjectBuilder::create('TypeB')->build());

        $union = $builder
            ->setDescription($description)
            ->setTypes(
                [$typeA, $typeB]
            )
            ->setResolveType(
                static function (bool $value) use ($typeA, $typeB): ObjectType {
                    return $value ? $typeA : $typeB;
                }
            )
            ->build();

        self::assertSame($name, $union['name']);
        self::assertSame($description, $union['description']);

        self::assertArrayHasKey('types', $union);
        self::assertIsArray($union['types']);
        self::assertCount(2, $union['types']);

        self::assertArrayHasKey('resolveType', $union);
        self::assertSame($typeA, $union['resolveType'](true));
        self::assertSame($typeB, $union['resolveType'](false));
    }
}
