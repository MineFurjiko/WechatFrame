<?php
namespace Home\Controller;

use Think\Controller;
use Think\Log;

class PayCallbackController extends Controller
{
    public function wechat()
    {
        $result=$this->notify();
        if ($result) {
            // 验证成功 修改数据库的订单状态等
        }
    }

    public function sino()
    {
        $result=$this->sino_notify();
        if ($result) {
            // 验证成功 修改数据库的订单状态等
        }
    }

    private function notify()
    {
        // 获取xml
        $xml=file_get_contents('php://input', 'r');

        // 转成php数组
        if (!$xml) {
            throw new \Think\Exception("xml数据异常！");
        }

        Log::write($xml);

        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        // 保存原sign
        $data_sign=$data['sign'];

        // sign不参与签名
        unset($data['sign']);

        //签名
        $wechatConfig=C('WEIXINPAY_CONFIG');
        $pay_key=$wechatConfig['KEY'];

        if ((empty($pay_key)|is_null($pay_key))) {
            throw new \Think\Exception("未设置微信支付密钥！");
        }
        $temp_data=$data;
        //签名步骤一：按字典序排序参数
        ksort($temp_data);
        $string =urldecode(http_build_query($temp_data));
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$pay_key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $sign = strtoupper($string);

        Log::write($sign);
        
        $result=false;
        // 判断签名是否正确  判断支付状态
        if ($sign===$data_sign && $data['return_code']=='SUCCESS' && $data['result_code']=='SUCCESS') {
            $result=$data;
            Log::write('Right Sign!');
        }
        // 返回状态给微信服务器
        if ($result) {
            $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        } else {
            $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
        return $result;
    }

    private function sino_notify()
    {
        // 获取xml
        $xml=file_get_contents('php://input', 'r');

        // 转成php数组
        if (!$xml) {
            throw new \Think\Exception("xml数据异常！");
        }

        Log::write($xml);

        //将XML转为array
        //禁止引用外部xml实体
        libxml_disable_entity_loader(true);
        $data = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);

        // 保存原sign
        $data_sign=$data['sign'];
        // sign不参与签名
        unset($data['sign']);

        //签名
        $wechatConfig=C('WEIXINPAY_CONFIG');
        $pay_key=$wechatConfig['KEY'];

        if ((empty($pay_key)|is_null($pay_key))) {
            throw new \Think\Exception("未设置微信支付密钥！");
        }
        $temp_data=$data;
        //签名步骤一：按字典序排序参数
        ksort($temp_data);
        $string =urldecode(http_build_query($temp_data));
        //签名步骤二：在string后加入KEY
        $string = $string . "&key=".$pay_key;
        //签名步骤三：MD5加密
        $string = md5($string);
        //签名步骤四：所有字符转为大写
        $sign = strtoupper($string);

        Log::write($sign);
        
        $result=false;
        // 判断签名是否正确  判断支付状态
        if ($sign===$data_sign && $data['result_code']=='0' && $data['pay_result']=='0') {
            $result=$data;
            Log::write('Right Sign!');
        }
        
        // 返回状态给sino服务器
        if ($result) {
            $str='success';
        } else {
            $str='fail';
        }
        echo $str;
        return $result;
    }
}
