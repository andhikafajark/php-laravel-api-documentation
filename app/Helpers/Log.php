<?php

namespace App\Helpers;

use Exception;
use Illuminate\Support\Facades\Log as LogFacades;

class Log
{
    /**
     * Save Error Exception to log file.
     *
     * @param Exception $e
     * @param string $method
     * @return void
     */
    public static function exception(Exception $e, string $method = ""): void
    {
        LogFacades::error("Error At : $method");
        LogFacades::error("Message : " . $e->getMessage());
        LogFacades::error("Stack Trace : \n" . $e->getTraceAsString());
    }
}
