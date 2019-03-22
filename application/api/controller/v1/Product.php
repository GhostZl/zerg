<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 10:49
 */

namespace app\api\controller\v1;


use app\api\validate\CountValidate;
use app\api\model\Product as ProductModel;
use app\api\validate\IDMustBePostiveInt;
use app\lib\exception\ProductException;

class Product
{
    public function getRecent($count=15) {
        (new CountValidate())->goCheck();
        $result = ProductModel::getMostRecent($count);
        $result = $result->hidden(['summary']);
        return json($result);
    }

    public function getAllByCategory($id) {
        (new IDMustBePostiveInt())->goCheck();
        $result = ProductModel::getAllByCategoryId($id);
        return $result;
    }

    public function getOne($id) {
        (new IDMustBePostiveInt())->goCheck();
        $product = ProductModel::getProductDetail($id);
        if (!$product) {
            throw new ProductException();
        }
        return $product;
    }
}