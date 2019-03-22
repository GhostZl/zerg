<?php
/**
 * Created by PhpStorm.
 * User: zhenglei
 * Date: 2018/5/21
 * Time: 17:41
 */

namespace app\api\service;

use think\Loader;
use think\Log;
use app\api\model\Order as OrderModel;
use app\lib\exception\OrderException;

Loader::import('WxPay.WxPay', EXTEND_PATH, '.Api.php');
class Pay
{
    private $orderId;
    private $orderNo;

    function __construct($orderId)
    {
        if (!$orderId) {
            throw new Exception("找不到订单ID");
        }
        $this->orderId = $orderId;
    }

    public function pay()
    {
        $orderService = new Order();
        //检测订单是否存在
        $this->orderNo = Order::checkOrderValidate($this->orderId);
        //库存量检测
        $status = $orderService->checkOrderStock($this->orderId);
        if (!$status['pass']) {
            return $status;
        }
        return $this->makeWxPreOrder($status['orderPrice']);
    }

    private function makeWxPreOrder($totalPrice)
    {
        $openId = Token::getCurrentTokenVar('openid');
        if (!$openId) {
            throw new Exception("Error Processing Request", 1);
            
        }
        $wxOrderData = new \WxPayUnifiedOrder();
        $wxOrderData->SetOut_trade_no($this->orderNo);
        $wxOrderData->SetTrade_type('JSAPI');
        $wxOrderData->SetTotal_fee(bcmul($totalPrice, 100));
        $wxOrderData->SetBody('零食商贩');
        $wxOrderData->SetOpenid($openId);
        $wxOrderData->SetNotify_url(config('secure.pay_back_url'));
        return $this->getPaySignature($wxOrderData);
    }

    private function recordPreOrder($wxOrder)
    {
        OrderModel::where('id', $this->orderId)
            ->update(['prepay_id' => $wxOrder['prepay_id']]);
    }

    private function getPaySignature(\WxPayUnifiedOrder $wxOrderData)
    {
        $wxOrder = \WxPayApi::unifiedOrder($wxOrderData);
        if ($wxOrder['return_code'] != 'SUCCESS' || $wxOrder['result_code'] != 'SUCCESS') {
            Log::record($wxOrder, 'error');
            Log::record('获取预支付订单失败！', 'error');
            throw new OrderException(['message' => "获取预支付订单失败！"]);
            
        }
        $wxOrder['prepay_id'] = 'wx20170926180718fbe9d3267c0152125150';
        $this->recordPreOrder($wxOrder);
        $jsParams = $this->sign($wxOrder);
        return $jsParams;
    }

    private function sign($wxOrder)
    {
        $jsApiPayData = new \WxPayJsApiPay();
        $jsApiPayData->SetAppid(config('wx.app_id'));
        $jsApiPayData->SetTimeStamp((string)time());
        $rand = md5(time().mt_rand(0, 1000));
        $jsApiPayData->SetNonceStr($rand);
        $jsApiPayData->setPackage('prepay_id='.$wxOrder['prepay_id']);
        $jsApiPayData->SetSignType('md5');
        $sign = $jsApiPayData->MakeSign();
        $jsParams = $jsApiPayData->GetValues();
        $jsParams['paySign'] = $sign;
        unset($jsParams['appId']);
        return $jsParams;
    }
}
