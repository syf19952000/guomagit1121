<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * Author xinghuali<xinghuali@126.com>
 * $Id: index.ctl.php 2016-09-27 02:07:36  xinghuali
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Huocode_ewmadmin extends Ctl
{
    

    public function index()     // 显示后台主页
    {
        $this->tmpl = 'admin:huocode/index.html';
    }

    // html 页面中这样调控制器 <a href="?huocode/ewmadmin-index.html"
    
    

    public function aa()  // 加载type表
    {
       $a = K::M('code/type')->all();
       $this->pagedata['attr'] = $a;
       $this->tmpl = 'admin:huocode/codetype.html';
    }

    // 所有bianqian码表
    public function bb()
    {
        // 从所有类型表中取出来显示在同一页面,不能显示在同一页面，字段不一样,如果真要显示我可以觉得哪个字段显示哪个字段不显示，或者没有的字段就是null
        // 把数据合成一个数组
        // $a = K::M('code/bianqian')->all();
        // $b = K::M('code/huiyi')->all();
        // // 把content表中的src,uid 拿过来
        // $c = array_merge($a,$b);
        $d = K::M('code/content')->codeAll222();    
        $this->pagedata['data'] = $d;
        $this->tmpl = 'admin:huocode/shoutypeone.html';
    }

    // 所有码表的数据，是不可以进行删除的吧
    public function deletecode()
    {
        $url = $this->request['uri'];
        $aa = explode('?',$url);
        $tid = $aa[1];
        $type_id2 = K::M('code/content')->chaxun222(id,$tid);
        foreach($type_id2 as $v)
        {
            $type_id = $v['type_id'];
        }
        switch ($type_id) {
            case '1':
                $result1 = K::M('code/bianqian')->delete(tid,$tid);   //
                $result = K::M('code/content')->delete(id,$tid);
                break;
            case '2':
                $result1 = K::M('code/huiyi')->delete(tid,$tid);   //
                $result = K::M('code/content')->delete(id,$tid);
            default:
                # code...
                break;
        }
        $this->bb();
    }

    public function cc()     // 显示编辑码表页面,显示之前数据
    {
        $url = $this->request['uri'];
        $aa = explode('?',$url);
        $tid = $aa[1];
        $data = K::M('code/content')->chaxun222(id,$tid);
        foreach($data as $v)
        {
            $type_id = $v['type_id'];
        }
        switch ($type_id) {
            case '1':
                $data2 = K::M('code/bianqian')->chaxun(tid,$tid);
                break;
            case '2':
                $data2 = K::M('code/huiyi')->chaxun(tid,$tid);
                break;
            
            default:
                # code...
                break;
        }
        foreach($data2 as $v)
        {
            $data2 = $v;
        }
        $this->pagedata['data'] = $data2;
        $this->tmpl = 'admin:huocode/editcode.html';
    }

    public function editcode()  //进行编辑操作,可以对码表进行编辑吗
    {
        $data = $this->GP('data');
        $tid = $data['tid'];
        $data1 = K::M('code/content')->chaxun222(id,$tid);
        foreach($data1 as $v)
        {
            $type_id = $v['type_id'];
        }
        switch ($type_id) {
            case '1':
                $data2 = K::M('code/bianqian')->edit($data);  // 只能传主键吗
                break;
            case '2':
                $data2 = K::M('code/huiyi')->edit($data);
                break;  
            default:
                # code...
                break;
        }
        $this->bb();
    }
   
    public function deletetype()  // 现在是把数据删除,真正的删除，类型表type
    {
        $url = $this->request['uri'];
        $aa = explode('?',$url);
        $id = $aa[1];
        $result = K::M('code/type')->delete($id);
        $this->aa();
    }

    public function deletetype2()  // 把status改为o,前台提示删除成功
    {
        $data = $this->GP('a');
        //$data = $_POST['a'];
        var_dump($data);
        $shanchu = K::M('code/type')->deletetype2($data);
        var_dump($shanchu);
        $returnData = json_encode($shanchu, JSON_UNESCAPED_UNICODE); 
        echo $returnData;
        exit;
    }


    public function addtype()  // 添加类型
    {
        $data = $this->GP('data');
        // 加入已经存在，则不能再添加
        $tname = $data['tname'];
        if($a = K::M('code/type')->chaxun(tname,$tname))
        {
            $this->err->add('已存在', 201);  // 页面不提示
        }else{
            $result = K::M('code/type')->create($data,true);
        }
       
        $this->aa();
    }

    // 二维码表
    public function dd()    
    {
        $d = K::M('code/content')->codeAll333();    
        $this->pagedata['data'] = $d;
        $this->tmpl = 'admin:huocode/shoucontent.html';
    }

    // 搜索用户id和手机号,这是content表
    public function search()    
    {
        $data = $this->GP('key');
        $length = strlen($data);
        if($length < 10)            
        {
            $uid = $data;
            echo '111';
        }else{
            $result = K::M('member/member')->chaxun(mobile,$data);  // 获取uid
            foreach($result as $v)
            {
                $uid = $v['uid'];
            }
        }
        // 根据uid查询表
        $result1 = K::M('code/content')->chaxun(uid,$uid);     
        $this->pagedata['data'] = $result1;
        $this->tmpl = 'admin:huocode/shoucontent.html';  
    }

     public function search222()    // 所有bianqian码表
    {
        $data = $this->GP('key');
        $length = strlen($data);
        if($length < 10)           
        {
            $uid = $data;
            echo '111';
        }else{
            $result = K::M('member/member')->chaxun(mobile,$data);  
            foreach($result as $v)
            {
                $uid = $v['uid'];
            }
        }
        // 根据uid查询content表,再从两张表中查出uid在其中的
        $d = K::M('code/content')->codeAll($uid);       
        $this->pagedata['data'] = $d;
        $this->tmpl = 'admin:huocode/shoutypeone.html';  
    }
}