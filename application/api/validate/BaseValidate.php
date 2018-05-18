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
            throw new ValidateException($error);
        }
        else{
            return true;
        }
    }
}