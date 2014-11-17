<?php
/**
 * All rights reserved.
 * 文档
 * @author          libo <191358832@qq.com>
 * @time            2014/11/17 12:07:06
 * @version         1.0.0
 */

class DocumentController extends BaseController {
	
	public function __construct($controller, $action)
	{
		self::$controller = $controller;
		self::$action = $action;
		session_start();
	}

	/**
	 * [about 关于本站]
	 * @return [type] [description]
	 */
	public function about()
	{
		$this->setView('username',$_SESSION['username']);
		$this->display("document/about.html");
	}

	/**
	 * [faq 常见问题]
	 * @return [type] [description]
	 */
	public function faq()
	{
		$this->display("document/faq.html");
	}

	/**
	 * [feedback 意见吐槽]
	 * @return [type] [description]
	 */
	public function feedback()
	{
		$this->display("document/feedback.html");
	}

	/**
	 * [contact 联系我们]
	 * @return [type] [description]
	 */
	public function contact()
	{
		$this->display("document/contact.html");
	}

}