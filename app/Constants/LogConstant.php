<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Constants;

class LogConstant
{
    public static function stream(): string
    {
        if (env('APP_LOG_PHP_OUTPUT', false)) {
            return 'php://output';
        }

        return BASE_PATH . '/runtime/logs/hyperf.log';
    }
}
