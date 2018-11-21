<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * $Id: user.ctl.php 10098 2015-09-29 14:44:00  xinghuali
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_User extends Ctl
{
    public function index()
    {
        $this->seo->init('index');
        $this->tmpl = 'login.html';
    }

    public function zhuce()
    {
        $this->tmpl = 'zhuce.html';
    }

    public function xiugai()
    {
        $this->tmpl = 'xiugai.html';
    }

    /*public function gai()
    {
        $data = $this->GP("gdh");
        var_dump($data);
        die;
    }*/

    //修改密码
    public function uppwd()
    {
        if(!$gdh = $this->GP('gdh')){
            $this->err->add('非法的数据提交', 212);
        }else if(empty($gdh['regCode'])){
            $this->err->add('短信验证码不能为空', 213);
        }else if (!preg_match('/^[\x21-\x7E]{6,15}$/', $gdh['passwd'])) {
            $this->err->add('用户密码只包含(数字,大小写字母,特殊符号,不含空格)长度6~15字符', 401);
        }else{
            $access = $this->system->config->get('access');
            $session =K::M('system/session')->start();
            $scode =  $session->get('MOBILE_VERIFY_CODE');
            if($gdh['regCode'] = $scode){                   // 在这验证手机号是否存在
                $member = K::M('member/member')->member($gdh['mobile'], 'mobile');
                if ($member) {
                    $succ = K::M('member/account')->update_passwd($member['uid'], $gdh['passwd']);
                    if ($succ) {
                        $this->err->set_data('forward', $this->mklink('user'));
                        $this->err->add("重新设置密码成功");
                    } else {
                        $this->err->add('修改失败!请联系客服人员 服务热线：4001781616',212);
                    }
                } else {
                    $this->err->add('该手机不存在',411);
                }
            } else {
                $this->err->add('验证码不正确', 212);
            }
        }
    }

    //退出登录
    public function loginout()
    {
        $this->cookie->delete('uid');
        $this->err->add('退出登录成功',200);
    }

    public function login()     // 登录的时候访问
    {
        if(!$gdh = $this->GP('user')){
            $this->err->add('非法的数据提交', 212);
        }else if(!$uname = $gdh['tel']){
             $this->err->add('用户名不正确', 213);
        }else if(!$passwd = $gdh['pwd']){
             $this->err->add('登录密码不下确', 214);
        }else{
            $keep = $this->GP('keep') ? true : false;
            // $a = K::M('verify/check')->mail($uname) ? 'mail' : 'uname';
            $a = 'mobile';
            if($member = $this->auth->login($uname, $passwd, $a, false,$keep)){
                $this->err->add("{$member[mobile]}，欢迎您回来!");
                $forward = K::M('helper/link')->mklink('index:index', array(), array(), 'base');
                // var_dump($forward);
                // die;
                $this->err->set_data('forward', $forward);
            }
        }
    }

    public function check()
    {
        if($clientid = $this->GP('clientid')){
            $gdh = $this->GP('gdh');
            $obj = K::M('member/account');
            $oString = K::M('content/string');
            if($clientid == 'uname' && isset($gdh['uname'])){
                if($obj->check_uname($oString->unescape($gdh['uname']))){
                    $this->err->add("用户可以使用");
                }
            }else if($clientid == 'mobile' && isset($gdh['mobile'])){
                if($obj->check_mobile($oString->unescape($gdh['mobile']))){
                    $this->err->add('手机号可以使用');
                }
            }else if($clientid == 'mail' && isset($gdh['mail'])){
                if($obj->check_mail($oString->unescape($gdh['mail']))){
                    $this->err->add('邮箱可以使用');
                }
            }else{
                $this->err->add('非法的数据提交', 211);
            }
        }else{
            $this->err->add('非法的数据提交', 211);
        }
    }

    public function check2()
    {
        if($clientid = $this->GP('clientid')){
            $gdh = $this->GP('gdh');
            $obj = K::M('member/account');
            $oString = K::M('content/string');
            if($clientid == 'uname' && isset($gdh['uname'])){
                if($obj->check_uname($oString->unescape($gdh['uname']))){
                    $this->err->add("用户可以使用");
                }
            }else if($clientid == 'mobile' && isset($gdh['mobile'])){
                if(!$obj->check_mobile($oString->unescape($gdh['mobile']))){
                    $this->err->add('手机号可以使用');
                }else{
                     $this->err->add('手机号没有找到', 211);
                }
            }else if($clientid == 'mail' && isset($gdh['mail'])){
                if($obj->check_mail($oString->unescape($gdh['mail']))){
                    $this->err->add('邮箱可以使用');
                }
            }else{
                $this->err->add('非法的数据提交', 211);
            }
        }else{
            $this->err->add('非法的数据提交', 211);
        }
    }

    public function register($from='member')
    {
		$lanlu = $_SERVER['HTTP_REFERER'];
		$lxh_arr = explode('/',$lanlu);
		$ip = gethostbyname($lxh_arr[2]);
		if(1 || $ip=='101.201.150.143'){
			$from_list = K::M('member/member')->from_list();
			if(!$from_title = $from_list[$from]){
				$from_title = $from_list['member'];
				$from = 'member';
			}

			if($GUID = K::M('system/cookie')->GUID){
				$session =K::M('system/session')->start();
				$code = strtoupper(K::M('content/string')->random(32));
				$session->set('MOBILE_VERIFY_TIME',__TIME, 900); //5分钟有效
				$session->set('MOBILE_VIMGCODE', $code,900); //15分钟缓存
				$this->pagedata['code'] = $code;
			}
			$pager = array('from'=>$from, 'from_title'=>$from_title);
			$this->pagedata['pager'] = $pager;
			$this->tmpl = 'user/register.html';
		}else{
			$rid = rand(1,1685);
			header("Location:http://www.jisunet.com/chao/tezhuang-{$rid}.html");
		}
    }

    public function shang($cname='',$uname='',$mobile='')
    {
		$cname = $uname = $mobile = '';
		$uri = $this->request['uri'];
		$uri = str_replace(".html", "", $uri);
		$args = explode('-', trim($uri, '-'));
		$cz_id = $args[2];
		$cname = urldecode($args[3]);
		$uname = urldecode($args[4]);
		$mobile = $args[5];
		$from_title = '参展商';
		$from = 'shang';
        if($GUID = K::M('system/cookie')->GUID){
            $session =K::M('system/session')->start();
            $code = K::M('content/string')->random(32);
	  	    $session->set('MOBILE_VERIFY_TIME',__TIME, 900); //5分钟有效
            $session->set('MOBILE_VIMGCODE', strtoupper($code),900); //15分钟缓存
	        $this->pagedata['code'] = $code;
        }

        $pager = array('from'=>$from, 'from_title'=>$from_title, 'cz_id'=>$cz_id,'cname'=>$cname, 'uname'=>$uname, 'mobile'=>$mobile);
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'user/shangreg.html';
    }

    public function mobile()
    {
	//	K::M('email/czs')->send_czs();
		file_put_contents('./xhl/data/gongji_ip.txt', __IP.PHP_EOL, FILE_APPEND);
//		header('Location:http://www.jisunet.com/index.html');
		exit;
    }
    public function send()
    {
		if($gdh = $this->GP('gdh')){
			if($GUID = K::M('system/cookie')->GUID){
				$lanlu = $_SERVER['HTTP_REFERER'];
//				if($lanlu == 'http://www.jisunet.com/user-register-shang.html' || $lanlu == 'http://www.jisunet.com/user-register-designer.html' || $lanlu == 'http://www.jisunet.com/user-register-company.html' || $lanlu == 'http://www.jisunet.com/user-register-member.html'){
					$lxh_arr = explode('/',$lanlu);
					$session =K::M('system/session')->start();
					$stime = (int)$session->get('MOBILE_VERIFY_TIME');
					$code = $session->get('MOBILE_VIMGCODE');
					$stime1 = (int)$session->get('MOBILE_VERIFY_TIME1');

					$ip = gethostbyname($lxh_arr[2]);
 //				if($stime && $stime < __TIME-5 && $stime > __TIME-900 && $ip=='101.201.150.143' && $code==$ylz['code']){
 					$code = K::M('content/string')->Random(6, 1);
			        $code = 123456;
					$session->set('MOBILE_VERIFY_CODE',$code, 1800); //30分钟有效
					$session->set('MOBILE_VERIFY_TIME1',__TIME, 180); //5分钟有效
//					K::M('sms/sms')->send($ylz['mobile'],'reg_mobile',array('uname'=>$ylz['uname'], 'verify_code'=>$code));
					file_put_contents('./xhl/data/send_ip.txt', __IP.'|'.$this->request['uri'].PHP_EOL, FILE_APPEND);
//				}
			}else{

				file_put_contents('./xhl/data/hei_ip.txt', __IP.'|'.$lanlu.'|'.$this->request['uri'].PHP_EOL, FILE_APPEND);
//			}
			}
		}else{

		}
    }


    public function reg()
    {
        $this->seo->init('index');
        $this->tmpl = 'user/reg.html';
    }

    /*public function loginout()
    {
        @header("Expires: -1");
        @header("Cache-Control: no-store, private, post-check=0, pre-check=0, max-age=0", FALSE);
        @header("Pragma: no-cache");
        if($this->auth->loginout()){
			$url = $this->request['forward'];
			if(empty($url) || strpos($url,'loginout') !== false){
				$cfg = $this->system->config->get('site');
				$url = $cfg['siteurl'];
			}
			//$this->err->redirect($url, 200);
			$this->err->add('您已成功退出');
			$this->err->set_data('forward', $this->mklink('index', array()));
		}else{
			$this->err->add('退出失败');
			$this->err->set_data('forward', $url);
		}
    }*/





    public function create()    
    {
        if(!$gdh = $this->GP('gdh')){
            $this->err->add('非法的数据提交', 212);
        }else if(empty($gdh['regCode'])){
            $this->err->add('短信验证码不能为空', 213);
        }else{
            $verifycode_success = true;
			$session =K::M('system/session')->start();
            $scode =  $session->get('MOBILE_VERIFY_CODE');
	        if($gdh['regCode'] != $scode){
				$verifycode_success = false;
				$this->err->add('验证码不正确', 212);
            }
            if($verifycode_success){
                if($uid = K::M('member/account')->create($gdh)){
                    $this->err->add('恭喜您，注册会员成功');          // 在这之前提示登录名或密码不正确
//                    $from_list = K::M('member/member')->from_list();
                    $forward = K::M('helper/link')->mklink('index:index', array(), array(), 'base');
                    $this->err->set_data('forward', $forward);
                }
           }
        }
    }

    public function postforget($from='member')
    {
        if(!$gdh = $this->GP('gdh')){
            $this->err->add('非法的数据提交', 212);
        }else if(empty($gdh['regCode'])){
            $this->err->add('短信验证码不能为空', 213);
        }else{
            $access = $this->system->config->get('access');
            $session =K::M('system/session')->start();
            $scode =  $session->get('MOBILE_VERIFY_CODE');
            if($gdh['regCode'] != $scode){
                $this->err->add('验证码不正确', 212);
            }else{
                $session->set('MOBILE_NUM_FORGET',$gdh['mobile'], 1800); //30分钟有效
                $this->err->set_data('forward', $this->mklink('user:forgot', array()));
                $this->err->add("输入正确！");
            }
        }
    }

    public function forgetpw($from='member')
    {
        $from_list = K::M('member/member')->from_list();
        if(!$from_title = $from_list[$from]){
            $from_title = $from_list['member'];
            $from = 'member';
        }
         if($GUID = K::M('system/cookie')->GUID){
            $session =K::M('system/session')->start();
            $code = K::M('content/string')->random(32);
            $session->set('MOBILE_VERIFY_TIME',__TIME, 600); //5分钟有效
            $session->set('MOBILE_VIMGCODE', strtoupper($code),900); //15分钟缓存
            $this->pagedata['code'] = $code;
        }
       $pager = array('from'=>$from, 'from_title'=>$from_title);
        $this->pagedata['pager'] = $pager;
        $this->seo->init('index');
        $this->tmpl = 'user/forgetpw.html';
    }

    /**
     * 第三方登录
     */
    public function sso($from='qq')
    {
        echo "success";
        exit;
    }

    //QQ 联合登录
    public function qqlogin($type=null)
    {
        if($url = K::M('member/qqlogin')->qqloign_url($type)){
            header("Location: {$url}");
            die;
        }

    }

    public function qqcallback()
    {
        if(!$code = $this->GP('code')){
            die('回传地址有问题2');
        }elseif(!$state = $this->GP('state')){
            die('回传地址有问题1');
        }elseif(true == K::M('member/qqlogin')->qqcallback($code,$state)){
            $forward = K::M('helper/link')->mklink('member/member:index', array(), array(), 'base');
            header("Location: {$forward}");
            die;
        }
    }


    public function weibo($type=null)
    {
        if($url = K::M('member/weibo')->weibo_url($type)){
            header("Location: {$url}");
            die;
        }
    }

    public function weibocallback()
    {
        if(!$code = $this->GP('code')){
            die('回传地址有问题2');
        }if(true == K::M('member/weibo')->weibocallback($code)){
            $forward = K::M('helper/link')->mklink('member/member:index', array(), array(), 'base');
            header("Location: {$forward}");
           die;
        }
    }

    public function verfiy($type = 'mail',$uid = 0 ,$token = null)
    {

        if(!($uid=(int)$uid) || empty($token)){
            $this->err->add('参数有误', 211);
        }else{
            if(!$member = K::M('member/view')->member($uid)){
                $this->err->add('用户ID不存在！', 201);
            }else if (K::M('system/integral')->check('email',$member) === false) {
                $this->err->add('很抱歉您的账户余额不足！', 201);
            }else{
                if(K::M('member/magic')->verify_mail($uid,$token)){
                    K::M('system/integral')->commit('email', $member,'用户邮箱认证');
                    $this->err->add('邮箱验证成功！');
                    $this->err->set_data('forward',  K::M('helper/link')->mklink('member/member:index', array(), array(), 'base'));
                }else{
                    $this->err->add('邮箱验证失败！', 201);
                    $this->err->set_data('forward',  K::M('helper/link')->mklink('member/member:index', array(), array(), 'base'));
                }
            }
        }
    }
    public function getmsg(){
        // $mobile = $this->GP('gdh');
        // $this->pushdata($mobile);
        // return $mobile;
        $mobile = $this->getlink(17);
        $mobile = $mobile['mobile'];
        // $this->pushdata($mobile);
        // var_dump($mobile);
        // die;
        if(!K::M('verify/check')->mobile($mobile)){
            $this->pushdata(array('error'=>1,'message'=>'手机格式错误'));
        }else{
            $session =K::M('system/session')->start();
            $stime1 = (int)$session->get('MOBILE_VERIFY_TIME1');
            if(time()-$stime1<60){
                $ajax = array(
                    'error'=>2,
                    'message'=>'请稍候再获取验证码'
                );
                // $this->ajaxReturn($ajax);die;
            }
            $session->set('MOBILE_VERIFY_TIME1',__TIME, 180); //5分钟有效
            $this->send_msg_order(1,$mobile);
            $this->pushdata(array('error'=>0,'message'=>'发送成功'));
        }
    }

    public function getlink($num)
    {
        $aa = $this->request;
        $a = substr($aa['uri'], $num);
        $b = explode('&', $a);
        foreach ($b as $k=>$v) {
            $bb[] = explode('=', $v);
        }
        foreach ($bb as $k => $v) {
            $arr[$v[0]] = $v[1];
        }

        return $arr;
    }


}


