<?php /* Smarty version Smarty-3.1.8, created on 2016-12-23 17:29:22
         compiled from "admin:page/login.html" */ ?>
<?php /*%%SmartyHeaderCode:5530585cee72b52d74-58665474%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'c66b93d857459543b1d37f71a25f1f726163f51a' => 
    array (
      0 => 'admin:page/login.html',
      1 => 1482483464,
      2 => 'admin',
    ),
  ),
  'nocache_hash' => '5530585cee72b52d74-58665474',
  'function' => 
  array (
  ),
  'variables' => 
  array (
    'pager' => 0,
  ),
  'has_nocache_code' => false,
  'version' => 'Smarty-3.1.8',
  'unifunc' => 'content_585cee72ba0f88_98372438',
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_585cee72ba0f88_98372438')) {function content_585cee72ba0f88_98372438($_smarty_tpl) {?><!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="Content-Language" content="zh-CN" />
    <title>一路展管理系统</title>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/script/cloud.js"></script>
    <link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/style/common.css" type="text/css" charset="utf-8" />
    <link href="style/style.css"rel="stylesheet" type="text/css" />
    <link href="/static/style/kt.widget.css" rel="stylesheet" type="text/css" />
    <link href="/static/ui/j.ui.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/script/kt.js"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/script/kt_002.js"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/script/widget.js"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/script/j.js"></script>
    <script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/script/kt.003.js"></script>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
    <script type="text/javascript">if (window.parent != window){window.top.location.href = location.href;}</script>
</head>
<body style=" background:#1c77ac url(<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/images/light.png) 445px 0px no-repeat; overflow: hidden;">
<div id="mainBody">
		<div id="cloud1" class="cloud" style="background-position: -367.6999999999956px 100px;"></div>
		<div id="cloud2" class="cloud" style="background-position: -387px 460px;"></div>
	</div>
<div class="loginbody">	
	<div class="login_top">
		<p class="float-l"><span class="login_top_ico"></span>一路展管理系统入口</p>
		<a href="/" class="float-r">返回首页</a>
	</div>
	<div class="login_logo"></div>
	<div class="login_box">
		<form action="?index-login" method="post">
			<ul>
				<li>
					<input name="admin_name" id="admin_name" type="text" class="loginuser" value="" placeholder="请输入您的用户名"/>
				</li>
				<li>
					<input  name="admin_pwd" type="password" class="loginpwd" value=""  placeholder="请输入您的密码"/>
				</li>
				<li>
					<input name="verify_code" type="text" class="logincheckcode" id="textfield3" value=""  maxlength="4"  placeholder="请输入验证码"/>
					<img src="?index-verify" alt="请输入验证码" width="100" height="30" onClick="this.src='?index-verify&_'+Math.random()" />
				</li>
				<li>
					<input type="submit" class="loginbtn" value="登录" />					
					<input type="checkbox" tabindex="5" id="chkAutoLogin" name="chkAutoLogin" onChange="javascript:f_AutoLogin_change();">
					一周内免登录</label>
				</li>
			</ul>
		</form>
		<div class="login_foot loginfot"><a href="/" target="_blank">@一路展 版权所有</a>
		</div>
	</div>
</div>
<script type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['pager']->value['url'];?>
/script/cloud.js"></script>
<script type="text/javascript">document.getElementById('admin_name').focus();</script>
</body>
</html>
<?php }} ?>