<?php

namespace app\api\service;

use think\Loader;
use app\api\model\Order as OrderModel;
use app\api\model\Product as ProductModel;
use app\api\service\Order as OrderService;
use app\lib\enum\OrderStatusEnum;
use think\Db;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');

class WxNotify extends \WxPayNotify
{
    public function NotifyProcess($data, &$msg)
    {
        if ($data['result_code'] == 'SUCCESS') {
            $orderNo = $data['out_trade_no'];
            try {
                Db::startTrans();
                $order = OrderModel::where('order_no', $orderNo)
                ->find();
                if ($order->status == OrderStatusEnum::UNPAID) {
                    $status = (new OrderService())->checkOrderStock($order->id);
                    if ($status['pass']) {
                        $this->updateOrderStatus($order->id, true);
                        $this->reduceStock($status);
                    } else {
                        $this->updateOrderStatus($order->id, false);
                    }
                }
                Db::commit();
                return true;
            } catch (\Exception $e) {
                Log::error($e);
                Db::rollback();
                return false;
            }
        } else {
            return true;
        }
    }

    private function updateOrderStatus($orderId, $success)
    {
        $status = $success ? OrderStatusEnum::PAID : OrderStatusEnum::PAID_BUT_OUT_OF;
        OrderModel::where('id', $orderId)->update(['status'=>$status]);
    }
    private function reduceStock()
    {
        foreach ($stockStatus['pStatusArray'] as $value) {
            ProductModel::where('id', $value['id'])
                ->setDec('stock', $value['count']);
        }
    }
}
