<?php

declare(strict_types=1);

namespace SimPod\GraphQL\Utils;

use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use Nette\Utils\Strings;
use function is_array;
use function is_object;
use function method_exists;

class DefaultFieldResolver
{
    private const METHOD_PREFIX_GET = 'get';
    private const METHOD_PREFIX_IS  = 'is';
    private const METHOD_PREFIX_HAS = 'has';

    /**
     * @param mixed   $source
     * @param mixed[] $arguments
     * @param mixed   $context
     *
     * @return mixed|null
     */
    public static function resolve($source, array $arguments, $context, ResolveInfo $info)
    {
        $fieldName     = $info->fieldName;
        $resolvedValue = null;

        if (is_array($source) && isset($source[$fieldName])) {
            $resolvedValue = $source[$fieldName];
        }
        if (is_object($source)) {
            if (isset($source->$fieldName)) {
                $resolvedValue = $source->$fieldName;
            }
            foreach ([self::METHOD_PREFIX_GET, self::METHOD_PREFIX_IS, self::METHOD_PREFIX_HAS] as $getterPrefix) {
                $getterName = $getterPrefix . Strings::firstUpper($fieldName);
                if (! method_exists($source, $getterName)) {
                    continue;
                }

                $resolvedValue = $source->$getterName();
            }
            if (method_exists($source, $fieldName)) {
                $resolvedValue = $source->$fieldName();
            }
        }

        return $resolvedValue instanceof Closure
            ? $resolvedValue($source, $arguments, $context)
            : $resolvedValue;
    }
}
