<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/18
 * Time: 17:31
 */

namespace app\api\model;


use think\Model;

class BaseModel extends Model
{
    public function prefixImgUrl($value) {
        if ($this->from==1) {
            return config('setting.img_prefix').$value;
        }
        return $value;
    }
}