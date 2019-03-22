<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/17
 * Time: 15:28
 */

namespace app\api\validate;


use app\lib\exception\ValidateException;
use think\Validate;
use think\Exception;
use think\Request;

class BaseValidate extends Validate
{
    public function goCheck()
    {
        $request = Request::instance();
        $params = $request->param();

        $result = $this->check($params);
        if(!$result){
            $error = $this->error;
            throw new ValidateException(['message'=>$error]);
        }
        else{
            return true;
        }
    }
    protected function isPositiveInteger($value)
    {
        if (is_numeric($value) && is_int($value + 0) && ($value + 0) > 0) {
            return true;
        }
        else{
            return false;
        }
    }

    protected function isNotEmpty($value) {
        if (empty($value)) {
            return false;
        }
        return true;
    }

    protected function isMobile($value)
    {
        $rule = '^1(3|4|5|7|8)[0-9]\d{8}$^';
        $result = preg_match($rule, $value);
        if ($result) {
            return true;
        }
        return false;
    }

    public function getDataByRule($arrays)
    {
        if (isset($arrays['uid']) || isset($arrays['user_id'])) {
            throw new ValidateException(['message'=>'参数中包含有非法的参数名user_id或uid']);
        }
        $newArray = [];
        foreach ($this->rule as $key => $value) {
            $newArray[$key] = $arrays[$key];
        }
        return $newArray;
    }
}
