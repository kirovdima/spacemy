<?php
/**
 * Created by PhpStorm.
 * User: kirov
 * Date: 06.07.2018
 * Time: 16:42
 */

namespace App\Logging;

use Monolog\Handler\NativeMailerHandler;
use Monolog\Logger;

class EmailLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param  array  $config
     * @return \Monolog\Logger
     */
    public function __invoke(array $config)
    {
        $logger = new Logger(new Logger('email'));
        $logger->pushHandler(new NativeMailerHandler('kir.dimanum@yandex.ru', 'error log', 'error', Logger::NOTICE));

        return $logger;
    }
}
