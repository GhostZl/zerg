<?php 
namespace app\api\controller\v1;

use app\api\validate\OrderPlace;
use app\api\service\Order as OrderService;
use app\api\service\UserToken as UserTokenService;
use app\api\validate\PagingParameter;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;

/**
 * 
 */
class Order extends BaseController
{
    protected $beforeActionList = [
        'checkExclusiceScope' => ['only'=>'placeOrder'],
        'checkPrimaryScope' => ['only' => 'getOrderListByUser']
    ];

    public function placeOrder($value='')
    {
        (new OrderPlace())->goCheck();
        $products = input('post.products/a');
        $uid = UserTokenService::getCurrentUid();
        $order = (new OrderService())->place($uid, $products);
        return json($order);
    }

    public function getOrderListByUser($page=1, $size=10)
    {
        (new PagingParameter())->goCheck();
        $uid = UserTokenService::getCurrentUid();
        $pagingOrders = OrderModel::getSummaryByUser($uid, $page, $size);
        if ($pagingOrders->isEmpty()) {
            return [
                'data' => [],
                'current' => $page,

            ];
        }
        return [
            'data' => $pagingOrders->hidden(['snap_items', 'snap_address', 'prepay_id'])->toArray(),
            'current' => $page
        ];
    }

    public function getDetail($id)
    {
        $orderDetail = orderModel::get($id);
        if (!$orderDetail) {
            throw new OrderException();
        }
        return $orderDetail->hidden(['prepay_id']);
    }
}