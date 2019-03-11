<?php

namespace Ailose\ExceptionAlert;


use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ExceptionAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /**
     * @var
     */
    private $message;
    /**
     * @var
     */
    private $code;
    /**
     * @var
     */
    private $file;
    /**
     * @var
     */
    private $line;
    /**
     * @var
     */
    private $url;
    /**
     * @var
     */
    private $trace;
    /**
     * @var
     */
    private $exception;

    /**
     * @var
     */
    private $mode;

    /**
     * @var array
     */
    private $template = [
        'simple' => "### %s
##### 时间：
- %s
##### 环境：
- %s
##### 项目：
- %s
##### 链接：
- %s
##### 异常：
- %s
##### 负责人：
- %s
",
        'normal' => "### %s
##### 时间：
- %s
##### 环境：
- %s
##### 项目：
- %s
##### 链接：
- %s
##### 异常：
- %s
##### 调试：
- %s
##### 负责人：
- %s
"
    ];

    /**
     * Create a new job instance.
     *
     * @param $url
     * @param $exception
     * @param $message
     * @param $code
     * @param $file
     * @param $line
     * @param $trace
     * @param $mode
     */
    public function __construct($url, $exception, $message, $code, $file, $line, $trace, $mode = "normal")
    {
        $this->message = $message;
        $this->code = $code;
        $this->file = $file;
        $this->line = $line;
        $this->url = $url;
        $this->trace =  $trace;
        $this->exception = $exception;
        $this->mode = $mode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        switch ($this->mode) {
            case 'simple':
                $message = sprintf($this->template[$this->mode],
                    config('app.name')."告警",
                    Carbon::now()->toDateTimeString(),
                    config('app.env'),
                    config('app.name'),
                    $this->url,
                    "$this->exception(code:$this->code): $this->message at $this->file:$this->line",
                    config('ding.DING_WORKERS')
                ); break;
            default:
                $message = sprintf($this->template[$this->mode],
                    config('app.name')."告警",
                    Carbon::now()->toDateTimeString(),
                    config('app.env'),
                    config('app.name'),
                    $this->url,
                    "$this->exception(code:$this->code): $this->message at $this->file:$this->line",
                    $this->trace,
                    config('ding.DING_WORKERS')
                ); break;
        }
        try {
            ding()->markdown(config('app.name')."告警", $message);
        } catch (\Exception $exception) {
            logger($exception->getMessage());
        }
    }
}
