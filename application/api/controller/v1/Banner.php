<?php 
namespace app\api\controller\v1;

use app\api\validate\IDMustBePostiveInt;
use app\api\model\Banner as BannerModel;
use app\lib\exception\BannerMissException;

/**
 * 
 */
class Banner
{
    /**
     * [getBanner 获取banner数据]
     * @param  [type] $id [bannerId]
     * @url /banner/:id
     * @http GET
     */
    public function getBanner($id)
    {
        (new IDMustBePostiveInt())->goCheck();

        $banner = BannerModel::getBannerById($id);
        if (!$banner) {
            throw new BannerMissException();
        }
        return json($banner);
    }
}