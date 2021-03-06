<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * $Id: ask.ctl.php 9372 2015-11-26 06:32:36  xinghuali
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Member_Member_Ask extends Ctl_Ucenter 
{
    
    public function index($page=1)
    {
        $filter = $pager = array();
        $filter['uid'] = $this->uid;
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 20;
        $pager['count'] = $count = 0;
        if($items = K::M('ask/ask')->items(array('uid'=>$this->uid), null, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
            $this->pagedata['items'] = $items;
            $this->pagedata['cate_list'] = K::M('ask/cate')->fetch_all();
        }
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'member/member/ask/asks.html';
    }

    public function answer($page=1)
    {
        $filter = $pager = array();
        $filter['uid'] = $this->uid;
        $pager['page'] = $page = max((int)$page, 1);
        $pager['limit'] = $limit = 20;
        $pager['count'] = $count = 0;
        if($items = K::M('ask/answer')->items(array('uid'=>$this->uid), null, $page, $limit, $count)){
            $pager['count'] = $count;
            $pager['pagebar'] = $this->mkpage($count, $limit, $page, $this->mklink(null, array('{page}')));
            $ask_ids = array();
            foreach($items as $k=>$v){
                $ask_ids[$v['ask_id']] = $v['ask_id'];
            }
            if($ask_ids){
                $this->pagedata['ask_list'] = K::M('ask/ask')->items_by_ids($ask_ids);
            }
            $this->pagedata['items'] = $items;
        }
        $this->pagedata['pager'] = $pager;
        $this->tmpl = 'member/member/ask/answers.html';
    }

}