<?php
class BaseController {
	
	/**
	 * 模板变量
	 * @var unknown
	 */
	protected $view = array();
	/**
	 * 控制器
	 * @var unknown
	 */
	protected static $controller = NULL;
	/**
	 * 控制器方法
	 * @var unknown
	 */
	protected static $action = NULL;
	
	
	public function __construct($controller, $action)
	{
		self::$controller = $controller;
		self::$action = $action;
	}
	
	public function runCommand()
	{
		switch ($_SERVER['REQUEST_METHOD'])
		{
			case 'GET':
				break;
			case 'POST' :
				// 需要安全验证 referer
				
				break;
			case 'HEAD'	:
				break;
			default:
				exit('请求的方法不允许');
		}
		
		$action = BaseModelCommon::getFormatName(self::$action);
		$controllerName = BaseModelCommon::getFormatName(self::$controller, 'class');
		
		if (in_array($action, array('runCommand'), TRUE))
		{
			BaseModelMessage::showError($controllerName.' 类中的 '.$action.' 为系统预定义方法，这里无法使用。');
		}
		if (method_exists($this, $action)) 
		{
			call_user_func_array(array(&$this, $action), array());
		}
		else 
		{
			BaseModelMessage::showError($controllerName.' 类中的 '.$action.' 方法不存在');
		}
	}
	
	/**
	 * 设置模板变量
	 * @param unknown $key		模板变量名
	 * @param unknown $value	模板变量值
	 */
	protected function setView($key, $value)
	{
		$this->view[$key] = $value;
	}
	
	/**
	 * 显示输出
	 * @param unknown $tplFile
	 */
	protected function display($tplFile)
	{
		$this->fetch($tplFile);
	}
	
	/**
	 * 使用模板显示输出
	 * @param unknown $tplFile
	 */
	private function fetch($tplFile)
	{
		$tpl = new BaseView();

		$tpl->setView($this->view);
		$tpl->display($tplFile);
	}
	
	
	
}