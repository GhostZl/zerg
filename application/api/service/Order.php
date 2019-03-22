<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 17:41
 */

namespace app\api\service;

use app\api\model\Product as ProductModel;
use app\api\model\UserAddress;
use app\api\model\Order as OrderModel;
use app\api\model\OrderProduct as OrderProductModel;
use app\api\service\UserToken as UserTokenService;
use app\lib\enum\OrderStatusEnum;
use app\lib\exception\OrderException;
use app\lib\exception\TokenException;
use think\Db;

class Order
{   
    //订单的商品列表，也就是客户端传来的
    protected $oProducts;
    //数据库的商品
    protected $products;

    protected $uid;

    public function place($uid, $oProducts)
    {
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        $this->uid = $uid;
        $status = $this->getOrderStatus();
        if (!$status['pass']) {
            $status['order_id'] = -1;
            return $status;
        }
        //创建订单
        $orderSnap = $this->snapOrder($status);
        $order = $this->createOrder($orderSnap);
        $order['pass'] = true;
        return $order;
    }
    private function createOrder($snapOrder)
    {
        try {
            Db::startTrans();
            $orderNo = self::makeOrderNo();
            $order = new OrderModel();
            $order->user_id = $this->uid;
            $order->order_no = $orderNo;
            $order->total_price = $snapOrder['orderPrice'];
            $order->total_count = $snapOrder['totalCount'];
            $order->snap_img = $snapOrder['snapImg'];
            $order->snap_name = $snapOrder['snapName'];
            $order->snap_address = $snapOrder['snapAddress'];
            $order->snap_items = json_encode($snapOrder['pStatus']);
            $order->save();
            $orderId = $order->id;
            foreach ($this->oProducts as $key => &$value) {
                $value['order_id'] = $orderId;
            }
            $orderProcut = new OrderProductModel();
            $orderProcut->saveAll($this->oProducts);
            Db::commit();
            return [
                'order_no' => $orderNo,
                'order_id' => $orderId,
                'create_time' => $order->create_time
            ];
        } catch (Exception $e) {
            Db::rollback();
            throw $e;
        }
    }

    private function snapOrder($status)
    {
        $snap = [
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatus' => [],
            'snapAddress' => null,
            'snapName' => '',
            'snapImg' => ''
        ];
        $snap['orderPrice'] = $status['orderPrice'];
        $snap['totalCount'] = $status['totalCount'];
        $snap['pStatus'] = $status['pStatusArray'];
        $snap['snapAddress'] = json_encode($this->getUserAddress());
        $snap['snapName'] = $this->products[0]['name'];
        $snap['snapImg'] = $this->products[0]['main_img_url'];
        if (isset($this->products[1])) {
            $snap['snapName'] = $snap['snapName'].'等';
        }
        return $snap;
    }

    private function getUserAddress()
    {
        $userAddress = UserAddress::where('user_id', $this->uid)->find();
        if (!$userAddress) {
            throw new UserException("用户地址不存在，下单失败");
        }
        return $userAddress->toArray();
    }

    public function checkOrderStock($orderId) {
        $oProducts = OrderProductModel::where('order_id', $orderId)
            ->select();
        $this->oProducts = $oProducts;
        $this->products = $this->getProductsByOrder($oProducts);
        return $this->getOrderStatus();
    }
    private function getOrderStatus()
    {
        $status = [
            'pass' => true,
            'orderPrice' => 0,
            'totalCount' => 0,
            'pStatusArray' => []
        ];
        foreach ($this->oProducts as $oProduct) {
            $pStatus = $this->getProductStatus($oProduct);
            if (!$pStatus['haveStock']) {
                $status['pass'] = false;
            }
            $status['orderPrice'] = floatval(bcadd($status['orderPrice'], $pStatus['totalPrice'], 2));
            $status['totalCount'] += $pStatus['count'];
            $status['pStatusArray'][] = $pStatus;
        }
        return $status;
    }

    private function getProductStatus($oProduct)
    {
        $pIndex = -1;
        $pStatus = [
            'id' => null,
            'haveStock' => false,
            'count' => 0,
            'name' => '',
            'totalPrice' => 0,
        ];
        for ($i=0; $i < count($this->products); $i++) { 
            if ($oProduct['product_id'] == $this->products[$i]['id']) {
                $pIndex = $i;
            }
        }
        if ($pIndex == -1) {
            throw new OrderException([
                'message' => 'id为'.$oProduct['product_id'].'的商品不存在，创建订单失败！'
            ]);
        }
        $product = $this->products[$pIndex];
        $pStatus['id'] = $product['id'];
        $pStatus['count'] = $oProduct['count'];
        $pStatus['totalPrice'] = bcmul($product['price'], $pStatus['count'], 2);
        if ($product['stock'] - $oProduct['count'] >= 0) {
            $pStatus['haveStock'] = true;
        }
        return $pStatus;
    }

    //根据订单商品获取数据库商品
    private function getProductsByOrder($oProducts)
    {
        $oPids = [];
        foreach ($oProducts as $value) {
            $oPids [] = $value['product_id'];
        }
        $products = ProductModel::all($oPids)
            ->visible(['id', 'price', 'stock', 'name', 'main_img_url'])
            ->toArray();
        return $products;
    }

    public static function makeOrderNo()
    {
        $yCode = ['A', 'B', 'C', 'D', 'E', 'C', 'E', 'F', 'G', 'H', 'I', 'J'];
        $orderSn = $yCode[intval(date('Y'))- 2017] . strtoupper(dechex(date('m'))) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $orderSn;
    }
    public static function checkOrderValidate($orderId)
    {
        $order = OrderModel::where('id', $orderId)
            ->find();
        if (!$order) {
            throw new OrderException();
        }
        if (!UserTokenService::isValidOperate($order->user_id)) {
            throw new TokenException([
                'message' => '订单与用户不匹配！',
                'errorCode' => 10003 
            ]);
        }

        if ($order->status != OrderStatusEnum::UNPAID) {
            throw new OrderException([
                'message' => '订单已支付！',
                'errorCode' => 80003,
                'code' => 400
            ]);
            
        }
        return $order->order_no;
    }
}
