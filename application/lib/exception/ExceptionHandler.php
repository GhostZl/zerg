<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/17
 * Time: 16:57
 */

namespace app\lib\exception;



use think\exception\Handle;
use Exception;
use think\Log;
use think\Request;

class ExceptionHandler extends Handle
{
    //HTTP 状态码 404，200
    private $code = 400;

    // 具体错误信息
    private $msg = 'params error';

    //自定义错误代码
    private $errorCode = 10000;

    public function render(Exception $e)
    {
        if ($e instanceof BaseException) {
            //如果是自定义异常
            $this->code = $e->getCode();
            $this->msg = $e->getMessage();
            $this->errorCode = $e->getErrorCode();

        } else {
            $switch = config('app_debug');
            if ($switch) {
                return parent::render($e);
            }
            $this->code = 500;
            $this->msg = '服务器发生错误！';
            $this->errorCode = 999;
            //记录日志
            $this->recordErrorLog($e);
        }
        $request = Request::instance();
        $result = [
            'msg' => $this->msg,
            'errorCode' => $this->errorCode,
            'request_url' => $request->url()
        ];
        return json($result, $this->code);
    }
    private function recordErrorLog(Exception $e)
    {
        Log::init([
            'type' => 'File',
            'path' => LOG_PATH,
            'level' => ['error']
        ]);
        Log::record($e->getMessage(), 'error');
    }
}