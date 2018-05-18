<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/17
 * Time: 17:00
 */

namespace app\lib\exception;


use think\Exception;

class BaseException extends Exception
{
    //HTTP 状态码 404，200
    protected $code = 400;
    // 具体错误信息
    protected $message= 'params error';
    //自定义错误代码
    protected $errorCode = 10000;

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}