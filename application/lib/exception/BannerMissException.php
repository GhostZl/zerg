<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/17
 * Time: 17:04
 */

namespace app\lib\exception;


class BannerMissException extends BaseException
{
    //HTTP 状态码 404，200
    protected $code = 404;
    // 具体错误信息
    protected $message = '请求的Banner不存在！';
    //自定义错误代码
    protected $errorCode = 40000;
}