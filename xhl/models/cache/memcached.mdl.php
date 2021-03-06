<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * Author xinghuali<xinghuali@126.com>
 * $Id: memcached.mdl.php 2016-09-27 02:07:36  xinghuali
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

//源生memcached
Import::I('cache');
class Mdl_Cache_Mcache extends Memcached implements Cache_Interface
{
    
    public function __construct()
    {
        parent::__construct();
		$cfg = explode(':', __CFG::MEMCACHE);
		if(!$this->addServer($cfg[0], $cfg[1])){
            trigger_error('Memcache Connect Fail', E_USER_ERROR);
        }else{
            $this->prefix();
        }
    }

    //Memcached::set($key, $val, $ttl)
    //Memcached::get($key)
    //Memcached::delete($key)
    //Memcached::flush($time=0)

    public function prefix($key=null)
    {
        if($key === null){
            $preifx = 'K:cache.';
        }else{
            $prefix = "K:{$key}.";
        }
        $this->setOption(Memcached::OPT_PREFIX_KEY,$prefix);
        return $prefix;
    }

    public function set_prefix($key)
    {
        return $this->setOption(Memcached::OPT_PREFIX_KEY, "K:{$key}.");
    }

    public function reset_prefix()
    {
        return $this->setOption(Memcached::OPT_PREFIX_KEY,'K:cache.');
    }
}