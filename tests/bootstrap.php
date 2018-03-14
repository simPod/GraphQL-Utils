<?php

declare(strict_types=1);

namespace SimPod\GraphQL\Utils;

use const E_ALL;
use function date_default_timezone_set;
use function error_reporting;

require __DIR__ . '/../vendor/autoload.php';

error_reporting(E_ALL);
date_default_timezone_set('UTC');
