<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * $Id: index.ctl.php 2015-11-26 06:32:36  xinghuali
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Ctl_Ewm extends Ctl
{

    public function xuqiu()
    {
        $list = K::M('code/xuqiu')->chaxun555();
        // var_dump($result);
        foreach($list as $key=>$value){
            $list[$key]['count']=count(explode('-',$value['bpath']));
        }
        // die;
        $this->pagedata['alist'] = $list;
        $this->tmpl = 'xuqiu_add.html';
    }

    public function xuqiu_list()
    {
        $list = K::M('code/xuqiu')->chaxun555();
        foreach($list as $key=>$value){
            $list[$key]['count']=count(explode('-',$value['bpath']));
        }
         $this->pagedata['alist'] = $list;
        $this->tmpl = 'xuqiu_list.html';
    }

    public function xuqiu_add()
    {
        echo '<pre>';
        $pid = $this->GP('pid');    // pid是新添加数据的id
        $name = $this->GP('name');
        // 执行添加操作
        $list = K::M('code/xuqiu')->add555($pid,$name);
        if($list)
        {
            $this->err->add('添加分类成功', 201);
        }else{
            $this->err->add('添加分类失败', 201);
        }
        
        // var_dump($list);
        // die;
        $this->tmpl = 'xuqiu_add.html';
    }

    // 应该是 ajax 删除
    public function xuqiu_delete()
    {
        echo '<pre>';
        $url = $this->request['uri'];    
        $url = explode('=',$url);
        $id = $url[1];
        $result = K::M('code/xuqiu')->chaxun222('pid',$id);
        // var_dump($result);
        // die;
        if($result)
        {
             $this->err->add('模块下操作不能空，不能删除', 201);
        }else{
             $list = K::M('code/xuqiu')->delete('id',$id);
            // 设置有子类的情况下不能删除
            if($list)
            {
                $this->err->add('删除分类成功', 201);
            }else{
                $this->err->add('删除分类失败', 201);
            }
        }
        $this->tmpl = 'xuqiu_list222.html';
    }


    // 第2 种
    public function xuqiu333()
    {
        echo '<pre>';
        $list = K::M('code/xuqiu')->chaxun555();
        // $a = $this->array2level($list); // 这个有level字段
        $b = $this->arr2tree($list);    // 展示出树级结构，chilren,遍历显示在页面
        // var_dump($b);
        // die;
        // foreach($b as $v)  // 不应该多加一层遍历
        // {
        //     $b = $v;
        // }
        $this->pagedata['commlist'] = $b;
        $this->tmpl = 'xuqiu_list222.html';
        
    }

    public function xuqiu_add222()
    {
        // echo '<pre>';
        $name = $this->GP('name');
        $pid = $this->GP('id');
        $list = K::M('code/xuqiu')->addfenlei($pid,$name);
        // 返回json数据给前台
        $returnData = json_encode($list,JSON_UNESCAPED_UNICODE); 
        // return $returnData;
        echo $returnData;   // 只有echo才打印信息，如果是return就返回信息为空，格式正确，但是走error
        exit;    
    }

    public function xuqiu_delete222()
    {
        // echo '<pre>';
        $id = $this->GP('id');
        $a = K::M('code/xuqiu')->deletefenlei($id);
        $returnData = json_encode($a,JSON_UNESCAPED_UNICODE); 
        echo $returnData;   
        exit;    
    }


    // public function test()
    // {
    //     $a = $this->array2level($list); 
    //     foreach($a as $v)
    //     {
    //         if($v['level'] = 2)
    //     }
    // }





    function array2level($array, $pid = 0, $level = 1) 
    { 
        static $list = []; 
        foreach ($array as $v) 
        { 
            if ($v['pid'] == $pid) 
            { 
                $v['level'] = $level;
                $list[] = $v; 
                $this->array2level($array, $v['id'], $level + 1);
             }
         } 
    return $list; 
    }

    function arr2tree($tree, $rootId = 0,$level=1) 
    { 
        $return = array(); 
        foreach($tree as $leaf) 
        { 
            if($leaf['pid'] == $rootId) 
            { 
                $leaf["level"] = $level; 
                foreach($tree as $subleaf) 
                { 
                    if($subleaf['pid'] == $leaf['id']) 
                    { 
                        $leaf['children'] = $this->arr2tree($tree, $leaf['id'],$level+1); break; 
                    } 
                } 
                $return[] = $leaf; 
            } 
        } 
        return $return;
     }

    
   // 第三种
   // 把增加同级目录，下级目录修改成，输入名字之后就存数据库
   


    public function xuqiu_update333()
    {
        // echo '<pre>';
        $name = $this->GP('name');
        $pid = $this->GP('id');
        $data['name'] = $name;
        $list = K::M('code/xuqiu')->update($pid,$data,true);
        if($list)
        {
            $returnData = ['code'=>3, 'info'=>'编辑成功'];
        }else{
            $returnData = ['code'=>4, 'info'=>'编辑失败'];
        }
        $returnData = json_encode($returnData,JSON_UNESCAPED_UNICODE); 
        echo $returnData;   // 只有echo才打印信息，如果是return就返回信息为空，格式正确，但是走error
        exit;
        // die;
         
    }


    public function xuqiu_delete333()
    {
        // echo '<pre>';
        $id = $this->GP('id');
        // var_dump($id);
        // die;
        $a = K::M('code/xuqiu')->deletefenlei($id);
        $returnData = json_encode($a,JSON_UNESCAPED_UNICODE); 
        echo $returnData;   
        exit;    
    }


    public function xuqiu_add333()
    {
        // 添加同级目录，就是添加id 的同pid =pid
        // 添加下级目录，就是pid=id
        // 添加同级还是下级 tag=0 添加同级 tag=1 添加下级
        // 下级的路径不对，pid存的0，路径村的-
        echo '<pre>';
        $pid = $this->GP('pid');    // pid是新添加数据的id,当添加的是下级目录时，提示$pid没有定义
        $name = $this->GP('name');
        $tag = $this->GP('tag');
        var_dump($pid);
        // 执行添加操作
        $list = K::M('code/xuqiu')->add666($pid,$name,$tag);
        // die;
        // var_dump($pid);
        // var_dump($name);
        // var_dump($tag);
        // die;
        $returnData = json_encode($list,JSON_UNESCAPED_UNICODE); 
        echo $returnData;   // 只有echo才打印信息，如果是return就返回信息为空，格式正确，但是走error
        exit;
    }






	//加载活码页面
    public function index()
    {
       $data = $this->request['uri'];

        $a = explode('=',$data);
        $page = $a[1];
        // var_dump($page);
        // die;
        $uid = $this->cookie->get('uid');
        if (!$uid) {
            $this->err->add('请先登录', 201);
        } else {
            $data2 = K::M('code/content')->codeAll($uid);
            $num = K::M('code/fangwen')->chaxun('uid', $uid);
            foreach ($num as $k=>$v) {
                $nums[$v['code_id']] = $v;
            }
            foreach ($data2 as $k=>&$v) {
                $v['num'] = $nums[$k]['num'];
            }
            // 分页
            $count = count($data2);  // 数据总条数
            $pagenum = 1;
            $pagesize = 3 ;         // 每页的数据数
            $pagecount = ceil($count/$pagesize);    // 总页数 ,尾页
            // 当$page = 0 的时候，$page应该加1，
            $page2 = $page + 1;
            //$start = ($page-1)*$pagesize;     // 计算每次分页的开始位置，当是第一页的时候，$page=1,当时第二页的时候，$page还是1
            $start = ($page2-1)*$pagesize;  
            $pagedata = array();
            $pagedata = array_slice($data2,$start,$pagesize);  // 为什么总是重复几条数
            $this->pagedata['pagecount'] = $pagecount-1;
            $this->pagedata['page'] = $page;      
            $this->pagedata['data'] = $pagedata;
            $this->tmpl = 'ewm.html';
        }
    }

   
    


    //加载选择码类型页面
    public function code()
    {
        $a = K::M('code/type')->all();
        $code = $this->getlink(9);
        $this->pagedata['data'] = $a;
        $this->pagedata['code'] = $code['code'];

    	$this->tmpl = 'ewm_class.html';
    }

    //选择码类型 展示相应的模板文件
    //同时生成码 认领
    public function codeType()
    {
        $data = $this->getlink(13);
        $arr = K::M('code/content')->cfg();
        $uid = $this->cookie->get('uid');
        if (!$arr[$data['type']]) {         //这后期要改，因为后台删除type的话，id是不会清零 的增长
            $this->err->add('分类待完善', 201);
        } elseif ($uid) {
            $code = $data['code'];
            if (!$code) {
                $code = K::M('code/content')->mkCode();
            }
            $codeData = K::M('code/content')->chaxun('code', $code);
            foreach ($codeData as $v) {
                $codeId = $v['id'];
            }
            $a = K::M('code/content')->moveCode($data['type'], $codeId);
            $link = $arr[$data['type']];
            if ($a) {
                $this->pagedata['tid'] = $codeId;
                $this->tmpl = $link . '.html';
            } else {
                $this->err->add('生成二维吗失败', 201);
            }
        } else {
            $this->err->add('请先登陆', 201);
        }
    }

    //子表内容添加 第一个
    public function addContent()
    {
    	$data = $this->GP('data');
        $dataBianqian = K::M('code/bianqian')->addBianqian($data);
        $a = K::M('code/bianqian')->codeData($dataBianqian);
        if ($dataBianqian) {
            $this->index();
            // $this->pagedata['data'] = $a;
            // $this->tmpl = 'ewm_information.html';
        } else {
            $this->err->add('生成二维吗失败', 201);
        }
    }


    //子表内容添加 回忆码
    public function huiyi()
    {
        $data = $this->GP('data');
        
        $dir = $_SERVER['DOCUMENT_ROOT'].'/static/imgs';    // 上传的文件夹
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $dir = $dir = $dir .'/'.date('Y');
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $dir = $dir .'/'.date('m');
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $dir = $dir .'/'. date('d');
        if (!file_exists($dir)) {
            mkdir($dir);
        }
        $imgLink = $dir .'/'. $_FILES["img"]["name"];
        move_uploaded_file($_FILES["img"]["tmp_name"], $imgLink);
        $imgLink = str_replace($_SERVER['DOCUMENT_ROOT'], '', $imgLink);
        
        $data['img'] = $imgLink;
        $dataHuiyi = K::M('code/huiyi')->addHuiyi($data);
        $a = K::M('code/huiyi')->codeData($dataHuiyi);
        if ($dataHuiyi) {
            $this->index();
            // $this->pagedata['data'] = $a;
            // $this->tmpl = 'ewm_information.html';
        } else {
            $this->err->add('生成二维吗失败', 201);
        }
    }

    //加载子表内容编辑页面
    public function edit()      // 修改操作统一进这一方法 
    {
        $id = $this->getlink(9);    // $id['id'] 是content表中的id
        $arr = K::M('code/content')->cfg();
        $arr1 = K::M('code/content')->chaxun('id', $id['id']);
        foreach($arr1 as $v)
        {
            $type_id = $v['type_id'];
        }
        switch ($type_id) {
            case '1':
                $data = K::m('code/bianqian')->chaxun('tid', $id['id']);
                break;
            case '2':
                $data = K::m('code/huiyi')->chaxun('tid', $id['id']);
                break;
            default:
                # code...
                break;
        }
        foreach ($data as $v) {
            $data = $v;
        }
        if (empty($data)) {
            $data['tid'] = $id['id'];
        }
        foreach ($arr1 as $val) {
            $vv = $val;
        }
        //$link = $arr[$val['type_id']];      
        $link = $arr[$vv['type_id']];
        $this->pagedata['data'] = $data;
        $this->tmpl = $link . '_edit.html';
    }

    //执行子表内容更新或者添加操作 编辑
    public function editup()
    {
        $data = $this->GP('data');
        $bianqian = K::M('code/bianqian')->chaxun('tid', $data['tid']);
        if ($bianqian) {
           $a = K::M('code/bianqian')->edit($data);
        } else {
            if (!empty($_FILES['img'])) {
                
                $dir = $_SERVER['DOCUMENT_ROOT'].'/static/imgs';
                if (!file_exists($dir)) {
                    mkdir($dir);
                }
                $dir = $dir = $dir .'/'.date('Y');
                if (!file_exists($dir)) {
                    mkdir($dir);
                }
                $dir = $dir .'/'.date('m');
                if (!file_exists($dir)) {
                    mkdir($dir);
                }
                $dir = $dir .'/'. date('d');
                if (!file_exists($dir)) {
                    mkdir($dir);
                }
                $imgLink = $dir .'/'. $_FILES["img"]["name"];
                move_uploaded_file($_FILES["img"]["tmp_name"], $imgLink);
                $imgLink = str_replace($_SERVER['DOCUMENT_ROOT'], '', $imgLink);
                
                $data['img'] = $imgLink;
            }
            $huiyi = K::M('code/huiyi')->chaxun('tid', $data['tid']);
            $a = K::M('code/huiyi')->edit($data);
        }
        if ($a) {
            // $this->tmpl = 'ewm.html';//跳转至详情页
            $this->index();
        } else {
            $this->err->add('编辑失败', 201);
        }
    }

    //删除二维码
    public function shanchu()
    {
        // 应该是content表和code表都进行删除
        $data = $_POST['a'];
        $shanchu = K::M('code/content')->shanchu($data);
        $returnData = json_encode($shanchu, JSON_UNESCAPED_UNICODE); 
        echo $returnData;
        exit;
    }

    //扫描二维码进入这个方法
    public function moveCode()      
    {
        $data = $this->getlink(13);
        $code = $data['code'];
        //$code = base64_decode($data['code']);  // 线上的获取方法
        $data = K::M('code/content')->chaxun('code', $code);
        foreach ($data as $v) {
            $data = $v;
        }
        if (!empty($data)) {
            if (empty($data['uid'])) {
                $id = $this->cookie->get('uid');
                if (!empty($id)) {
                    $this->pagedata['data'] = $data;
                    $this->tmpl = 'renling.html';
                } else {
                    $this->tmpl = 'login.html';
                }
            } else {
                $saveData['time'] = date('Y-m-d', time());
                $saveData['code_id'] = $data['id'];
                $saveData['uid'] = $data['uid'];
                $fangwen = K::M('code/fangwen')->fangwen('code_id', $data['id'], $saveData['time']);
                if (empty($fangwen)) {
                    $saveData['num'] = 1;
                    K::M('code/fangwen')->create($saveData, true);
                } else {
                    foreach ($fangwen as $v) {
                        $fangwen = $v;
                    }
                    $saveData['num'] = $fangwen['num'] + 1;
                    K::M('code/fangwen')->update($fangwen['id'], $saveData, true);
                }
                //跳转至展示页面  完了写活
                $this->bianqian($code);
            }
        } else {
            $this->err->add('请求错误,请稍后再试'); 
        }
    } 

    //加载便签详情页面，直接加载的时候进这个方法，不统计访问数量
    public function bianqian($code=0)
    {
        if (!$code) {
            $data = $this->getlink(13);
            $code = $data['code'];
        }
        $arr = K::M('code/content')->cfg();
        $codeData = K::M('code/content')->chaxun('code', $code);
        if (!$codeData) {
            $this->err->add('信息不存在');
        } else {
            foreach ($codeData as $v) {
                $id = $v;
                $type_id = $v['type_id'];
            }
            // 也可以把type_id的判断放到模型中判断，ewm中把type_id传到模型中
            switch ($type_id) {
                case '1':
                    $bianqian = K::M('code/bianqian')->chaxun('tid', $id['id']);
                    break;
                case '2':
                    $bianqian = K::M('code/huiyi')->chaxun('tid', $id['id']);
                    break;
                default:
                    # code...
                    break;
            }
            foreach ($bianqian as $v) {
                $bianqianData = $v;
            }
            $link = $arr[$id['type_id']];   // $id 是二维码表中的id
            $this->pagedata['data'] = $bianqianData;
            $this->pagedata['code'] = $id;

            $this->tmpl = $link . '_y.html'; 
        }
        
    } 

    //截取链接后缀
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

    //加载认领页面
    // public function renling($data)
    // {
    //     $codeData = k::M('code/content')->mkcode();
    //     $data = K::M('code/content')->chaxun('code', $codeData);
    //     foreach ($data as $v) {
    //         $data = $v['code_link'];
    //     }

    //     $this->pagedata['data'] = $data;
    //     $this->tmpl = 'renling.html';
    // }

    //认领成功页面
    // public function succ()
    // {
    //     $this->tmpl = 'renling_2.html';
    // }
    



}