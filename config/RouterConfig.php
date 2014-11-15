<?php
/**
 * 路由配置
 */
class RouterConfig {

	public function __construct()
	{
		return;
	}

	public static $config = array(


	);

	/**
	 * [$defaultRouter 默认路由配置]
	 * @var array
	 */
	public static $defaultRouter = array(
		ASSASSIN_APP_DEFAULT => array(
			'default_controller'	=>	'default',
			'default_action'		=>	array(
					'default'	=>	'index',		// DefaultController 的默认 action 为 view	
					
				)
		),
	);




}