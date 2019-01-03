<?php

if (!defined("__CORE_DIR")) {
	exit("Access Denied");
}
class CTL extends Factory
{
	public $MOD = array();

	public function __construct(&$system)
	{
		parent::__construct($system);
		$this->cookie = $system->cookie;
		$this->InitializeApp();
		register_shutdown_function(array($this, "shutdown"));
	}

	protected function InitializeApp()
	{
		$this->err->template("admin:page/notice.html");
		$this->system->objctl = &$this;
		$this->admin = &$this->system->admin;

		// if (!$this->check_priv()) {
		// 	$this->err->add("您没有权限操作", -1);		// 在这，html页面调 控制器方法提示  没有权限操作，注释掉就可以
		// 	$this->err->response();
		// }
	}

	protected function _init_pagedata()
	{
		parent::_init_pagedata();
		$this->pagedata["MOD"] = $this->MOD;
		$this->pagedata["ADMIN"] = $this->admin->admin;
		$this->pagedata["OATOKEN"] = $this->system->OATOKEN;
		$this->pagedata["pager"]["url"] = __APP_URL;
		$this->pagedata["pager"]["res"] = __CFG::RES_URL;
		$this->pagedata["request"] = $this->request;
		$output = K::M("system/frontend");
		$output->setCompileDir(__CFG::DIR . "data/tpladmin");
	}

	protected function check_priv($ctl = NULL, $act = NULL)
	{
		$ctl = ($ctl ? $ctl : $this->request["ctl"]);
		$act = ($act ? $act : $this->request["act"]);

		if ($ctl == "index") {
			$this->MOD = array("mod_id" => 0, "module" => "module", "ctl" => $ctl, "act" => "act", "title" => "通用控制器");
			return true;
		}

		if (($this->MOD = K::M("module/view")->ctlmap($ctl, $act)) && $this->admin->check_priv($this->MOD["mod_id"])) {
			return true;
		}

		return false;
	}

	protected function logs($title = "", $data = array())
	{
		return false;
	}

	public function shutdown()
	{
		return false;
	}
	
	
	public function check_city($city_id=0)
	{
//		return false;
		return true;
	}
	

}


?>
