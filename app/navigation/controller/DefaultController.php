<?php
/**
 * All rights reserved.
 * 导航基础类
 * @author          libo <191358832@qq.com>
 * @time            2014/11/6 10:49:19
 * @version         1.0.0
 */

class DefaultController extends BaseController {
	
	public function __construct($controller, $action)
	{
		self::$controller = $controller;
		self::$action = $action;
		session_start();
	}
	public function index()
	{
		$auth_code = isset($_GET['u']) && !empty($_GET['u']) ? trim($_GET['u']) : '';

		$username = '';

		if(! empty($_SESSION['uid']) || ! empty($auth_code))
		{
			$cate_db = new CategoryModelDB();
			$url_db = new UrlModelDB();

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
		
			foreach ($cate_list as $key => &$value) {
				$url = $url_db->find( array('cate_id'=>$value['_id']));
				$value['urls'] = $url;
			}

			$username = isset($_SESSION['username']) && ! empty($_SESSION['username']) ? $_SESSION['username'] : '';
			$this->setView('url_list',$cate_list);
		}


		$rw_db = new RecommendWebModelDB();
		$rw_list = $rw_db->find();
		$this->setView('rw_list',$rw_list);
		$this->setView('username',$username);
		$this->display("index/index.html");
		
	}

	public function add()
	{
		$msg = array('code' => 0, 'msg'=>'');

		$user_id = trim($_POST['uid']) !='' ? trim($_POST['uid']) : '';
		$cate_name = isset($_POST['cate_name']) && (trim($_POST['cate_name']) == '') ? '未分类' : trim($_POST['cate_name']);
		$page_name = trim($_POST['web_name']);
		$url = trim($_POST['web_url']);
		$icon = isset($_POST['web_icon_url']) && (trim($_POST['web_icon_url']) != '') ? trim($_POST['web_icon_url']) : '';

		$is_cate_id = TRUE;

		// 区分是分类id，还是分类名称
		if (! preg_match('/[\d|a-z]{24}/', $cate_name)) 
		{
			$is_cate_id = FALSE;
		}

		// 判断URL是否有添加 http:// 没有添加，自动添加
		if (! preg_match('/^http[s?]:\/\//', $url)) 
		{
			$path = parse_url($url);
			$url = 'http://'.$path['path'];
			if(isset($path['query']) && ! empty($path['query']))
			{
				$url .= '?'.$path['query'];
			}
			
		}

		if ($user_id != $_SESSION['uid']) {
			$msg = array('code' => -1, 'msg'=>'系统错误，请重新登录');
			echo json_encode($msg);exit;
		}

		if (empty($page_name) || empty($url)) {
			$msg = array('code' => -2, 'msg'=>'网站名称和网址不能为空');
			echo json_encode($msg);exit;
		}

		$cate_db = new CategoryModelDB();
		$url_db = new UrlModelDB();
		$url_info = $url_db->findOne(array('url'=>$url,'user_id'=>$_SESSION['uid']), array('_id'));

		if (!empty($url_info)) {
			$msg = array('code' => -3, 'msg'=>'该网址已经存在您的导航中');
			echo json_encode($msg);exit;
		}

		$cate_info = '';
		// 查询分类是否存在，存在返回分类ID
		if ($is_cate_id) 
		{
			$cate_info = $cate_db->findOne(array('user_id'=>$user_id, '_id'=> new MongoId($cate_name)), array('_id'));
		}
		else
		{
			$cate_info = $cate_db->findOne(array('user_id'=>$user_id, 'cate_name'=>$cate_name), array('_id'));
		}
				
		if (empty($cate_info)) 
		{
			$cate_info = array('user_id'=>$user_id, 'cate_name'=> $cate_name, 'order_by'=> 100, 'add_time'=> date('Y-m-d H:i:s',time()));
			$cate_db->insert($cate_info);
		}
		$cate_info = BaseModelCommon::filterMongoData($cate_info);

		$url_info = array('user_id'=>$user_id, 'cate_id'=>$cate_info['_id'], 'page_name'=> $page_name, 'url'=>$url, 'icon'=>$icon, 'order_by'=> 100, 'add_time'=>date('Y-m-d H:i:s',time()));

		$status = $url_db->insert($url_info);

		if (isset($status['ok']) && $status['ok'] > 0) {
			$msg = array('code' => 1, 'msg'=>'添加成功');
			echo json_encode($msg);exit;
		}
	}

