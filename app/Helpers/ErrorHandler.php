<?php
/**
 * Created by PhpStorm.
 * User: moabit
 * Date: 03.03.2018
 * Time: 18:47
 */

namespace Filehosting\Helpers;


class ErrorHandler

{
    /**
     *
     */
    public function register()
    {
        set_exception_handler([$this, 'exceptionHandler']);
        set_error_handler(function ($errno, $errstr, $errfile, $errline) {
            if (!error_reporting()) {
                return;
            }
            throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
        });
    }
    /**
     * @param \Throwable $e
     */
    public function exceptionHandler(\Throwable $e)
    {
        var_dump ($e);
    }
}