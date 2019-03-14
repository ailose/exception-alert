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
    private $params_content;
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
        'simple' => "#### %s
##### 时间：
> %s
##### 环境：
> %s
##### 项目：
> %s
##### 链接：
> %s
##### 请求参数：
> %s
##### 异常：
> %s
##### 负责人：
  %s
",
        'normal' => "#### %s
##### 时间：
> %s
##### 环境：
> %s
##### 项目：
> %s
##### 链接：
> %s
##### 请求参数：
> %s
##### 异常：
> %s
##### 调试：
> %s
##### 负责人：
  %s
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
     * @param $params_content ,
     * @param $trace
     * @param $mode
     */
    public function __construct($url, $exception, $message, $code, $file, $line, $params_content, $trace, $mode = "normal")
    {
        $this->message = $message;
        $this->code = $code;
        $this->file = $file;
        $this->line = $line;
        $this->params_content = $params_content;
        $this->url = $url;
        $this->trace = $trace;
        $this->exception = $exception;
        $this->mode = config('ding.DING_SIMPLE') ?? 'normal';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $developers = explode(',', config('ding.DING_WORKERS'));
        $developers_str = "";
        foreach ($developers as $developer) {
            $developers_str .= "@{$developer} ";
        }
        $title = config('ding.DING_TITLE') ?? '错误异常';
        switch ($this->mode) {
            case 'simple':
                $message = sprintf($this->template[$this->mode],
                    $title,
                    Carbon::now()->toDateTimeString(),
                    config('app.env'),
                    config('app.name'),
                    $this->url,
                    $this->params_content,
                    "$this->exception(code:$this->code): $this->message at $this->file:$this->line",
                    $developers_str
                );
                break;
            default:
                $message = sprintf($this->template[$this->mode],
                    $title,
                    Carbon::now()->toDateTimeString(),
                    config('app.env'),
                    config('app.name'),
                    $this->url,
                    $this->params_content,
                    "$this->exception(code:$this->code): $this->message at $this->file:$this->line",
                    $this->trace,
                    $developers_str
                );
                break;
        }
        try {
            ding()->at($developers)->markdown($title, $message);
        } catch (\Exception $exception) {
            logger($exception->getMessage());
        }
    }
}