	/**
	 * [userLogin 用户登录]
	 * @return [type] [description]
	 */
	public function userLogin()
	{
		
		$msg = array('code'=>0, 'msg'=>'');
		$username = trim($_POST['username']);
		$password = trim($_POST['password']);

		if (empty($username) || empty($password)) {
			$msg = array('code'=>-1, 'msg'=>'请正确填写信息');
			echo json_encode($msg);exit;
		}

		$user_db = new UsersModelDB();
	
		$user_info = $user_db->findOne(array('username'=>$username,'password'=>md5($password)));

		$user_info = BaseModelCommon::filterMongoData($user_info);

		if (empty($user_info)) {
			$msg = array('code'=>-2, 'msg'=>'该用户信息不存在,请重新登录或注册');
			echo json_encode($msg);exit;
		}
		$msg = array('code'=>1, 'msg'=>$user_info);
		$_SESSION['uid'] 	  = $user_info['_id'];
		$_SESSION['username'] = $user_info['username'];
		echo json_encode($msg);exit;
	}

	/**
	 * [userReg 用户注册]
	 * @return [type] [description]
	 */
	public function userReg()
	{
		$msg = array('code'=>0, 'msg'=>'');

		$username = trim($_POST['username']);
		$password = trim($_POST['password']);
		$auth_code = trim($_POST['auth_code']);

		if (empty($username) || empty($password)) {
			$msg['code'] = -1;
			$msg['msg']  = '请正确填写信息';
			echo json_encode($msg);exit;
		}

		$user_db = new UsersModelDB();

		$user_info = $user_db->findOne(array('username'=>$username));

		if (!empty($user_info)) {
			$msg['code'] = -2;
			$msg['msg']  = '用户名已经存在';
			echo json_encode($msg);exit;
		}

		$user_arr = array('username'=>$username, 'password'=>md5($password),'add_time'=>date('Y-m-d H:i:s', time()), 'last_login_time'=>0);

		$status = $user_db->insert($user_arr);
		if ($status) {
			$msg['code'] = 1;
			$msg['msg']  = '注册成功，请登录';
			echo json_encode($msg);exit;
		}
	}

	/**
	 * [addRecommend 添加推荐网站]
	 */
	function addRecommend()
	{
		$msg = array('code' => 0, 'msg'=>'');

		$user_id = trim($_POST['uid']) !='' ? trim($_POST['uid']) : '';
		$cate_name = isset($_POST['cate_name']) && (trim($_POST['cate_name']) == '') ? '未分类' : trim($_POST['cate_name']);
		$page_name = trim($_POST['web_name']);
		$url = trim($_POST['web_url']);
		$icon = isset($_POST['web_icon_url']) && (trim($_POST['web_icon_url']) != '') ? trim($_POST['web_icon_url']) : '';

		if ($user_id != $_SESSION['uid']) {
			$msg = array('code' => -1, 'msg'=>'系统错误，请重新登录');
			echo json_encode($msg);exit;
		}

		if (empty($page_name) || empty($url)) {
			$msg = array('code' => -2, 'msg'=>'网站名称和网址不能为空');
			echo json_encode($msg);exit;
		}

		$rw_db = new RecommendWebModelDB();
		$url_info = $rw_db->findOne(array('url'=>$url,), array('_id'));

		if (!empty($url_info)) {
			$msg = array('code' => -3, 'msg'=>'该网址已经存在您的导航中');
			echo json_encode($msg);exit;
		}

		$url_info = array('user_id'=>$user_id, 'page_name'=> $page_name, 'url'=>$url, 'icon'=>$icon, 'order_by'=> 100, 'add_time'=>date('Y-m-d H:i:s',time()));

		$status = $rw_db->insert($url_info);

		if (isset($status['ok']) && $status['ok'] > 0) {
			$msg = array('code' => 1, 'msg'=>'添加成功');
			echo json_encode($msg);exit;
		}
	}

	/**
	* 获得分类信息
	*/
	function getCategory()
	{
		$auth_code = isset($_GET['u']) && !empty($_GET['u']) ? trim($_GET['u']) : '';
		$cate_db = new CategoryModelDB();
		$user_id = empty($_SESSION['uid']) ? $auth_code : $_SESSION['uid'];
		$cate_list = $cate_db->find(array('user_id' => $user_id));
		
		$cate_list = BaseModelCommon::filterMongoData($cate_list);
		echo json_encode($cate_list);exit;
	}

	function loginOut()
	{
		$_SESSION['uid'] = '';
		$_SESSION['username'] = '';
		echo json_encode('1');exit;
	}
}