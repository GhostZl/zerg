<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/18
 * Time: 19:35
 */

namespace app\lib\exception;


class ThemeException extends BaseException
{
    //HTTP 状态码 404，200
    protected $code = 404;
    // 具体错误信息
    protected $message = '请求的主题不存在！';
    //自定义错误代码
    protected $errorCode = 30000;
}