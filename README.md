# PHP GraphQL Utils for graphql-php

[![GitHub Actions][GA Image]][GA Link]
[![Shepherd Type][Shepherd Image]][Shepherd Link]
[![Code Coverage][Coverage Image]][CodeCov Link]
[![Downloads][Downloads Image]][Packagist Link]
[![Packagist][Packagist Image]][Packagist Link]
[![Infection MSI][Infection Image]][Infection Link]

## Contents

- [Installation](#installation)
- [Features](#features)
    - [Schema Builders](#schema-builders)
        - [ObjectBuilder and FieldBuilder](#objectbuilder-and-fieldbuilder)
        - [EnumBuilder](#enumbuilder)
        - [InterfaceBuilder](#interfacebuilder)
        - [UnionBuilder](#unionbuilder)
    - [Types](#types)
        - [DateTime](#%EF%B8%8F-datetime)
    - [Error Handling](#error-handling)

## Installation

Add as [Composer](https://getcomposer.org/) dependency:

```sh
composer require simpod/graphql-utils
```

## Features

### Schema Builders

Instead of defining your schema as an array, use can use more objective-oriented approach. This library provides set of strictly typed builders that help you build your schema.

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

#### UnionBuilder

✔️ Standard way with `webonyx/graphql-php`

```php
<?php

use GraphQL\Type\Definition\UnionType;

$searchResultType = new UnionType([
    'name' => 'SearchResult',
    'types' => [
        MyTypes::story(),
        MyTypes::user()
    ],
    'resolveType' => static function($value) {
        if ($value->type === 'story') {
            return MyTypes::story();            
        }

        return MyTypes::user();
    }
]);
```

✨ The same can be produced in objective way

```php
<?php

use SimPod\GraphQLUtils\Builder\UnionBuilder;

$character = new UnionType(
    UnionBuilder::create('SearchResult')
        ->setTypes([
            MyTypes::story(),
            MyTypes::user()
        ])
        ->setResolveType(
            static function($value) {
                if ($value->type === 'story') {
                    return MyTypes::story();            
                }
        
                return MyTypes::user();
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
                "category": "validation"
            }
        }
    ]
}
```

[GA Image]: https://github.com/simPod/GraphQL-Utils/workflows/CI/badge.svg

[GA Link]: https://github.com/simPod/GraphQL-Utils/actions?query=workflow%3A%22CI%22+branch%3Amaster

[Shepherd Image]: https://shepherd.dev/github/simPod/GraphQL-Utils/coverage.svg

[Shepherd Link]: https://shepherd.dev/github/simPod/GraphQL-Utils

[Coverage Image]: https://codecov.io/gh/simPod/GraphQL-Utils/branch/master/graph/badge.svg

[CodeCov Link]: https://codecov.io/gh/simPod/GraphQL-Utils/branch/master

[Downloads Image]: https://poser.pugx.org/simpod/graphql-utils/d/total.svg

[Packagist Image]: https://poser.pugx.org/simpod/graphql-utils/v/stable.svg

[Packagist Link]: https://packagist.org/packages/simpod/graphql-utils

[Infection Image]: https://badge.stryker-mutator.io/github.com/simPod/GraphQL-Utils/master

[Infection Link]: https://infection.github.io
