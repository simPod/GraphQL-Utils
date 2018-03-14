<?php

declare(strict_types=1);

namespace SimPod\GraphQL\Utils\Tests;

use LogicException;
use PHPUnit\Framework\TestCase;
use SimPod\GraphQL\Utils\EnumBuilder;

final class EnumBuilderTest extends TestCase
{
    public function testCreateFromName() : void
    {
        $name = 'SomeEnum';

        $builder = EnumBuilder::createFromName($name);
        $object  = $builder
            ->addValue('Value1')
            ->addValue('Value2')
            ->build();

        self::assertSame($name, $object['name']);

        $values = $object['values'];
        self::assertCount(2, $values);
        foreach ($values as $enumValue => $value) {
            self::assertArrayHasKey($enumValue, $values);
            self::assertSame($enumValue, $value['value']);
        }
    }

    public function testInvalidValue() : void
    {
        $this->expectException(LogicException::class);

        EnumBuilder::createFromName('Enum')->addValue('invalid-value');
    }
}
