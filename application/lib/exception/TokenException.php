<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 18:24
 */

namespace app\lib\exception;


class TokenException extends BaseException
{
    //HTTP 状态码 404，200
    protected $code = 401;
    // 具体错误信息
    protected $message= 'Token 已过期或无效token';
    //自定义错误代码
    protected $errorCode = 10000;

}