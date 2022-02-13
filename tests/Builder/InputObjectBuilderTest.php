<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use GraphQL\Type\Definition\Type;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Builder\InputFieldBuilder;
use SimPod\GraphQLUtils\Builder\InputObjectBuilder;

final class InputObjectBuilderTest extends TestCase
{
    public function testCreate(): void
    {
        $description = 'To the sichuan-style nachos add ghee, noodles, buttermilk and heated herring.';
        $name        = 'SomeType';

        $builder = InputObjectBuilder::create($name);
        $object  = $builder
            ->setDescription($description)
            ->setFields(
                [
                    InputFieldBuilder::create('SomeField', Type::string())->build(),
                ]
            )
            ->build();

        self::assertArrayHasKey('name', $object);
        self::assertSame($name, $object['name']);
        self::assertArrayHasKey('description', $object);
        self::assertSame($description, $object['description']);
        self::assertIsArray($object['fields']);
        self::assertCount(1, $object['fields']);
    }
}
