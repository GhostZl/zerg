<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 11:37
 */

namespace app\api\controller\v1;

use app\api\model\Category as CategoryModel;
use app\api\validate\IDMustBePostiveInt;
use app\api\service\Pay as PayService;
use app\api\service\WxNotify;

class Pay extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiceScope' => ['only'=>'getPreOrder']
    ];

    public function getPreOrder($id) {
        (new IDMustBePostiveInt())->goCheck();
        $payService = new PayService($id);
        $result = $payService->pay();
        return json($result);
    }

    public function receiveNotify()
    {
        $notify = new WxNotify();
        $notify->Handle();
    }
}