<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * Author @xinghuali<xinghuali@126.com>
 * $Id: account.mdl.php 2016-09-27 02:07:36  xinghuali
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Member_Account extends Model
{   

    public function create($data)
    {
		if(!defined('IN_ADMIN')){
            if(!$this->check_signup_count()){
                return false;
            }
        }
		if(!$data['mail']){
			$data['mail'] = $data['mobile'].'@jisunet.com';
		}
		if(isset($data['cname'])){
			$cname = $data['cname'];
			unset($data['cname']);
		}
		if(isset($data['zhixing_id'])){
			$zhixing_id = $data['zhixing_id'];
			unset($data['zhixing_id']);
		}else{
			$zhixing_id = 0;
		}
		if(isset($data['zhixing_name'])){
			$zhixing_name = $data['zhixing_name'];
			unset($data['zhixing_name']);
		}else{
			$zhixing_name = '前台注册';
		}
		if(isset($data['cz_id'])){
			$cz_id = $data['cz_id'];
			unset($data['cz_id']);
		}
        //- -这样弄吧
        // if(!$uname = $this->check_uname($data['uname'])){
        //     return false;
        // }else 
        if(!$mobile = $this->check_mobile($data['mobile'])){
            return false;
        }else if(!$passwd = $this->check_passwd($data['passwd'])){
            return false;
        }
//        $gender = strtolower($data['gender']) == 'man' ? 'man' : 'woman';
        $a = array('uname'=>$uname, 'mobile'=>$mobile, 'mail'=>$mail, 'passwd'=>md5($passwd));    
        $a['from'] = in_array($data['from'], array('member', 'designer', 'company', 'shop','shang','mechanic','unknown')) ? $data['from'] : 'designer';
        $a['from_id'] = $data['from_id'] ? $data['from_id'] : 0;
/*        if($data['city_id']){
            $a['city_id'] = intval($data['city_id']);
        }else{
            $site = K::$system->config->get('site');
            $a['city_id'] = (int)$site['city_id'];
        }*/
        if(defined('UC_OPEN') && UC_OPEN){
            if($uid = K::M('member/ucenter')->create($uname, $passwd, $mail)){
                $maxuid = K::M('member/member')->max_uid();
                $a['uc_uid'] = $uid;
                if($uid > $maxuid){
                    $a['uid'] = $uid;
                }
            }
        }
        $a['dateline'] = __CFG::TIME;
        $a['regip'] = __IP;
		$group = K::M('member/group')->default_group($a['from']);
		$a['group_id'] = $group['group_id'];

        if(!$uid = K::M('member/member')->create($a, true)){
    		return false;
    	}
		$b = array('uid'=>$uid,'group_id'=>$a['group_id'],'name'=>$uname,'mobile'=>$mobile);
		$s = array('uid'=>$uid,'group_id'=>$a['group_id'],'title'=>$cname,'contact'=>$uname,'mobile'=>$mobile,'zhixing_id'=>$zhixing_id,'zhixing_name'=>$zhixing_name);
		// $c = array('uid'=>$uid,'group_id'=>8,'title'=>$cname,'contact'=>$uname,'mobile'=>$mobile);
        $c = array('uid'=>$uid,'group_id'=>$a['group_id'],'name'=>$uname,'mobile'=>$mobile);
        // 在这判断 $a['from']，从而插入不同的表

        if($a['from'] == 'designer'){//初始设计师表
            K::M('designer/designer')->create($b, null, true);
        }else if($a['from'] == 'shang'){//参展商表
            if($shang_id = K::M('shang/shang')->create($s, true)){
				if($cz_id){
					K::M('canzhan/canzhan')->update($cz_id, array('uid'=>$uid,'shang_id'=>$shang_id,'guid'=>''));
				}else{
					//没有传ID的情况
				}
			}
        }else if($a['from'] == 'company'){      //参展商表
            K::M('company/company')->create($c, true);
        }
        if(!defined('IN_ADMIN') && !$a['from_id']){
           K::$system->auth->login($uname, $passwd, 'uname');
        }
        
        return $uid;
    }

    public function check_uname($uname)
    {
		$uname = K::M('content/html')->encode($uname);
		if(!preg_match('/^[\x{4e00}-\x{9fa5}\w\-]{2,16}$/u', $uname) || strlen($uname)>24 || strlen($uname)<3){
			$this->err->add('用户名只包含(数字,大小写字母,下划线,中文)长度2~16字符',401);
		}else if(!defined('IN_ADMIN') && $this->_check_retain_uname($uname)){
			$this->err->add('系统保留用户名，请重新填写',401);
		}else if(K::M('member/member')->member($uname,'uname')){
            $this->err->add('此用户名太受欢迎，已有人抢注啦',401);
		}else if(defined('UC_OPEN') && UC_OPEN){
            return K::M('member/ucenter')->check_uname($uname);
        }else{
			return $uname;
		}
		return false;
    }

    public function _check_retain_uname($uname)
    {
    	$access = K::$system->config->get('access');
        if($retain_uname = preg_replace("/[\r\n]+/", "|", $access['retain_uname'])){
            if($retain_uname = trim($retain_uname, '|')){
                $retain_uname = str_replace('*', '.*', $retain_uname);
                if(preg_match("/{$retain_uname}/ui", $uname)){
                    return true;
                }
            }
        }    
        return false;
    }

	public function check_signup_count()
    {
        $access = K::$system->config->get('access');
        if($signup_count = (int)$access['signup_count']){
            if($signup_count < K::M('member/member')->count(array('regip'=>__IP, 'dateline'=>'>:'.(__TIME-86400)))){
                $this->err->add('同一IP24小时只能注册'.$signup_count.'用户', 501);
                return false;
            }
        }
        if($signup_time = (int)$access['signup_time']){
            $time = __TIME - $signup_time*60;
            if(K::M('member/member')->count(array('regip'=>__IP, 'dateline'=>'>:'.$time))){
                $this->err->add('同一IP两次注册间隔'.$signup_time.'分钟', 502);
                return false;
            }
        }
        return true;
    }

    public function check_shop_title($title)
    {
		$title = K::M('content/html')->encode($title);
        if(!preg_match('/^[\x{4e00}-\x{9fa5}\w\-]{2,80}$/u', $title) || strlen($title)>80 || strlen($title)<2){
            $this->err->add('用户名只包含(数字,大小写字母,下划线,中文)长度2~80字符'.$title,411);
            return false;
        }
        return $title;
    }

    public function check_mobile($mobile)
    {
    	if(!K::M('verify/check')->mobile($mobile)){
    		$this->err->add('手机格式不正确', 611);
            return false;
    	}else if($member = K::M('member/member')->member($mobile, 'mobile')){
    		$this->err->add('此手机已被注册过', 612);
            return false;
    	}
    	return $mobile;
    }
    public function check_mobile_cz($mobile)
    {
    	if(!K::M('verify/check')->mobile($mobile)){
            return false;
    	}else if($member = K::M('member/member')->member($mobile, 'mobile')){
            return $member;
    	}
    	return false;
    }
    public function check_mail($mail)
    {
    	if(!K::M('verify/check')->mail($mail)){
    		$this->err->add('邮箱格式不正确', 511);
            return false;
    	}else if($member = K::M('member/member')->member($mail, 'mail')){
    		$this->err->add('此邮箱已被占用', 512);
            return false;
    	}else if(defined('UC_OPEN') && UC_OPEN){
            return K::M('member/ucenter')->check_mail($mail);
        }
    	return $mail;
    }

    public function check_passwd($passwd)
    {
       if(!preg_match('/^[\x21-\x7E]{6,15}$/', $passwd)){
            $this->err->add('用户密码只包含(数字,大小写字母,特殊符号,不含空格)长度6~15字符', 401);
            return false;
        }
        return $passwd;
    }

    //passwd 为明文的密码,非MD5后的。
    public function update_passwd($uid, $passwd)
    {
        if(!$uid = (int)$uid){
            return false;
        }else if(!$passwd = $this->check_passwd($passwd)){
            return false;
        }else if(!$member = K::M('member/member')->member($uid)){
            return false;
        }else if(defined('UC_OPEN') && UC_OPEN){
            if(!K::M('member/ucenter')->update($member['uname'], '', $passwd, '', 1)){
                return false;
            }
        }
        return K::M('member/member')->update($uid, array('passwd'=>md5($passwd)), true);
    }

    public function update_mail($uid, $mail)
    {
        if(!$uid = (int)$uid){
            return false;
        }else if(!$mail = $this->check_mail($mail)){
            return false;
        }else if(!$member = K::M('member/member')->member($uid)){
            return false;
        }else if(defined('UC_OPEN') && UC_OPEN){
            if(!K::M('member/ucenter')->update($member['uname'], '', '', $mail, 1)){
                return false;
            }
        }
        return K::M('member/member')->update($uid, array('mail'=>$mail), true);        
    }


	public function create_xcx($data)
	{

		if(!defined('IN_ADMIN')){
			if(!$this->check_signup_count()){
				return false;
			}
		}

		if(!$data['mail']){
			$data['mail'] = $data['mobile'].'@16-expo.com';
		}
		if(isset($data['cname'])){
			$cname = $data['cname'];
			unset($data['cname']);
		}
		if(isset($data['zhixing_id'])){
			$zhixing_id = $data['zhixing_id'];
			unset($data['zhixing_id']);
		}else{
			$zhixing_id = 0;
		}
		if(isset($data['zhixing_name'])){
			$zhixing_name = $data['zhixing_name'];
			unset($data['zhixing_name']);
		}else{
			$zhixing_name = '前台注册';
		}
		if(isset($data['cz_id'])){
			$cz_id = $data['cz_id'];
			unset($data['cz_id']);
		}

		if(!$uname = $this->check_uname_xcx($data['uname'])){

			return false;
		} else if(!$passwd = $this->check_passwd($data['passwd'])){

			return false;
		}


		//存储openid
		$openid = $data['openid'];
		$unionid = $data['unionid'];
//        $gender = strtolower($data['gender']) == 'man' ? 'man' : 'woman';
		$a = array('uname'=>$uname, 'mobile'=>$mobile, 'mail'=>$mail, 'passwd'=>md5($passwd),'openid'=>$openid,'unionid'=>$unionid);
		$a['from'] = in_array($data['from'], array('member', 'designer', 'company', 'shop','shang','zhu','unknown')) ? $data['from'] : 'member';

		$a['from_id'] = $data['from_id'] ? $data['from_id'] : 0;
		/*        if($data['city_id']){
					$a['city_id'] = intval($data['city_id']);
				}else{
					$site = K::$system->config->get('site');
					$a['city_id'] = (int)$site['city_id'];
				}*/
		if(defined('UC_OPEN') && UC_OPEN){
			if($uid = K::M('member/ucenter')->create($uname, $passwd, $mail)){
				$maxuid = K::M('member/member')->max_uid();
				$a['uc_uid'] = $uid;
				if($uid > $maxuid){
					$a['uid'] = $uid;
				}
			}
		}
		$a['dateline'] = __CFG::TIME;
		$a['regip'] = __IP;
		$group = K::M('member/group')->default_group($a['from']);
		$a['group_id'] = $group['group_id'];

		if(!$uid = K::M('member/member')->create($a, true)){
			return false;
		}
		$b = array('uid'=>$uid,'group_id'=>$a['group_id'],'name'=>$uname,'mobile'=>$mobile);
		$s = array('uid'=>$uid,'group_id'=>$a['group_id'],'title'=>$cname,'contact'=>$uname,'mobile'=>$mobile,'zhixing_id'=>$zhixing_id,'zhixing_name'=>$zhixing_name);
		$c = array('uid'=>$uid,'group_id'=>8,'title'=>$cname,'contact'=>$uname,'mobile'=>$mobile);
		if($a['from'] == 'designer'){//初始设计师表
			K::M('designer/designer')->create($b, null, true);
		}else if($a['from'] == 'shang'){//参展商表
			if($shang_id = K::M('shang/shang')->create($s, true)){
				if($cz_id){
					K::M('canzhan/canzhan')->update($cz_id, array('uid'=>$uid,'shang_id'=>$shang_id,'guid'=>''));
				}else{
					//没有传ID的情况
				}
			}
		}else if($a['from'] == 'company'){//参展商表
			K::M('company/company')->create($c, true);
		}
		if(!defined('IN_ADMIN') && !$a['from_id']){
			K::$system->auth->login($uname, $passwd, 'uname');
		}
		return $uid;
	}

	public function check_uname_xcx($uname)
	{
		$uname = K::M('content/html')->encode($uname);
		if(!preg_match('/^[\x{4e00}-\x{9fa5}\w\-]{2,56}$/u', $uname) || strlen($uname)>54 || strlen($uname)<3){

			$this->err->add('用户名只包含(数字,大小写字母,下划线,中文)长度2~16字符',401);
		}else if(!defined('IN_ADMIN') && $this->_check_retain_uname($uname)){
			$this->err->add('系统保留用户名，请重新填写',401);
		}else if(K::M('member/member')->member($uname,'uname')){
			$this->err->add('此用户名太受欢迎，已有人抢注啦',401);
		}else if(defined('UC_OPEN') && UC_OPEN){

			return K::M('member/ucenter')->check_uname($uname);
		}else{
			return $uname;
		}
		return false;
	}
}
