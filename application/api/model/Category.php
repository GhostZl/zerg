<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 11:37
 */

namespace app\api\model;


class Category extends BaseModel
{
    protected $hidden = ['update_time', 'delete_time', 'topic_img_id'];

    public function img() {
        return $this->hasOne('Image', 'id', 'topic_img_id');
    }
}