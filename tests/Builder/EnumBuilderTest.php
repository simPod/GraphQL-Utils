<?php

declare(strict_types=1);

namespace SimPod\GraphQLUtils\Tests\Builder;

use PHPUnit\Framework\TestCase;
use SimPod\GraphQLUtils\Builder\EnumBuilder;
use SimPod\GraphQLUtils\Exception\InvalidArgument;

final class EnumBuilderTest extends TestCase
{
    public function testCreate(): void
    {
        $name = 'SomeEnum';

        $builder = EnumBuilder::create($name);
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

    public function testInvalidValue(): void
    {
        $this->expectException(InvalidArgument::class);
        $this->expectExceptionMessage('does not match pattern');

        EnumBuilder::create('Enum')->addValue('invalid-value');
    }
}
