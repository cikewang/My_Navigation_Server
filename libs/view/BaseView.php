<?php
/**
 *  模板类
 * @author libo
 *
 */
class BaseView {
	
	/**
	 * 模板值
	 * @var unknown
	 */
	protected $view = array();
	
	/**
	 * 模板文件
	 * @var unknown
	 */
	protected $tplPath = NULL;
	
	public function __construct()
	{
		$this->tplPath = ASSASSIN_PATH_APP_TPL;
	}
	
	public function setView($key, $value = '', $allowXss = FALSE)
	{
		$this->assign($key, $value, $allowXss);
	}

	/**
	 * 设置模板变量，XSS过滤
	 * @param unknown $key
	 * @param unknown $value
	 * @param string $allowXss
	 */
	public function assign($key, $value = '', $allowXss = FALSE)
	{
		if (is_array($key))
		{
			if ($allowXss === FALSE)
			{
				$key = BaseModelCommon::filterArr($key);
			}
			$this->view = array_merge($this->view, $key);
		}
		else
		{
			if ($allowXss === FALSE)
			{
				$value = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
			}
			$this->view[$key] = $value;
		}
	}
	
	public function display($tplFile)
	{
		echo $this->fetch($tplFile);
	}
	/**
	 * 引用模板文件并输出
	 * @param unknown $tplFile	模板文件名
	 * @return string
	 */
	private function fetch($tplFile)
	{

		$tplFile = $this->tplPath . $tplFile;
		if (!file_exists($tplFile))
		{
			exit($tplFile.'模板文件不存在');
		}
		extract($this->view);
		ob_start();
		include($tplFile);
		$out = ob_get_contents();
		ob_end_clean();
		return $out;
	}
}