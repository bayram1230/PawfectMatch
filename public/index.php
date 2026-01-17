<?php
dd($_ENV['DATABASE_URL'] ?? 'NO ENV');

use App\Kernel;

register_shutdown_function(function () {
    $mb = round(memory_get_peak_usage(true) / 1024 / 1024, 2);

    file_put_contents(
        dirname(__DIR__).'/var/log/memory_peak.log',
        date('H:i:s').' PEAK_MB='.$mb.PHP_EOL,
        FILE_APPEND
    );
});

require dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel(
        $context['APP_ENV'],
        (bool) $context['APP_DEBUG']
    );
};
