<?php
/**
 * Copy Right jisunet.com
 * 人要活得优雅,代码更需要优雅
 * $Id: themes.php 2034 2015-11-07 03:08:33Z xinghuali $
 */

if(!defined('__CORE_DIR')){
    exit("Access Denied");
}

return array (
  'theme_id' => 
  array (
    'field' => 'theme_id',
    'label' => 'ID',
    'pk' => true,
    'add' => false,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'theme' => 
  array (
    'field' => 'theme',
    'label' => '模板',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'title' => 
  array (
    'field' => 'title',
    'label' => '名称',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'config' => 
  array (
    'field' => 'config',
    'label' => '配置',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => false,
    'type' => 'text',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'default' => 
  array (
    'field' => 'default',
    'label' => '默认',
    'pk' => false,
    'add' => true,
    'edit' => true,
    'html' => false,
    'empty' => true,
    'show' => false,
    'list' => true,
    'type' => 'boolean',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
  'dateline' => 
  array (
    'field' => 'dateline',
    'label' => '安装时间',
    'pk' => false,
    'add' => false,
    'edit' => true,
    'html' => false,
    'empty' => false,
    'show' => false,
    'list' => true,
    'type' => 'dateline',
    'comment' => '',
    'default' => '',
    'SO' => false,
  ),
);