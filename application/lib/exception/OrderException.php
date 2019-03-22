<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/17
 * Time: 17:04
 */

namespace app\lib\exception;


class OrderException extends BaseException
{
    //HTTP 状态码 404，200
    protected $code = 404;
    // 具体错误信息
    protected $message = '订单不存在， 请检查ID';
    //自定义错误代码
    protected $errorCode = 80000;
}