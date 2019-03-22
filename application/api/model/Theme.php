<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/18
 * Time: 18:09
 */

namespace app\api\model;


class Theme extends BaseModel
{
    protected $hidden = ['delete_time', 'update_time', 'head_img_id', 'topic_img_id'];

    public function topicImg() {
        return $this->hasOne('Image', 'id', 'topic_img_id');
    }

    public function headImg() {
        return $this->hasOne('Image', 'id', 'head_img_id');
    }

    public function products() {
        return $this->belongsToMany('Product', 'theme_product', 'product_id', 'theme_id');
    }
}