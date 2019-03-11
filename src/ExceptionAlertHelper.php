<?php

namespace Ailose\ExceptionAlert;


class ExceptionAlertHelper
{

    /**
     * @param \Exception $exception
     *
     * @param $mode
     */
    public static function notify($exception, $mode = "normal")
    {
        ExceptionAlertJob::dispatch(
            \request()->fullUrl(),
            get_class($exception),
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString(),
            $mode
        );
    }

}