<?php
namespace WechatUtil\Controller;

use Think\Controller;

class PayController extends Controller
{
    private $_appid='';     //公众账号ID
    private $_mch_id='';    //商户号
    private $_pay_key='';   //支付密钥

    private $_openid='';
    private $_body='乐享';
    private $_attach='1';
    private $_total_fee=1;

    private $_out_trade_no='';
    private $_goods_tag='none';

    private $_trade_type='JSAPI';
    private $_sino_trade_type='pay.weixin.jspay';

    private $_notify_url='';
    private $_succeed_url='';
    private $_fail_url='';

    private $_is_init_config=false;
    public function setPayConfig($configs)
    {
        $this->_appid=$configs['appid'];
        $this->_mch_id=$configs['mch_id'];
        $this->_pay_key=$configs['pay_key'];
        $this->_is_init_config=true;
    }

    private $_is_init_order=false;
    public function setOrderInformation($order)
    {
        $this->_openid=$order['openid'];
        $this->_body=$order['body'];
        $this->_attach=$order['attach'];
        $this->_total_fee=$order['total_fee'];
        $this->_out_trade_no=$order['out_trade_no'];
        $this->_goods_tag=$order['goods_tag'];
        $this->_notify_url=$order['notify_url'];
        $this->_succeed_url=$order['succeed_url'];
        $this->_fail_url=$order['fail_url'];
        $this->_is_init_order=true;
    }

    public function pay()
    {
        if (!$this->_is_init_config) {
            throw new Think\Exception("未初始化支付商户信息！");
            exit();
        }
        if (!$this->_is_init_order) {
            throw new Think\Exception("未初始化订单信息！");
            exit();
        }

        $str="
        <form style='display:none;' id='form1' name='form1' method='post' action='https://m.lenjoy.me/WechatPay/WechatUtil/Pay/index'>
            <input type='text' name='wp_appid' value='$this->_appid' />
            <input type='text' name='wp_mch_id' value='$this->_mch_id'/>
            <input type='text' name='wp_pay_key' value='$this->_pay_key'/>
            <input type='text' name='wp_openid' value='$this->_openid'/>
            <input type='text' name='wp_body' value='$this->_body'/>
            <input type='text' name='wp_attach' value='$this->_attach'/>
            <input type='text' name='wp_total_fee' value='$this->_total_fee'/>
            <input type='text' name='wp_out_trade_no' value='$this->_out_trade_no'/>
            <input type='text' name='wp_goods_tag' value='$this->_goods_tag'/>
            <input type='text' name='wp_notify_url' value='$this->_notify_url'/>
            <input type='text' name='wp_succeed_url' value='$this->_succeed_url'/>
            <input type='text' name='wp_fail_url' value='$this->_fail_url'/>
            <input type='submit' value='Submit' />
        </form>
        <script>window.onload=function(){(document.getElementById('form1')).submit()}</script>";
        echo $str;
    }

    public function sinoPay()
    {
        if (!$this->_is_init_config) {
            throw new Think\Exception("未初始化支付商户信息！");
            exit();
        }
        if (!$this->_is_init_order) {
            throw new Think\Exception("未初始化订单信息！");
            exit();
        }

        $str="
        <form style='display:none;' id='form1' name='form1' method='post' action='https://m.lenjoy.me/WechatPay/WechatUtil/Pay/sino'>
            <input type='text' name='wp_appid' value='$this->_appid' />
            <input type='text' name='wp_mch_id' value='$this->_mch_id'/>
            <input type='text' name='wp_pay_key' value='$this->_pay_key'/>
            <input type='text' name='wp_openid' value='$this->_openid'/>
            <input type='text' name='wp_body' value='$this->_body'/>
            <input type='text' name='wp_attach' value='$this->_attach'/>
            <input type='text' name='wp_total_fee' value='$this->_total_fee'/>
            <input type='text' name='wp_out_trade_no' value='$this->_out_trade_no'/>
            <input type='text' name='wp_goods_tag' value='$this->_goods_tag'/>
            <input type='text' name='wp_notify_url' value='$this->_notify_url'/>
            <input type='text' name='wp_succeed_url' value='$this->_succeed_url'/>
            <input type='text' name='wp_fail_url' value='$this->_fail_url'/>
            <input type='submit' value='Submit' />
        </form>
        <script>window.onload=function(){(document.getElementById('form1')).submit()}</script>";
        echo $str;
    }
}
