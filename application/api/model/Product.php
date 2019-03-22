<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/18
 * Time: 18:09
 */

namespace app\api\model;


class Product extends BaseModel
{
    protected $hidden = ['delete_time', 'create_time', 'update_time', 'img_id', 'delete_time', 'pivot'];

    public function getMainImgUrlAttr($value) {
        return $this->prefixImgUrl($value);
    }
    public static function getMostRecent($count) {
        $products = self::limit($count)
            ->order('create_time desc')
            ->select();
        return $products;
    }

    public function imgs() {
        return $this->hasMany('ProductImage', 'product_id', 'id');
    }

    public function properties() {
        return $this->hasMany('ProductProperty', 'product_id', 'id');
    }


    public static function getAllByCategoryId($id) {
        $products = self::where('category_id', $id)->select();
        return $products;
    }

    public static function getProductDetail($id) {
        $product = self::with(['imgs' => function ($query) {
            $query->with('imgUrl')
                ->order('order', 'asc');
        }, 'properties'])
            ->find($id);
        return $product;
    }
}