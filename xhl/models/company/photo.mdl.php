<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * $Id: photo.mdl.php 2015-09-27 02:07:36  xinghuali
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

class Mdl_Company_Photo extends Mdl_Table
{   
  
    protected $_table = 'company_photo';
    protected $_pk = 'photo_id';
    protected $_cols = 'photo_id,company_id,type,title,photo';

//    protected $_type_means = array(1=>'工厂设备',2=>'信誉担保');    
    protected $_type_means = array(1=>'工厂设备');    
    protected $_type = array('qualification'=>1,'honor'=>2);
    
    protected $_orderby = array('photo_id'=>'DESC');
    
    public function get_type_means(){
        
        return $this->_type_means;
    }
    public function get_type(){
        
        return $this->_type;
    }
    
    public function create($data, $checked=false)
    {
        if(!$checked && !$data = $this->_check_schema($data)){
            return false;
        }
        return $this->db->insert($this->_table, $data, true);
    }

    public function update($pk, $data, $checked=false)
    {
        $this->_checkpk();
        if(!$checked && !$data = $this->_check_schema($data,  $pk)){
            return false;
        }
        return $this->db->update($this->_table, $data, $this->field($this->_pk, $pk));
    }

    public function upload()
    {

    }

    public function items_by_company($company_id, $p=1, $l=50, &$count=0)
    {
        if(!$company_id = (int)$company_id){
            return false;
        }
        return $this->items(array('company_id'=>$company_id), null, $p, $l, $count);
    }
}