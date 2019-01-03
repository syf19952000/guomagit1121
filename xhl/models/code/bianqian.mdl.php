<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * Author @xinghuali<xinghuali@126.com>
 * $Id: area.mdl.php 2034 2015-11-07 03:08:33Z xinghuali $
 */

class Mdl_Code_Bianqian extends Mdl_Table
{   
  
    protected $_table = 'code_bianqian';
    protected $_pk = 'id';
    // protected $_cols = 'id, title, content, status, time, uid';
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
	

    //查询 $key:字段  $val:值
    public function chaxun($key, $val)
    {
        if (empty($val)) {
            return '';
        }
        $where = $key . ' = "'. $val . '"';
        $sql = "SELECT * FROM " . $this->table($this->_table) . " where " . $where;
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

    //便签内容添加
    public function addBianqian($data)
    {
        // var_dump($data['tid']);die;
        $bianqian = k::M('code/bianqian')->chaxun('tid', $data['tid']);
        if (empty($data['tid'])) {
            $this->err->add('分类信息为空', 201);
        } elseif (empty($data['title']) || empty($data['content'])) {
            $this->err->add('标题或者内容不能为空', 201);
        } elseif (!empty($bianqian)) {
            $this->err->add('该二维码已经编辑');
        } else {    
            $saveData['title'] = $data['title'];
            $saveData['content'] = $data['content'];
            $saveData['tid'] = $data['tid'];
            $saveData['time'] = date('Y-m-d H:i:s', time());
            $success = K::M('code/bianqian')->create($saveData, true);
            return $success;
        }
    }

    //便签内容更新
    public function edit($data)
    {
        $bianqian = K::M('code/bianqian')->chaxun('tid', $data['tid']);
        foreach ($bianqian as $v) {
            $bianqian = $v;
        }
        if (empty($data['title']) || empty($data['content'])) {
            $this->err->add('标题或者内容不能为空', 201);
        } else {
            $saveData['title'] = $data['title'];
            $saveData['content'] = $data['content'];
            $saveData['time'] = date('Y-m-d H:i:s', time());
            $saveData['tid'] = $data['tid'];
            if (!$bianqian) {
                $success = K::M('code/bianqian')->create($saveData, true);
                return $success;
            } else {
                $success = K::M('code/bianqian')->update($bianqian['id'], $saveData, true);
                return $success;
            }
        }
    }

    //便签删除
    public function shanchu()
    {
        //二维码id 查便签内容
        //
    }

    public function codeData($data)
    {
        $a = K::M('code/bianqian')->chaxun('id', $data);
        foreach ($a as $v) {
            $tid = $v['tid'];
            $title = $v['title'];
        }
        $aa = K::M('code/content')->chaxun('id', $tid);
        foreach ($aa as $v) {
            $typeId = $v['type_id'];
            $img = $v['code_link'];
            $id = $v['id'];
        }
        $aaa = K::M('code/type')->chaxun('id', $typeId);
        foreach ($aaa as $v) {
            $type = $v['tname'];
        }
        $arr['title'] = $title;
        $arr['type'] = $type;
        $arr['code_link'] = $img;
        $arr['id'] = $id;

        return $arr;
    }

    public function all()
    {
        // 我是从两个表取数据合并还是根据content表的tid取数据合并，
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

    public function delete($key,$data)
    {
        $where = $key.'='.$data;
        $sql="DELETE FROM ".$this->table($this->_table)." WHERE " .$where;
        return $this->db->Execute($sql);
    }
}