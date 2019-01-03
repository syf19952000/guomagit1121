<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * Author @xinghuali<xinghuali@126.com>
 * $Id: area.mdl.php 2034 2015-11-07 03:08:33Z xinghuali $
 */

class Mdl_Code_Huiyi extends Mdl_Table
{   
  
    protected $_table = 'code_huiyi';
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
	
    public function delete($key,$data)
    {
        $where = $key.'='.$data;
        $sql="DELETE FROM ".$this->table($this->_table)." WHERE " .$where;
        return $this->db->Execute($sql);
    }


    //查询 $key:字段  $val:值
    public function chaxun($key, $val)
    {
        if (empty($val)) {
            return '';
        }
        $where = $key . ' = "'. $val . '"';
        $sql = "SELECT * FROM " . $this->table($this->_table) . " where " . $where;
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

    //回忆码内容添加
    public function addHuiyi($data)
    {
        // var_dump($data['tid']);die;
        $bianqian = k::M('code/huiyi')->chaxun('tid', $data['tid']);
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
            $saveData['img'] = $data['img'];
            $success = K::M('code/huiyi')->create($saveData, true);
            return $success;
        }
    }

    //回忆码内容更新
    public function edit($data)
    {
        echo '<pre>';
        //var_dump($data);
        $huiyi = K::M('code/huiyi')->chaxun('tid', $data['tid']);
        foreach ($huiyi as $v) {
            $huiyi = $v;
        }
        if (empty($data['title']) || empty($data['content'])) {
            $this->err->add('标题或者内容不能为空', 201);
        } else {
            $saveData['title'] = $data['title'];
            $saveData['content'] = $data['content'];
            $saveData['time'] = date('Y-m-d H:i:s', time());
            $saveData['tid'] = $data['tid'];
            $saveData['img'] = $data['img'];
            if (!$huiyi) {
                $success = K::M('code/huiyi')->create($saveData, true);
                return $success;
            } else {
                $success = K::M('code/huiyi')->update($huiyi['id'], $saveData, true);
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
        $a = K::M('code/huiyi')->chaxun('id', $data);
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

}