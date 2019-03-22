<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 17:41
 */

namespace app\api\service;

use think\Request;
use think\Cache;
use app\lib\exception\ForbiddenException;
use app\lib\exception\TokenException;
use app\lib\enum\ScopeEnum;

class Token
{
    public static function generateToken() {
        //32个字符组成一组随机字符串
        $randChar = getRandChar(32);
        //用三组字符串进行md5加密
        $itmestamp = $_SERVER['REQUEST_TIME_FLOAT'];
        //salt 盐
        $salt = config('secure.token_salt');
        return md5($randChar.$itmestamp.$salt);
    }

    public static function getCurrentTokenVar($key)
    {
        $token = Request::instance()
            ->header('token');
        $vars = Cache::get($token);
        if (!$vars) {
            throw new TokenException();  
        } else {
            if (!is_array($vars)) {
                $vars = json_decode($vars, true);
            }
        }
        if (array_key_exists($key, $vars)) {
            return $vars[$key];
        } else {
            throw new Exception("尝试获取的Token变量不存在！");
            
        }
    }

    public static function getCurrentUid()
    {
        $uid = self::getCurrentTokenVar('uid');
        return $uid;
    }

    public static function needExclusiveScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if (!$scope) {
            throw new TokenException();
        }
        if ($scope = ScopeEnum::User) {
            return true;
        }
        throw new ForbiddenException();
    }

    public static function needPrimaryScope()
    {
        $scope = self::getCurrentTokenVar('scope');
        if (!$scope) {
            throw new TokenException();
        }
        if ($scope >= ScopeEnum::User) {
            return true;
        }
        throw new ForbiddenException();
    }
}
