# PHP GraphQL Utils for graphql-php

[![Build Status](https://travis-ci.org/simPod/GraphQL-Utils.svg)](https://travis-ci.org/simPod/GraphQL-Utils)
[![Downloads](https://poser.pugx.org/simpod/graphql-utils/d/total.svg)](https://packagist.org/packages/simpod/graphql-utils)
[![Packagist](https://poser.pugx.org/simpod/graphql-utils/v/stable.svg)](https://packagist.org/packages/simpod/graphql-utils)
[![Licence](https://poser.pugx.org/simpod/graphql-utils/license.svg)](https://packagist.org/packages/simpod/graphql-utils)
[![Quality Score](https://scrutinizer-ci.com/g/simPod/GraphQL-Utils/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/simPod/GraphQL-Utils)
[![Code Coverage](https://scrutinizer-ci.com/g/simPod/GraphQL-Utils/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/simPod/GraphQL-Utils)
[![GitHub Issues](https://img.shields.io/github/issues/simPod/GraphQL-Utils.svg?style=flat-square)](https://github.com/simPod/GraphQL-Utils/issues)


## Contents
- [Installation](#installation)
- [Features](#features)
  - [Schema Builders](#schema-builders)
  - [Types](#types)
  - [Error Handling](#error-handling)

## Installation

Add as [Composer](https://getcomposer.org/) dependency:

```sh
composer require simpod/graphql-utils
```

## Features
 
### Schema Builders

Instead of defining your schema as an array, use can use more objective-oriented approach.
This library provides set of strictly typed builders that help you build your schema.

#### ObjectBuilder and FieldBuilder

✔️ Standard way with `webonyx/graphql-php`

```php
<?php

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

$userType = new ObjectType([
    'name' => 'User',
    'description' => 'Our blog visitor',
    'fields' => [
        'firstName' => [
            'type' => Type::string(),
            'description' => 'User first name'
        ],
        'email' => Type::string()
    ],
    'resolveField' => static function(User $user, $args, $context, ResolveInfo $info) {
        switch ($info->fieldName) {
            case 'name':
              return $user->getName();
            case 'email':
              return $user->getEmail();
            default:
              return null;
        }
    }
]);
``` 

✨ The same can be produced in objective way

```php
<?php

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;
use SimPod\GraphQLUtils\Builder\ObjectBuilder;

$userType = new ObjectType(
    ObjectBuilder::create('User')
        ->setDescription('Our blog visitor')
        ->setFields([
            FieldBuilder::create('firstName', Type::string())
                ->setDescription('User first name')
                ->build(),
            FieldBuilder::create('email', Type::string())->build(),
        ])
        ->setFieldResolver(
            static function(User $user, $args, $context, ResolveInfo $info) {
               switch ($info->fieldName) {
                   case 'name':
                     return $user->getName();
                   case 'email':
                     return $user->getEmail();
                   default:
                     return null;
               }
            }
        )
        ->build()
);
```

#### EnumBuilder

✔️ Standard way with `webonyx/graphql-php`

```php
<?php

use GraphQL\Type\Definition\EnumType;

$episodeEnum = new EnumType([
    'name' => 'Episode',
    'description' => 'One of the films in the Star Wars Trilogy',
    'values' => [
        'NEWHOPE' => [
            'value' => 4,
            'description' => 'Released in 1977.'
        ],
        'EMPIRE' => [
            'value' => 5,
            'description' => 'Released in 1980.'
        ],
        'JEDI' => [
            'value' => 6,
            'description' => 'Released in 1983.'
        ],
    ]
]);
```

✨ The same can be produced in objective way

```php
<?php

use GraphQL\Type\Definition\EnumType;
use SimPod\GraphQLUtils\Builder\EnumBuilder;

$episodeEnum = new EnumType( 
    EnumBuilder::create('Episode')
        ->setDescription('One of the films in the Star Wars Trilogy')
        ->addValue(4, 'NEWHOPE', 'Released in 1977.')
        ->addValue(5, 'EMPIRE', 'Released in 1980.')
        ->addValue(6, 'JEDI', 'Released in 1983.')
        ->build()
);
```

#### InterfaceBuilder

✔️ Standard way with `webonyx/graphql-php`

```php
<?php

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;

$character = new InterfaceType([
    'name' => 'Character',
    'description' => 'A character in the Star Wars Trilogy',
    'fields' => [
        'id' => [
            'type' => Type::nonNull(Type::string()),
            'description' => 'The id of the character.',
        ],
        'name' => [
            'type' => Type::string(),
            'description' => 'The name of the character.'
        ]
    ],
    'resolveType' => static function ($value) : object {
        if ($value->type === 'human') {
            return MyTypes::human();            
        }

        return MyTypes::droid();
    }
]);
```

✨ The same can be produced in objective way

```php
<?php

use GraphQL\Type\Definition\InterfaceType;
use GraphQL\Type\Definition\Type;
use SimPod\GraphQLUtils\Builder\InterfaceBuilder;
use SimPod\GraphQLUtils\Builder\FieldBuilder;

$character = new InterfaceType(
    InterfaceBuilder::create('Character')
        ->setDescription('A character in the Star Wars Trilogy')
        ->setFields([
            FieldBuilder::create('id', Type::nonNull(Type::string()))
                ->setDescription('The id of the character.')
                ->build(),
            FieldBuilder::create('name', Type::string())
                ->setDescription('The name of the character.')
                ->build()
        ])
        ->setResolveType(
            static function ($value) : object {
                if ($value->type === 'human') {
                    return MyTypes::human();            
                }
    
                return MyTypes::droid();
            }
        )
        ->build()
);
```

### Types

#### 🕰️ DateTime
 
scalar type that produces `scalar DateTime` in your schema.

[`SimPod\GraphQLUtils\Type\DateTimeType`](https://github.com/simPod/GraphQL-Utils/blob/master/src/Type/DateTimeType.php)

### Error Handling

Extending your exception with `SimPod\GraphQLUtils\Error\Error` forces you to implement `getType()` method.

Example Error class

```php
<?php

use SimPod\GraphQLUtils\Error\Error;

final class InvalidCustomerIdProvided extends Error
{
    private const TYPE = 'INVALID_CUSTOMER_ID_PROVIDED';

    public static function noneGiven() : self
    {
        return new self('No CustomerId provided');
    }

    public function getType() : string
    {
        return self::TYPE;
    }

    public function isClientSafe() : bool
    {
        return true;
    }
}
```

Create your formatter

```php
<?php

use GraphQL\Error\Error;
use SimPod\GraphQLUtils\Error\FormattedError;

$formatError = static function (Error $error) : array
{
   if (! $error->isClientSafe()) {
       // eg. log error
   }

   return FormattedError::createFromException($error);
};

$errorFormatterCallback = static function (Error $error) use ($formatError) : array {
    return $formatError($error);
};
        
$config = GraphQL::executeQuery(/* $args */)
    ->setErrorFormatter($errorFormatterCallback)
    ->setErrorsHandler(
        static function (array $errors, callable $formatter) : array {
            return array_map($formatter, $errors);
        }
    );
```

Error types will then be provided in your response so client can easier identify the error type

```json
{
  "errors": [
    {
      "message": "No CustomerId provided",
      "extensions": {
          "type": "INVALID_CUSTOMER_ID_PROVIDED",
          "category": "graphql"
      }
    }
  ]
}
```
