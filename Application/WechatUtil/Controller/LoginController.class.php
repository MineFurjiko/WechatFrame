<?php
namespace WechatUtil\Controller;

use Think\Controller;
use WechatUtil\Util\OAuth2\WechatOAuth2;

class LoginController extends Controller
{
    private $app_id;
    private $app_secret;
    private $is_init=false;

    public function init($appid, $appsecret)
    {
        $this->app_id=$appid;
        $this->app_secret=$appsecret;
        $this->is_init=true;
    }

    public function wechat_login($re_url, $is_userinfo=false, $cookie_prefix='')
    {
        $oauth2Packet=new WechatOAuth2();

        if ($this->is_init) {
            $oauth2Packet->init($this->app_id, $this->app_secret);
        } else {
            echo "系统错误!";
            exit();
        }

        $wxInfo='&ali='.$this->app_id.'&als='.$this->app_secret;

        $re_url=urlEncode($re_url);
        if ($is_userinfo==false) {
            $code_re_url='https://'.$_SERVER["SERVER_NAME"].__ROOT__.'/WechatUtil/Login/base_redirect?re_url='. $re_url.'&cp='.$cookie_prefix.$wxInfo;
            $oauth2Packet->get_base_code($code_re_url);
        } else {
            $code_re_url='https://'.$_SERVER["SERVER_NAME"].__ROOT__.'/WechatUtil/Login/userinfo_redirect?re_url='. $re_url.'&cp='.$cookie_prefix.$wxInfo;
            $oauth2Packet->get_userinfo_code($code_re_url);
        }
    }

    public function base_redirect()
    {
        @$code= I('get.code');
        @$re_url=I('get.re_url');
        @$cookie_prefix=I('get.cp');
        @$appid=I('get.ali');
        @$appsecret=I('get.als');

        // echo $code;
        // echo "<br>";
        // echo $re_url;
        // echo "<br>";
        // echo $cookie_prefix;

        if (empty($code)) {
            echo "Can't not get code!";
            exit();
        } else {
            $oauth2Packet=new WechatOAuth2();
            $oauth2Packet->init($appid, $appsecret);

            $oauth2Packet->get_access_token_and_openid_json($code);
            $oauth2Packet->set_base_oauth2_cookie($cookie_prefix);

            // echo "<br>";
            // var_dump(I('cookie.'.$cookie_prefix.'_openid'));

            redirect($re_url);
        }
    }

    public function userinfo_redirect()
    {
        @$code= I('get.code');
        @$re_url=I('get.re_url');
        @$cookie_prefix=I('get.cp');
        @$appid=I('get.ali');
        @$appsecret=I('get.als');

        // echo $code;
        // echo "<br>";
        // echo $re_url;
        // echo "<br>";
        // echo $cookie_prefix;

        if (empty($code)) {
            echo "Can't not get code!";
            exit();
        } else {
            $oauth2Packet=new WechatOAuth2();
            $oauth2Packet->init($appid, $appsecret);

            $oauth2Packet->get_user_info_throwaway($code);
            $oauth2Packet->set_oauth2_cookie($cookie_prefix);

            // echo "<br>";
            // var_dump(I('cookie.'.$cookie_prefix.'_openid'));
            // echo "<br>";
            // var_dump(I('cookie.'.$cookie_prefix.'_nickname'));

            redirect($re_url);
        }
    }

    public function check_oauth2_cookie($prefix='', $adv=false)
    {
        $oauth2Packet=new WechatOAuth2();
        return $oauth2Packet->check_oauth2_cookie($prefix, $adv);
    }

    public function clean_oauth2_cookie($prefix='')
    {
        $oauth2Packet=new WechatOAuth2();
        return $oauth2Packet->clean_oauth2_cookie($prefix);
    }

    public function get_oauth2_cookie_json($prefix='')
    {
        $oauth2Packet=new WechatOAuth2();
        return $oauth2Packet->get_oauth2_cookie_json($prefix);
    }
}
