<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/17
 * Time: 16:22
 */

namespace app\api\model;



class Banner extends BaseModel
{
    protected $hidden = ['update_time', 'delete_time'];
    public function items () {
        return $this->hasMany('BannerItem', 'banner_id', 'id');
    }

    public static function getBannerById($id)
    {
        $banner = self::with(['items', 'items.img'])->find($id);
        return $banner;
    }
}