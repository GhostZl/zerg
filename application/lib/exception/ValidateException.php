<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/17
 * Time: 18:52
 */

namespace app\lib\exception;


use Throwable;

class ValidateException extends BaseException
{
    //HTTP 状态码 404，200
    protected $code = 400;
    // 具体错误信息
    protected $message = '参数错误';
    //自定义错误代码
    protected $errorCode = 10000;

    public function __construct($message)
    {
        $this->message = $message;
    }
}