<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use LogicException;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Builder\EnumBuilder;

final class EnumBuilderTest extends TestCase
{
    public function testCreateFromName() : void
    {
        $name = 'SomeEnum';

        $builder = EnumBuilder::createFromName($name);
        $object  = $builder
            ->addValue('Value1', 'EnumName')
            ->addValue('Value2', null, 'Value 2 Description')
            ->build();

        self::assertSame($name, $object['name']);

        $values = $object['values'];

        self::assertCount(2, $values);

        self::assertArrayHasKey('EnumName', $values);
        self::assertSame('Value1', $values['EnumName']['value']);

        self::assertArrayHasKey('Value2', $values);
        self::assertSame('Value 2 Description', $values['Value2']['description']);
    }

    public function testInvalidValue() : void
    {
        $this->expectException(LogicException::class);

        EnumBuilder::createFromName('Enum')->addValue('invalid-value');
    }
}
