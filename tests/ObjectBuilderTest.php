<?php

declare(strict_types=1);

namespace SimPod\GraphQL\Utils\Tests;

use GraphQL\Type\Definition\Type;
use LogicException;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQL\Utils\FieldBuilder;
use SimPod\GraphQL\Utils\ObjectBuilder;

final class ObjectBuilderTest extends TestCase
{
    public function testCreateFromName() : void
    {
        $description = 'To the sichuan-style nachos add ghee, noodles, buttermilk and heated herring.';
        $name        = 'SomeType';

        $builder = ObjectBuilder::createFromName($name);
        $object  = $builder
            ->setDescription($description)
            ->setFields([
                FieldBuilder::create('SomeField', Type::string())->build(),
            ])
            ->build();

        self::assertSame($name, $object['name']);
        self::assertSame($description, $object['description']);
        self::assertCount(1, $object['fields']);
    }

    public function testInvalidName() : void
    {
        $this->expectException(LogicException::class);

        ObjectBuilder::createFromName('invalid-type-name');
    }
}
