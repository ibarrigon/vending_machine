<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return static function (array $context) {
    /** @var string $environment */
    $environment = $context['APP_ENV'];

    /** @var string $debug */
    $debug = $context['APP_DEBUG'];

    return new Kernel($environment, (bool) $debug);
};
