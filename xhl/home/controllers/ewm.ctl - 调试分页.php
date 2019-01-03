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
	//加载活码页面
    public function index()
    {
       $data = $this->request['uri'];
        $a = explode('=',$data);
        $page = $a[1];
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
           
            $count = count($data);  // 数据总条数
            $pagenum = 1;
            $pagesize = 2 ;     // 每页的数据数
            $pagecount = ceil($count/$pagesize); // 总页数

            $page2 = $page + 1;
            //$start = ($page-1)*$pagesize;  // 计算每次分页的开始位置，当是第一页的时候，$page=1,当时第二页的时候，$page还是1
            $start = ($page2-1)*$pagesize; 
            // 当$page = 0 的时候，$page应该加1，
            $pagedata = array();
            $pagedata = array_slice($data2,$start,$pagesize);  // 为什么总是重复几条数据
            // 当page = 0 ，start = -2 ,会取 23    之前的2个，显示  333 111          0 
            //   page = 2,  start = 2        34              333 111 ,$page = 2
            //   page = 1,  start = 0  取数字12                     444  222         1
            //假如是 1234，下标则是 0123
            // echo '<pre>';
            // var_dump($page);
            // var_dump($start);
            // var_dump($pagedata);           
            // die;
 
            $this->pagedata['page'] = $page;      // 上一页和下一页可以用了，尾页不能
            $this->pagedata['data'] = $pagedata;
            $this->tmpl = 'ewm.html';
        }
    }

    // 没有做分页的时候
    // public function index()
    // {
    //     $uid = $this->cookie->get('uid');
    //     if (!$uid) {
    //         $this->err->add('请先登录', 201);
    //     } else {
    //         $data = K::M('code/content')->codeAll($uid);
    //         $num = K::M('code/fangwen')->chaxun('uid', $uid);
    //         foreach ($num as $k=>$v) {
    //             $nums[$v['code_id']] = $v;
    //         }
    //         foreach ($data as $k=>&$v) {
    //             $v['num'] = $nums[$k]['num'];
    //         }
    //         //$data['count'] = count($data);
    //         $this->pagedata['data'] = $data;
    //         $this->tmpl = 'ewm.html';
    //     }
    // }

    // 在ewm.html页面加一个分页
    public function fenye()
    {
        $data = $this->request['uri'];
        $a = explode('=',$data);
        $page = $a[1];
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
           
            $count = count($data);  // 数据总条数
            $pagenum = 1;
            $pagesize = 2 ;     // 每页的数据数
            $pagecount = ceil($count/$pagesize); // 总页数
            $start = ($page-1)*$pagesize;  // 计算每次分页的开始位置
            $pagedata = array();
            $pagedata = array_slice($data2,$start,$pagesize);
            // echo '<pre>';
            // var_dump($pagedata);           
            // die
 
            $this->pagedata['page'] = $page;      // 上一页和下一页可以用了，尾页不能
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