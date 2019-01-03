<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * Author @xinghuali<xinghuali@126.com>
 * $Id: area.mdl.php 2034 2015-11-07 03:08:33Z xinghuali $
 */

class Mdl_Code_Xuqiu extends Mdl_Table
{   
  
    protected $_table = 'xuqiu';
    protected $_pk = 'id';
    protected $_cols = 'id, name, pid, path';
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

    public function chaxun555($key, $val, $desc='desc')
    {
        $sql = "SELECT id,name,pid,path,concat(path,'-',id) as bpath FROM " . $this->table($this->_table) ." order by bpath ";
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

    

    public function add555($pid,$name)
    {
        $pid=isset($pid)?(int)$pid:0;
        if($pid==0){
            $path =0;
        }else{
            $list = $this->chaxun222('id',$pid);
            foreach($list as $v)
            {
                $list = $v;
            }
            $path =$list['path'].'-'.$list['id'];   //子类的path为父类的path加上父类的id
            // var_dump($list);
            // var_dump($path);
            // die;
        }
         $datb['name'] = $name;
         $datb['pid'] = $pid;
         $datb['path'] = $path;
         $result = $this->create($datb,true);
         if ($result) {
            return $result;
        } else {
            return false;
        }
    }

    
    public function add666($pid,$name,$tag)
    {
        var_dump($pid);
         //  tag=0 添加同级 tag=1 添加下级
        if($tag == 0)
        {
            $list = $this->chaxun222('id',$pid);  // 同级数据
            foreach($list as $v)
            {
                $list = $v;
            }
            $datb['name'] = $name;
            $datb['pid'] = $list['pid'];  // pid是相同的
            $datb['path'] = $list['path'];
        }else if($tag == 1){
            $list = $this->chaxun222('id',$pid);  // 下级数据
            var_dump($pid); // 提侍没有定义
            var_dump($list);

            foreach($list as $v)
            {
                $list = $v;
            }
            $path =$list['path'].'-'.$list['id'];    //子类的path为父类的path加上父类的id
            $datb['name'] = $name;
            $datb['pid'] = $pid;
            $datb['path'] = $path;
        }
           
        $result = $this->create($datb,true);
        // var_dump($result);
        // 获取最新增加的id值
        if($result)
        {
            $returnData = ['code'=>3, 'info'=>'添加分类成功','id'=>$result];
        }else{
            $returnData = ['code'=>4, 'info'=>'添加分类失败','id'=>$result];
        }
        
        return $returnData;
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

    public function addfenlei($pid,$name)
    {
        $pid=isset($pid)?(int)$pid:0;
        if($pid==0){
            $path =0;
        }else{
            $list = $this->chaxun222('id',$pid);
            foreach($list as $v)
            {
                $list = $v;
            }
            $path =$list['path'].'-'.$list['id'];   //子类的path为父类的path加上父类的id
        }
         $datb['name'] = $name;
         $datb['pid'] = $pid;
         $datb['path'] = $path;
         $result = $this->create($datb,true);
       
        if($result)
        {
            $returnData = ['code'=>3, 'info'=>'评论成功'];
        }else{
            $returnData = ['code'=>4, 'info'=>'评论失败'];
        }
        
        return $returnData;
    }

    public function deletefenlei($id)
    {
        $result = K::M('code/xuqiu')->chaxun222('pid',$id); 
        // 设置有子类的情况下不能删除
        if($result)
        {
            $returnData = ['code'=>5, 'info'=>'模块下操作不能空，不能删除'];
        }else{
             $list = K::M('code/xuqiu')->delete('id',$id);
            if($list)
            {
                $returnData = ['code'=>3, 'info'=>'删除分类成功'];
            }else{
                $returnData = ['code'=>4, 'info'=>'删除分类失败'];
            }
        }
        return $returnData;
    }

    
    
   






   


  
}