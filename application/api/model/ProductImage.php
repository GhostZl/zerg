<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 19:30
 */

namespace app\api\model;


class ProductImage extends BaseModel
{
    protected $hidden = ['img_id', 'delete_time', 'product_id'];

    public function imgUrl() {
        return $this->belongsTo('Image', 'img_id', 'id');
    }
}