<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/17
 * Time: 16:22
 */

namespace app\api\model;



class Order extends BaseModel
{
    protected $autoWriteTimestamp = true;
    protected $hidden = ['update_time', 'delete_time'];

    public static function getSummaryByUser($uid, $page=1, $size=10)
    {
        return self::where('user_id', '=', $uid)
            ->order('create_time desc')
            ->paginate($size, true, ['page' => $page]);
    }

    public function getSnapItemsAttr($value)
    {
        if (empty($value)) {
            return null;
        }
        return json_decode($value);
    }

    public function getSnapAddressAttr($value)
    {
        if (empty($value)) {
            return null;
        }
        return json_decode($value);
    }
}