<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * Author @xinghuali<xinghuali@126.com>
 * $Id: area.mdl.php 2034 2015-11-07 03:08:33Z xinghuali $
 */

class Mdl_Code_Content extends Mdl_Table
{   
  
    protected $_table = 'code_content';
    protected $_pk = 'id';
    protected $_cols = 'id, title, content, status, time, uid';
    // protected $_orderby = array('orderby'=>'ASC', 'city_id'=>'ASC');
    // protected $_pre_cache_key = 'data-area-list';

    
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        if($area_id = $this->db->insert($this->_table, $data, true)){
            $this->flush();
        }
        return $area_id;
    }

    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !$data = $this->_check_schema($data,  $pk)){
            return false;
        }
        if($this->db->update($this->_table, $data, $this->field($this->_pk, $pk))){
            $this->flush();
        }
        return true;
    }

    public function delete($key,$data)
    {
        $where = $key.'='.$data;
        $sql="DELETE FROM ".$this->table($this->_table)." WHERE " .$where;
        return $this->db->Execute($sql);
    }
	
    //查询 $key:字段  $val:值
    public function chaxun($key, $val, $desc='desc')
    {
        if (empty($val)) {
            return false;
        }
        $where = $key . ' = "'. $val . '"';
        $sql = "SELECT * FROM " . $this->table($this->_table) . " where status=1 and " . $where . " order by time " . $desc;
        // var_dump($sql);
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row[$this->_pk]] = $row;
            }
        }
        self::$_CACHE_TABLES[$this->_pre_cache_key] = $items;
        $this->cache->set($this->_pre_cache_key, $items, $this->_cache_ttl);

        return $items;
    }

    public function chaxun222($key,$val)
    {
        $where = $key . ' = "'. $val . '"';
        //var_dump($where);
        $sql = "SELECT * FROM " . $this->table($this->_table) ." WHERE " .$where;
        //var_dump($sql);
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row[$this->_pk]] = $row;
            }
        }
        self::$_CACHE_TABLES[$this->_pre_cache_key] = $items;
        $this->cache->set($this->_pre_cache_key, $items, $this->_cache_ttl);

        return $items;
    }



    public function codeAll($data)  // 查询bianqian表中 uid=$uid 的所有数据
    {
        echo '<pre>';
        $a = $this->chaxun('uid', $data, 'desc');
        $tids = '';
        foreach ($a as $v) {
            $tids .= $v['id'] . ',';
            $type .=$v['type_id'] . ',';
        }
        $tids = rtrim($tids, ',');
        $types = rtrim($type, ',');
        $where1 = " id in(". $types .")";
        $where = " tid in(". $tids .")";
        $sql = "SELECT * FROM " . " xhl_code_bianqian " . " where" . $where;
        $sql1 = "SELECT * FROM " . " xhl_code_type " . " where" . $where1;
        $sql2 = "SELECT * FROM " . " xhl_code_huiyi " . " where" . $where;
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row[$this->_pk]] = $row;
            }
        }
        if($rs1 = $this->db->Execute($sql1)){
            while($row = $rs1->fetch()){
                $items1[$row[$this->_pk]] = $row;
            }
        }
        if($rs2 = $this->db->Execute($sql2)){
            while($row = $rs2->fetch()){
                $items2[$row[$this->_pk]] = $row;
            }
        }
        
        foreach ($items1 as $v) {
            $typename[$v['id']] = $v;
        }
        foreach ($items as $v) {
            $title[$v['tid']] = $v;
        }
        foreach ($items2 as $v) {
            $title[$v['tid']] = $v;
        }
        // var_dump($typename);die;
        foreach ($a as &$v) {
            $v['tname'] = $typename[$v['type_id']]['tname'];
            $v['title'] = $title[$v['id']]['title'];
            $v['time'] = $title[$v['id']]['time'];
        } 
        return $a;
    }


    public function all()
    {
        $sql = "SELECT * FROM " . $this->table($this->_table);
        // var_dump($sql);
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row[$this->_pk]] = $row;
            }
        }
        self::$_CACHE_TABLES[$this->_pre_cache_key] = $items;
        $this->cache->set($this->_pre_cache_key, $items, $this->_cache_ttl);

        return $items;
    }


    public function codeAll222($data)   // 后台显示所有biaoqian码表
    {
        echo '<pre>';
        $a = $this->all();  

        $tids = '';
        foreach ($a as $v) {
            $tids .= $v['id'] . ',';
        }

        $tids = rtrim($tids, ',');
        $where = " tid in(". $tids .")";
        $sql = "SELECT * FROM " . " xhl_code_bianqian " . " where" . $where;
        $sql2 = "SELECT * FROM " . " xhl_code_huiyi " . " where" . $where;
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row[$this->_pk]] = $row;
            }
        }
        if($rs2 = $this->db->Execute($sql2)){
            while($row = $rs2->fetch()){
                $items2[$row[$this->_pk]] = $row;
            }
        }
        //$ff = array_merge($items,$items2);  // 空的,当其中一个数组为空的时候
        $ff = array_merge((array)$items,(array)$items2);
        foreach($a as $v)
        {
            $uid[$v['id']] = $v;
            foreach($ff as &$n)
            {
                //$a['id'] = $v['tid'];  // 在新数组中加code_link属性
                $code_link[$a['id']] = $n['tid'];
                $n['uid'] = $uid[$v['id']]['uid'];
            }
        }      
        return $ff;
    }


    public function codeAll333($data)   // 后台显示所有，content表
    {
        echo '<pre>';
        $d = $this->all();  
        $ids = '';
        foreach($d as $v)
        {
            $ids .= $v['id'].',';
        }
        $ids = rtrim($ids, ',');
        $where = " code_id in (".$ids.")";
        $sql = "SELECT * FROM " . " xhl_code_fangwen " . " where" . $where;
        // 方法二
        // $sql2 = "SELECT b.id,b.type_id,b.status,b.uid,b.code_link,b.code,a.num FROM " . " xhl_code_fangwen a left join xhl_code_content b on a.code_id = b.id"; 
        if($rs = $this->db->Execute($sql)){
            while($row = $rs->fetch()){
                $items[$row[$this->_pk]] = $row;
            }
        }
        // if($rs = $this->db->Execute($sql2)){
        //     while($row = $rs->fetch()){
        //         $items2[$row[$this->_pk]] = $row;
        //     }
        // }
        // 
        foreach($items as $v)  // 访问表，有code_id
        {
            $num[$v['code_id']] = $v;
        }
        foreach($d as $v)
        {
            $code_id[$v['id']] = $v;
        }
        foreach($d as &$v)
        {
            $num[$v['code_id']]['code_id'] = $v['id'];  // $items的code_id是$d的id
            $v['num'] = $num[$v['id']]['num'];          // 为什么这样就可以，而不是$num[$v['code_id']]['num']
        }
        // var_dump($d);
        // die;
        return $d;
    }



    //添加子表内容
    //有改动  需要重新写 建立新表
    public function addCode($data)
    {
        if (empty($data['content']) || empty($data['title'])) {
            $data = ['code'=>1, 'info'=>'标题或者内容不允许为空'];
        } else {
            $uid = $this->cookie->get('uid');
            $code_data['title'] = $data['title'];
            $code_data['type_id'] = $type;//type  还没传
            $code_data['content'] = $data['content'];
            //执行添加操作 子表
            if ($success) {
                $data = ['code'=>2, 'info'=>'success', 'data'=>$code_data];
            } else {
                $data = ['code'=>3, 'info'=>'添加失败'];
            }
        }
        
        return $data;
    }

    //删除二维吗
    public function shanchu($data)
    {
        if (!$data) {
            $returnData = ['code'=>1, 'info'=>'数据错误'];
        } else {
            $data = $this->chaxun('code',$data);
            if ($data) {
                foreach ($data as $v) {
                    $data = $v;
                }
                $succ = $this->update($v['id'], ['status'=>0], 1);
                if ($succ) {
                    $returnData = ['code'=>3, 'info'=>'删除成功'];
                } else {
                    $returnData = ['code'=>4, 'info'=>'删除失败'];
                }
            } else {
                $returnData = ['code'=>2, 'info'=>'二维码不存在'];
            }
        }
        
        return $returnData;
    }

    //创建空二维码  唯一标识code
    //完整链接待确定
    public function mkCode()
    {
        include '/static/phpqrcode/phpqrcode.php';
        $str='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890';
        $randStr = str_shuffle($str);
        $data['code'] = substr($randStr,0,6);
        $code = base64_encode($data['code']);
        //完整链接待确定
        $link = 'http://yz.1d16.com/ewm-moveCode?code=' . $code;
        $name = date('Ymd') . time() . rand(1000000, 9999999);
        $code_link = 'static/code/' . $name . '.png';
        QRcode::png($link, $code_link, '', 10, 2, true);    // 线上的提示这个不存在
        $code_data['code'] = $data['code'];
        $code_data['time'] = date('Y-m-d H:i:s', time());
        $code_data['code_link'] = $code_link;
        $success = K::M('code/content')->create($code_data, true);
        if ($success) {
            return $data['code'];
        } else {
            return false;
        }
    }

    //认领二维码  更新二维码 uid type至表中
    //$type:码类型id   $codeId:码主键id
    public function moveCode($type, $codeId)
    {
        $uid = $this->cookie->get('uid');
        if ($uid) {
            $saveData['type_id'] = $type;
            $saveData['uid'] = $uid;
            $a = $this->update($codeId, $saveData, 1);
            return $a;
        } else {
            return false;
        }
    }

    //配置模板文件名
    public function cfg()
    {
        $arr = [
            1 => 'biaoqianma',//
            2 => 'huiyima',//回忆码
        ];

        return $arr;
    }







    //
    public function areas_by_city($city_id)
    {
        $areas = array();
        if($items = $this->fetch_all()){
            foreach($items as $k=>$v){
                if($v['city_id'] == $city_id){
                    $areas[$k] = $v;
                }
            }
        }
        return $areas;        
    }
    
    public function options($city_id=null)
    {
    	$options = array();
    	if($areas = $this->fetch_all()){
            if($city_id === null){
                $citys = K::M('data/city')->fetch_all();          
                foreach($citys as $k=>$v){
                    foreach($areas as $kk=>$vv){
                        if($vv['city_id'] == $k){
                            $options[$v['city_name']][$kk] = $vv['area_name'];
                        }
                    }
                }
            }else{
        		foreach($areas as $k=>$v){
                    if($v['city_id'] == $city_id){
            			$options[$k] = $v['area_name'];
                    }
        		}
            }
    	}
    	return $options;
    }

    public function area($area_id)
    {
        if(!$area_id = intval($area_id)){
            return false;
        }else if($items = $this->fetch_all()){
            return $items[$area_id];
        }
        return false;
    }    

    protected function _format_row($row)
    {
        static $citys = null;
        if($citys === null){
            $citys = K::M('data/city')->fetch_all();
        }
        if($citys[$row['city_id']]){
            $row['city_name'] = $citys[$row['city_id']]['city_name'];
        }else{
            $row['city_name'] = $row['city_id'];
        }
        return $row;
    }
}