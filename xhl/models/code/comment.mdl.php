<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * Author @xinghuali<xinghuali@126.com>
 * $Id: area.mdl.php 2034 2015-11-07 03:08:33Z xinghuali $
 */

class Mdl_Code_Comment extends Mdl_Table
{   
  
    protected $_table = 'code_comment';
    protected $_pk = 'id';
    //protected $_cols = 'id, tname, status';
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

    public function insert($data)
    {
         if (empty($data['content'])) {
            $this->err->add('回复内容为空', 201);
        }else {    
            $saveData['comment'] = $data['content'];
            $saveData['create_time'] = date('Y-m-d H:i:s', time());
            $saveData['article_id'] = $data['code_erid']; //二维码表的id
            $success = K::M('code/comment')->create($saveData, true);
            return $success;
        }
    }


}