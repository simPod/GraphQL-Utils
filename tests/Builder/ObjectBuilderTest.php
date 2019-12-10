<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Builder\FieldBuilder;
use SimPod\GraphQLUtils\Builder\ObjectBuilder;
use SimPod\GraphQLUtils\Exception\InvalidArgument;

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

        $fieldResolver = static function () : void {
        };

        $builder = ObjectBuilder::create($name);
        $object  = $builder
            ->setDescription($description)
            ->addInterface($interface)
            ->setFields(
                [
                    FieldBuilder::create('SomeField', Type::string())->build(),
                ]
            )
            ->setFieldResolver($fieldResolver)
            ->build();

        self::assertSame($name, $object['name']);
        self::assertSame($description, $object['description']);
        self::assertArrayHasKey('fields', $object);
        self::assertIsArray($object['fields']);
        self::assertSame($fieldResolver, $object['resolveField']);
        self::assertCount(1, $object['fields']);
    }

    public function testInvalidName() : void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('does not match pattern');

        ObjectBuilder::create('invalid-type-name');
    }
}
