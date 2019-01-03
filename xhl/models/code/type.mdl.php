<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * Author @xinghuali<xinghuali@126.com>
 * $Id: area.mdl.php 2034 2015-11-07 03:08:33Z xinghuali $
 */

class Mdl_Code_Type extends Mdl_Table
{   
  
    protected $_table = 'code_type';
    protected $_pk = 'id';
    protected $_cols = 'id, tname, status';
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

    public function delete($data)
    {
       $sql = "DELETE FROM ".$this->table($this->_table)." WHERE id = ".$data;
       return $this->db->Execute($sql);
    }
	
   
    public function deletetype2($data)
    {
         if (!$data) {
            $returnData = ['code'=>1, 'info'=>'数据错误'];
        } else {
            $data = $this->chaxun('id',$data);
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

}