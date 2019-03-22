<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/18
 * Time: 19:06
 */

namespace app\api\validate;


class OrderPlace extends BaseValidate
{
    protected $rule = [
        'products' => 'require|checkProducts'
    ];
    protected $singleRule = [
        'product_id' => 'require|isPositiveInteger',
        'count' => 'require|isPositiveInteger'
    ];
    protected $message = [
        'ids' => 'ids参数必须是以逗号分隔的正整数'
    ];

    protected function checkProducts($values)
    {
        if (!is_array($values)) {
            throw new ValidateException(['message'=>'商品参数不正确！']);
        }
        if (empty($values)) {
            throw new ValidateException(['message'=>'商品列表不能为空！']);
        }
        foreach ($values as $key => $value) {
            $this->checkProduct($value);
        }
        return true;
    }

    protected function checkProduct($value)
    {
        $validate = new BaseValidate($this->singleRule);
        $result = $validate->check($value);
        if (!$result) {
            throw new ValidateException(['message'=>'商品列表参数错误！']);
        }
    }
}