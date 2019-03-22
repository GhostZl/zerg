<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/17
 * Time: 17:00
 */

namespace app\lib\exception;


use think\Exception;
use Throwable;

class BaseException extends Exception
{
    //HTTP 状态码 404，200
    protected $code = 400;
    // 具体错误信息
    protected $message= 'params error';
    //自定义错误代码
    protected $errorCode = 10000;

    public function __construct($params=[])
    {
        if (!is_array($params)) {
            throw new Exception('参数必须为数组！', 10000);
        }
        if (array_key_exists('code', $params)) {
            $this->code = $params['code'];
        }
        if (array_key_exists('message', $params)) {
            $this->message = $params['message'];
        }
        if (array_key_exists('errorCode', $params)) {
            $this->errorCode = $params['errorCode'];
        }
    }

    public function getErrorCode()
    {
        return $this->errorCode;
    }
}