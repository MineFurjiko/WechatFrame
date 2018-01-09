<?php

namespace WechatUtil\Util\OAuth2;

class WechatOAuth2
{
    private $app_id;
    private $app_secret;
    private $current_openid;
    private $current_user_json;
    
    public function init($appid, $appsecret)
    {
        $this->app_id=$appid;
        $this->app_secret=$appsecret;
    }

    public function get_base_code($redirect_uri = '', $state = '')
    {
        $redirect_uri = urlencode($redirect_uri);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->app_id}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_base&state={$state}#wechat_redirect";
        redirect($url);
    }

    public function get_userinfo_code($redirect_uri = '', $state = '')
    {
        $redirect_uri = urlencode($redirect_uri);
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid={$this->app_id}&redirect_uri={$redirect_uri}&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect";
        redirect($url);
    }

    public function get_access_token_and_openid_json($code = '')
    {
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid={$this->app_id}&secret={$this->app_secret}&code={$code}&grant_type=authorization_code";
        $rs=$this->getJson($url);
        $this->current_openid=$rs['openid'];
        return $rs;
    }

    public function get_user_info_json($access_token = '', $openid = '')
    {
        $url = "https://api.weixin.qq.com/sns/userinfo?access_token={$access_token}&openid={$openid}&lang=zh_CN";
        $this->current_user_json = $this->getJson($url);
        return $this->current_user_json;
    }

    public function get_user_info_throwaway($code = '')
    {
        $codeJson = $this->get_access_token_and_openid_json($code);
        return $this->get_user_info_json($codeJson['access_token'], $codeJson['openid']);
    }

    public function get_current_user_json()
    {
        return $this->current_user_json;
    }

    public function set_base_oauth2_cookie($prefix='')
    {
        if (empty($prefix)) {
            $prefix=date("Ymd");
        }
        $full_cookie_name1=$prefix.'_openid';

        cookie($full_cookie_name1, $this->current_openid, 7200);
    }

    public function set_oauth2_cookie($prefix='')
    {
        if (empty($prefix)) {
            $prefix=date("Ymd");
        }
        $full_cookie_name1=$prefix.'_openid';
        $full_cookie_name2=$prefix.'_nickname';
        $full_cookie_name3=$prefix.'_full';

        cookie($full_cookie_name1, $this->current_user_json['openid'], 7200);
        cookie($full_cookie_name2, $this->current_user_json['nickname'], 7200);
        cookie($full_cookie_name3, json_encode($this->current_user_json, JSON_UNESCAPED_UNICODE), 7200);
    }

    public function check_oauth2_cookie($prefix='', $adv=false)
    {
        if (empty($prefix)) {
            $prefix=date("Ymd");
        }

        if ($adv==true) {
            $full_cookie_name2=$prefix.'_nickname';
            $full_cookie_name3=$prefix.'_full';

            if (isset($_COOKIE[$full_cookie_name3])) {
                if (empty($_COOKIE[$full_cookie_name3])) {
                    return false;
                }
            } else {
                return false;
            }
        }

        $full_cookie_name1=$prefix.'_openid';

        if (isset($_COOKIE[$full_cookie_name1])) {
            if (!empty($_COOKIE[$full_cookie_name1])) {
                return true;
            }
        }
        return false;
    }

    public function get_oauth2_cookie_json($prefix='')
    {
        if (empty($prefix)) {
            $prefix=date("Ymd");
        }

        $full_cookie_name1=$prefix.'_openid';
        $full_cookie_name2=$prefix.'_nickname';
        $full_cookie_name3=$prefix.'_full';

        $obj['openid']=$_COOKIE[$full_cookie_name1];
        $obj['nickname']=$_COOKIE[$full_cookie_name2];
        $obj['full']=$_COOKIE[$full_cookie_name3];

        $rs=json_encode($obj, JSON_UNESCAPED_UNICODE);
        return $rs;
    }

    public function clean_oauth2_cookie($prefix='')
    {
        if (empty($prefix)) {
            $prefix=date("Ymd");
        }

        if ($this->check_oauth2_cookie($prefix)) {
            $full_cookie_name1=$prefix.'_openid';
            $full_cookie_name2=$prefix.'_nickname';
            $full_cookie_name3=$prefix.'_full';
        
            cookie($full_cookie_name1, null);
            cookie($full_cookie_name2, null);
            cookie($full_cookie_name3, null);
        }
    }

    private function getJson($url)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);

        return json_decode($output, true);
    }
}
