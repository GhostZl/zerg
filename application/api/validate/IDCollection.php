<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/18
 * Time: 19:06
 */

namespace app\api\validate;


class IDCollection extends BaseValidate
{
    protected $rule = [
        'ids' => 'require|checkIds'
    ];
    protected $message = [
        'ids' => 'ids参数必须是以逗号分隔的正整数'
    ];

    protected function checkIds($value) {
        $values = explode(',', $value);
        if (empty($values)) {
            return false;
        }
        foreach ($values as $id) {
            if(!$this->isPositiveInteger($id)) {
                return false;
            }
        }
        return true;
    }
}