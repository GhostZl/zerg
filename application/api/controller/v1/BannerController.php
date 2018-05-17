<?php 
namespace app\api\controller\v1;

use app\api\validate\TestValidate;
use think\Validate;

/**
 * 
 */
class BannerController
{
    /**
     * [getBanner 获取banner数据]
     * @param  [type] $id [bannerId]
     * @url /banner/:id
     * @http GET
     */
    public function getBanner($id)
    {
        $data = [
            'name' => 'vendor111111111',
            'email' => 'vendorqq.com'
        ];
        $validate = new TestValidate();
        $result = $validate->batch()->check($data);
        var_dump($validate->getError());
    }
}