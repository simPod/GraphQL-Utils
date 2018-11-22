<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use LogicException;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Builder\FieldBuilder;
use SimPod\GraphQLUtils\Builder\ObjectBuilder;

final class ObjectBuilderTest extends TestCase
{
    public function testCreate() : void
    {
        $description = 'To the sichuan-style nachos add ghee, noodles, buttermilk and heated herring.';
        $name        = 'SomeType';
        $interface   = new class() extends InterfaceType
        {
            public function __construct()
            {
                $builder = ObjectBuilder::create('InterfaceA');
                parent::__construct($builder->build());
            }
        };

        $builder = ObjectBuilder::create($name);
        $object  = $builder
            ->setDescription($description)
            ->addInterface($interface)
            ->setFields([
                FieldBuilder::create('SomeField', Type::string())->build(),
            ])
            ->build();

        self::assertSame($name, $object['name']);
        self::assertSame($description, $object['description']);
        self::assertArrayHasKey('fields', $object);
        self::assertCount(1, $object['fields']);
    }

    public function testInvalidName() : void
    {
        $this->expectException(LogicException::class);

        ObjectBuilder::create('invalid-type-name');
    }
}
