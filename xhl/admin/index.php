<?php

define("__APP__", "admin");
define("__APP_DIR", dirname(__FILE__) . DIRECTORY_SEPARATOR);
define("__CORE_DIR", dirname(__APP_DIR) . DIRECTORY_SEPARATOR);
define("IN_ADMIN", true);

require(__CORE_DIR."xhlctl/xing.php");
class Index extends xing
{
	protected $_default_request = array("ctl" => "index", "act" => "index", "type" => "html", "args" => NULL);

	protected function _init()
	{
		$guest_allow = array("index:login", "index:verify", "index:loginout");
		if (($OATOKEN = trim($_POST["OATOKEN"])) && ($a = $this->load_model("secure/crypt")->hexarr($OATOKEN)) && $a["ATOKEN"] && $a["AGENT"]) {
			$GLOBALS["_SERVER"]["HTTP_USER_AGENT"] = $a["AGENT"];
			$GLOBALS["_COOKIE"][__CFG::C_PREFIX . "ATOKEN"] = $a["ATOKEN"];
		}

		parent::_init();
		require (__APP_DIR . "controller.php");
		$act = $this->request["ctl"] . ":" . $this->request["act"];
		$this->admin = K::M("admin/auth");

		// if (!$this->admin->token()) {
		// 	if (!in_array($act, $guest_allow)) {
		// 		header("Location:?index-login");
		// 		exit();
		// 	}
		// }

		$this->admin_id = $this->admin->admin_id;
		$this->admin_name = $this->admin->admin_name;
	}

	protected function _run($uri = NULL)
	{
		$objctl = $this->_frontend($this->request["ctl"], $this->request["act"]);

		if (!is_object($objctl)) {
			$this->error(404);
		}

		if ($objctl->__call) {
			array_unshift($this->request["args"], $this->request["act"]);
			$this->request["act"] = $objctl->__call;
		}

		if (!$this->call($objctl, $this->request["act"], $this->request["args"])) {
			trigger_error("not find " . $this->request[ctl] . ":{$this->request[act]}");
			$this->error(404);
		}

		$this->err->response();
	}

	protected function _route($uri = NULL)
	{
		$request = parent::_route($uri);
		$this->request = &$request;
		return $request;
	}

	protected function _frontend($ctl, $act = "index")
	{
		if (!$clsName = Import::C(__APP_APP . ":" . $ctl)) {
			$this->error("ctl:" . $ctl . " not find!!!");
		}

		$object = new $clsName($this);
		return $object;
	}

	public function mklink($ctl, $act = "index", $args = array(), $extends = ".html", $gets = array())
	{
		if ($args && is_array($args)) {
			$args = "-" . implode("-", $args);
		}
		else {
			$args = "";
		}

		return __APP_URL . "/?$ctl-$act$args$extends";
	}
}


//new Index();
?>
