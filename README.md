# 钉钉推送异常告警

# 介绍
进行监控和提醒操作

# 要求
- php版本:>=7.0
- laravel版本: Laravel5.5+


# 安装

```php
composer require ailose/exception-alert

```


# 在laravel项目中使用

安装成功后执行
```php
php artisan vendor:publish --provider="DingNotice\DingNoticeServiceProvider"

```
会自动将`ding.php`添加到您项目的配置文件当中

# 相关配置

### 钉钉启用开关
(可选)默认为开启
```php
DING_ENABLED=true
```
### 钉钉的推送token
- (必选)发送钉钉机器人的token，即在您创建机器人之后的access_token
- 钉钉推送链接:https://oapi.dingtalk.com/robot/send?access_token=you-push-token

```php
DING_TOKEN=you-push-token
```
### Inspire And Thanks
[wowiwj/ding-notice ](https://github.com/wowiwj/ding-notice)

publish the config file:

`php artisan vendor:publish --provider="DingNotice\DingNoticeServiceProvider"`

### 配置

config/app.php 
```
    'developers' => '@小明'
```

app/Exceptions/Handler.php
添加

```
 public function report(Exception $exception)
    {
        ExceptionAlertHelper::notify($exception);
        parent::report($exception);
    }

```