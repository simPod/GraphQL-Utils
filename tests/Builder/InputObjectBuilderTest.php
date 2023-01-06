<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\InputObjectField;
use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Builder\InputFieldBuilder;
use SimPod\GraphQLUtils\Builder\InputObjectBuilder;
use SimPod\GraphQLUtils\Builder\InterfaceBuilder;

final class InputObjectBuilderTest extends TestCase
{
    public function testCreate(): void
    {
        $description = 'To the sichuan-style nachos add ghee, noodles, buttermilk and heated herring.';
        $name        = 'SomeType';
        $interface   = new class () extends InterfaceType {
            public function __construct()
            {
                $builder = InterfaceBuilder::create('InterfaceA');

                parent::__construct($builder->build());
            }
        };

        $fieldResolver = static function (): void {
        };

        $builder = InputObjectBuilder::create($name);
        $object  = $builder
            ->setDescription($description)
            ->setFields(
                [
                    InputFieldBuilder::create('SomeField', Type::string())->build(),
                    new InputObjectField(InputFieldBuilder::create('Another', Type::string())->build()),
                ],
            )
            ->build();

        self::assertArrayHasKey('name', $object);
        self::assertSame($name, $object['name']);
        self::assertArrayHasKey('description', $object);
        self::assertSame($description, $object['description']);
        self::assertIsArray($object['fields']);
        self::assertCount(2, $object['fields']);
    }
}
