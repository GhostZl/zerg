<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 19:44
 */

namespace app\lib\exception;


class ForbiddenException extends BaseException
{
    //HTTP 状态码 404，200
    protected $code = 403;
    // 具体错误信息
    protected $message= '权限不足';
    //自定义错误代码
    protected $errorCode = 10001;
}