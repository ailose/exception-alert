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
        $data = request()->request->all();
        $params_content = $data ? json_encode($data) : "无参数";
        ExceptionAlertJob::dispatch(
            \request()->fullUrl(),
            get_class($exception),
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getFile(),
            $exception->getLine(),
            $params_content,
            $exception->getTraceAsString(),
            $mode
        );
    }

}