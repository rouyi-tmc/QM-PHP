<?php
/**
 * 错误处理
 * Created by PhpStorm.
 * User: Rouyi
 * Date: 2017/10/19
 * Time: 17:37
 * Email: 383442255@qq.com
 * WebSite: http://qimaweb.com Throwable
 */

namespace App\Http\Classes;

class Errors
{
    /**
     * 启动
     */
    public function __construct()
    {
        error_reporting(0);
        set_error_handler([$this, 'error'], E_ALL);
        set_exception_handler([$this, 'exception']);
        register_shutdown_function([$this, 'fatalError']);
    }

    /**
     * 自定义异常理
     * @param $e
     * @return mixed
     */
    public function exception($e)
    {
        if (PHP_SAPI == 'cli') {
            return message(PHP_EOL . "\033[;36m " . $e->getMessage() . "\x1B[0m\n" . PHP_EOL);
        }

        if (Config('app.debug') === true) {
            return view('errors.exception', ['exception' => $e]);
        }

        return $this->log($e->getMessage());
    }

    /**
     * 错误处理
     * @param $errNo
     * @param $error
     * @param $file
     * @param $line
     * @return bool|mixed
     */
    public function error($errNo, $error, $file, $line)
    {
        $data = [
            'errNo' => $this->errorType($errNo),
            'error' => $error,
            'file' => $file,
            'line' => $line,
            'msg' => $error . "($errNo)" . $file . " ($line).",
        ];

        if (PHP_SAPI == 'cli') {
            return message(PHP_EOL . "\033[;36m {$data['msg']} \x1B[0m\n" . PHP_EOL);
        }

        if (Config('app.debug') === true) {
            return view("errors.debug", $data);
        }

        return $this->log($data['msg']);
    }

    /**
     * 致命错误处理
     * @return mixed
     */
    public function fatalError()
    {
        if (function_exists('error_get_last')) {
            if ($e = error_get_last()) {
                $errNo = $this->errorType($e['type']);
                $error = $e['message'];
                $file = $e['file'];
                $line = $e['line'];
                return $this->error($errNo, $error, $file, $line);
            }
        }
        return false;
    }

    /**
     * 获取错误标识
     * @param $type
     * @return string
     */
    private function errorType($type)
    {
        switch ($type) {
            case E_ERROR:
                $type = 'E_ERROR';
                break;
            case E_WARNING:
                $type = 'E_WARNING';
                break;
            case E_PARSE:
                $type = 'E_PARSE';
                break;
            case E_NOTICE:
                $type = 'E_NOTICE';
                break;
            case E_CORE_ERROR:
                $type = 'E_CORE_ERROR';
                break;
            case E_CORE_WARNING:
                $type = 'E_CORE_WARNING';
                break;
            case E_COMPILE_ERROR:
                $type = 'E_COMPILE_ERROR';
                break;
            case E_COMPILE_WARNING:
                $type = 'E_COMPILE_WARNING';
                break;
            case E_USER_ERROR:
                $type = 'E_USER_ERROR';
                break;
            case E_USER_WARNING:
                $type = 'E_USER_WARNING';
                break;
            case E_USER_NOTICE:
                $type = 'E_USER_NOTICE';
                break;
            case E_STRICT:
                $type = 'E_STRICT';
                break;
            case E_RECOVERABLE_ERROR:
                $type = 'E_RECOVERABLE_ERROR';
                break;
            case E_DEPRECATED:
                $type = 'E_DEPRECATED';
                break;
            case E_USER_DEPRECATED:
                $type = 'E_USER_DEPRECATED';
                break;
        }

        return $type;
    }

    /**
     * 日志处理
     * @param string $message
     * @return mixed
     */
    private function log($message = '')
    {
        $file = storage_path('log/qm.log');
        $message = date('Y-m-d H:i:s') . ':  ' . $message;
        file_put_contents($file, $message . "\n\n", FILE_APPEND);
        return message('程序运行发生错误，由于配置项关闭了调试模式，请打开日志查看错误信息！', '程序运行出错');
    }
}