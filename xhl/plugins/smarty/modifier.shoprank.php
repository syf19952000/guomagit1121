<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * Author xinghuali<xinghuali@126.com>
 * $Id: modifier.shoprank.php 3053 2015-01-15 02:00:13  xinghuali
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

function smarty_modifier_shoprank($credit, $onlyNum=false)
{
    $credit = (int)$credit;
    static $rankCfg = null;
    if($rankCfg === null){
        $rankCfg = K::$system->config->get('shop_rank');
    }
    for($i=20; $i>0; $i--){
        if($rankCfg['rank_'.$i] <= $credit){
            if($onlyNum){
                return $i;
            }else{
                return '<span class="taobao_credit" style="margin-top:8px;"><span class="rank_'.$i.'" title="<{$shop.credit}>"></span></span>';
            }
        }
    }
    if($onlyNum){
        return 1;
    }else{
        return '<span class="taobao_credit" style="margin-top:8px;"><span class="rank_1" title="<{$shop.credit}>"></span></span>';
    }
}