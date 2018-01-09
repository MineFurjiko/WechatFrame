<?php
namespace Home\Controller;

use Think\Controller;

class IndexController extends Controller
{
    public function index()
    {
        $this->show('<style type="text/css">*{ padding: 0; margin: 0; } div{ padding: 4px 48px;} body{ background: #fff; font-family: "微软雅黑"; color: #333;font-size:24px} h1{ font-size: 100px; font-weight: normal; margin-bottom: 12px; } p{ line-height: 1.8em; font-size: 36px } a,a:hover{color:blue;}</style><div style="padding: 24px 48px;"> <h1>:)</h1><p>欢迎使用 <b>ThinkPHP</b>！</p><br/>版本 V{$Think.version}</div><script type="text/javascript" src="http://ad.topthink.com/Public/static/client.js"></script><thinkad id="ad_55e75dfae343f5a1"></thinkad><script type="text/javascript" src="http://tajs.qq.com/stats?sId=9347272" charset="UTF-8"></script>', 'utf-8');
    }

    public function loginDemo()
    {
        $url='https://m.lenjoy.me/thinkwechat/PartySupport/Index/index?parm=yes';

        $cp='pay';
        $cp2='main';

        $wechatLogin= A('WechatUtil/Login');
        
        if (!$wechatLogin->check_oauth2_cookie($cp)) {
            $wechatConfig=C('WEIXINPAY_CONFIG');
            $wechatLogin->init($wechatConfig['PAY_APPID'], $wechatConfig['PAY_APPSECRET']);
            $wechatLogin->wechat_login($url, false, $cp);
        }
        
        if (!$wechatLogin->check_oauth2_cookie($cp2, true)) {
            $wechatConfig=C('WEIXINPAY_CONFIG');
            $wechatLogin->init($wechatConfig['APPID'], $wechatConfig['APPSECRET']);
            $wechatLogin->wechat_login($url, true, $cp2);
        }
        
        $rs1=json_decode($wechatLogin->get_oauth2_cookie_json($cp));
        $pay_openid=$rs1->openid;
        // echo $pay_openid;
        // echo '<br>';
        
        $rs2=json_decode($wechatLogin->get_oauth2_cookie_json($cp2));
        $main_openid=$rs2->openid;
        // echo $main_openid;
        // echo '<br>';
        // echo $rs2->full;
        // echo '<br>';
    }

    public function payDemo()
    {
        $wechatConfig=C('WEIXINPAY_CONFIG');
        $_appid=$wechatConfig['PAY_APPID'];
        $_mch_id=$wechatConfig['MCHID'];
        $_pay_key=$wechatConfig['KEY'];
    
        $wechatPay= A('WechatUtil/Pay');
        $configs=array(
            'appid'=>$_appid,
            'mch_id'=>$_mch_id,
            'pay_key'=>$_pay_key
        );
        $wechatPay->setPayConfig($configs);

        $_openid='';
        $_fee=1;

        $_notify_url="http://m.lenjoy.me/WechatFrame/wechatpaynotify.php";
        // $_notify_url="http://m.lenjoy.me/WechatFrame/sinopaynotify.php";

        $_succeed_url="http://m.lenjoy.me/thinkwechat/PartySupport/Settle/index";
        $_fail_url="http://m.lenjoy.me/thinkwechat/PartySupport/Index/index";

        $order=array(
            'openid'=>$_openid,
            'body'=>'微信支付',              //商品描述
            'attach'=>'1',                  //自定义附加参数
            'total_fee'=>$_fee,             //支付金额
            'out_trade_no'=>'',             //商户单号 空则使用默认 $_mch_id.date("YmdHis");
            'goods_tag'=>'',                //优惠标识
            'notify_url'=>$_notify_url,     //支付结果通知url
            'succeed_url'=>$_succeed_url,   //支付成功后跳转url
            'fail_url'=>$_fail_url,         //支付失败后跳转url
        );
        $wechatPay->setOrderInformation($order);

        $wechatPay->pay();  //WechatPay
        // $wechatPay->sinoPay();  //SinoPay
    }
}
