<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 15:44
 */

namespace app\api\service;


use app\api\model\User as UserModel;
use app\lib\exception\WeChatException;
use think\Exception;
use app\lib\enum\ScopeEnum;
use app\lib\exception\TokenException;

class UserToken extends Token
{
    protected $code;
    protected $wxAppId;
    protected $wxAppSecret;
    protected $wxLoginUrl;

    public function __construct($code)
    {
        $this->code = $code;
        $this->wxAppId = config('wx.app_id');
        $this->wxAppSecret = config('wx.app_secret');
        $this->wxLoginUrl = sprintf(config('wx.login_url'), $this->wxAppId, $this->wxAppSecret, $this->code);
    }

    public function get() {
        $result = curl_get($this->wxLoginUrl);
        $wxResult = json_decode($result, true);
        if (empty($wxResult)) {
            throw new Exception('获取session_key及openID异常，微信内部错误！');
        }
        $loginFail = array_key_exists('errcode', $wxResult);
        if ($loginFail) {
            $this->processLoginError($wxResult);
        }
        return $this->grantToken($wxResult);

    }

    private function processLoginError($wxResult) {
        throw new WeChatException([
            'message' => $wxResult['errmsg'],
            'errorCode' => $wxResult['errcode']
        ]);
    }

    private function grantToken($wxResult) {
        // 拿到OpenId
        //判断数据库是否存在，如果存在不处理，否则新增
        //生成令牌，准备缓存数据，写入缓存
        //key:令牌 value:wxResult, uid,scope
        $openId = $wxResult['openid'];
        $user = UserModel::getByOpenId($openId);
        if ($user) {
            $uid = $user->id;
        } else {
            $uid = $this->newUser($openId);
        }
        $cachedValue = $this->prepareCachedValue($wxResult, $uid);
        $token = $this->saveToCache($cachedValue);
        return $token;
    }

    private function prepareCachedValue($wxResult, $uid) {
        $cachedValue = $wxResult;
        $cachedValue['uid'] = $uid;
        //scope 16代表App用户的权限
        $cachedValue['scope'] = ScopeEnum::User;
        return $cachedValue;
    }

    private function saveToCache($cachedValue) {
        $key = self::generateToken();
        $value = json_encode($cachedValue);
        $expire = config('setting.token_expire_in');
        $request = cache($key, $value, $expire);
        if (!$request) {
            throw new TokenException([
                'message' => '服务器缓存异常！',
                'errorCode' => 10005
            ]);
        }
        return $key;
    }

    private function newUser($openId) {
        $user = UserModel::create([
            'openid' => $openId
        ]);
        return $user->id;
    }

    public static function isValidOperate($checkedUid)
    {
        if (!$checkedUid) {
            return false;
        }
        $uid = self::getCurrentUid();
        if ($checkedUid == $uid) {
            return true;
        }
        return false;
    }
}