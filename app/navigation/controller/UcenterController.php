<?php
/**
 * All rights reserved.
 * 用户中心
 * @author          libo <191358832@qq.com>
 * @time            2015/1/27 13:23:38
 * @version         1.0.0
 */

class UcenterController extends BaseController {
	
	public function __construct($controller, $action)
	{
		self::$controller = $controller;
		self::$action = $action;
		session_start();

		if (empty($_SESSION['uid'])) 
		{
			echo "<script>alert('请登录');</script>";
			header('Refresh:0;url=http://cikewang.com');
			exit;
		}
	}

	public function index()
	{
		$cate_list = array();
		$cate_id = isset($_GET['cate_id']) && !empty($_GET['cate_id']) ? trim($_GET['cate_id']) : '';

		if(! empty($_SESSION['uid']))
		{
			$cate_db = new CategoryModelDB();
			
			$user_id = empty($_SESSION['uid']) ? $auth_code : $_SESSION['uid'];

			$cate_list = $cate_db->find(array('user_id' => $user_id));
			
			if(empty($cate_list))
			{
				$user_db = new UsersModelDB();
				$user_info = $user_db->findOne(array('username'=>$user_id));
				$user_info = BaseModelCommon::filterMongoData($user_info);
				$cate_list = $cate_db->find(array('user_id' => $user_info['_id']));
			}

			$cate_list = BaseModelCommon::filterMongoData($cate_list);
		}

	

		$username = isset($_SESSION['username']) && ! empty($_SESSION['username']) ? $_SESSION['username'] : '';

		$this->setView('cate_list', $cate_list);
		$this->setView('username',$username);
		$this->display('ucenter/index.html');
	}


	public function delete()
	{
		$cate_id = isset($_POST['cate_id']) && !empty($_POST['cate_id']) ? trim($_POST['cate_id']) : '';
		
		$cate_db = new CategoryModelDB();
		$id = new MongoId($cate_id);
		$status = $cate_db->remove(array('_id'=>$id));
		
		$url_db = new UrlModelDB();
		$status = $url_db->remove(array('cate_id'=>$cate_id));

		// header('Refresh:0;url=http://cikewang.com/index.php?c=ucenter');

		echo json_encode($cate_id);exit;
	}

	public function mCate()
	{
		$cate_id = isset($_POST['category_id']) && !empty($_POST['category_id']) ? trim($_POST['category_id']) : '';
		$cate_name = isset($_POST['category_name']) && !empty($_POST['category_name']) ? trim($_POST['category_name']) : '';

		$cate_db = new CategoryModelDB();
		$id = new MongoId($cate_id);

		$update_data = array('$set'=>array('cate_name'=>$cate_name));
		$d = $cate_db->update($update_data,  array('_id'=>$id));

		header('Refresh:0;url=http://cikewang.com/index.php?c=ucenter');
	}

	public function order()
	{
		$order = isset($_POST['order']) && !empty($_POST['order']) ? $_POST['order'] : '';

		if(count($order))
		{	
			$cate_db = new CategoryModelDB();
		
			foreach ($order as $id => $order_by) 
			{
				$id = new MongoId($id);
				$update_data = array('$set'=>array('order_by'=>$order_by));
				$cate_db->update($update_data,  array('_id'=>$id));
			}
		}
		header('Refresh:0;url=http://cikewang.com/index.php?c=ucenter');
	}

	public function orderUrl()
	{
		$order = isset($_POST['order']) && !empty($_POST['order']) ? $_POST['order'] : '';
		$cate_id = isset($_POST['cate_id']) && !empty($_POST['cate_id']) ? trim($_POST['cate_id']) : '';

		if(count($order))
		{	
			$url_db = new UrlModelDB();
		
			foreach ($order as $id => $order_by) 
			{
				$id = new MongoId($id);
				$update_data = array('$set'=>array('order_by'=>$order_by));
				$url_db->update($update_data,  array('_id'=>$id));
			}
		}

		header('Refresh:0;url=http://cikewang.com/index.php?c=ucenter&a=url&cate_id='.$cate_id);
	}
	
	public function url()
	{
		$cate_id = isset($_GET['cate_id']) && !empty($_GET['cate_id']) ? trim($_GET['cate_id']) : '';
		$url_db = new UrlModelDB();

		$url = $url_db->find( array('cate_id'=>$cate_id));
		$url_list = BaseModelCommon::filterMongoData($url);
		$cate_list = array();

		if(! empty($_SESSION['uid']))
		{
			$cate_db = new CategoryModelDB();
			
			$user_id = empty($_SESSION['uid']) ? $auth_code : $_SESSION['uid'];

			$cate_list = $cate_db->find(array('user_id' => $user_id));
			$cate_list = BaseModelCommon::filterMongoData($cate_list);
		}

		$this->setView('cate_id', $cate_id);
		$this->setView('cate_list', $cate_list);
		$this->setView('url_list',$url_list);
		$this->display('ucenter/url.html');
	}

	public function changeCate()
	{
		$id = isset($_GET['id']) && !empty($_GET['id']) ? trim($_GET['id']) : '';
		$to_id = isset($_GET['to_id']) && !empty($_GET['to_id']) ? trim($_GET['to_id']) : '';

		$url_db = new UrlModelDB();
		$id = new MongoId($id);
		$update_data = array('$set'=>array('cate_id'=>$to_id));
		$url_db->update($update_data,  array('_id'=>$id));

		header('Refresh:0;url=http://cikewang.com/index.php?c=ucenter');
	}


	public function deleteUrl()
	{
		$id = isset($_POST['id']) && !empty($_POST['id']) ? trim($_POST['id']) : '';
		$url_db = new UrlModelDB();
		$_id = new MongoId($id);
		$status = $url_db->remove(array('_id'=>$_id));
		echo json_encode($id);exit;
	}

	public function mUrl()
	{
		$url_id = isset($_POST['url_id']) && !empty($_POST['url_id']) ? trim($_POST['url_id']) : '';
		$page_name = isset($_POST['page_name']) && !empty($_POST['page_name']) ? trim($_POST['page_name']) : '';
		$url = isset($_POST['url']) && !empty($_POST['url']) ? trim($_POST['url']) : '';
		$cate_id = isset($_POST['cate_id']) && !empty($_POST['cate_id']) ? trim($_POST['cate_id']) : '';


		$url_db = new UrlModelDB();
		$id = new MongoId($url_id);

		$update_data = array('$set'=>array('page_name'=>$page_name, 'url'=>$url));
		$d = $url_db->update($update_data,  array('_id'=>$id));

		header('Refresh:0;url=http://cikewang.com/index.php?c=ucenter&a=url&cate_id='.$cate_id);
	}
}