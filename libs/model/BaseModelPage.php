<?php
class BaseModelPage	{


	private $prePage;			//	上一页
	private $nextPage;			//	下一页
	private $firstPage;			//	第一页
	private $lastPage;			//	最后一页
	private $pageStr;			//	翻页导航	
	private $totalNum = 0;		//	总个数
	private $pageSize = 10;		//	每页显示几个
	private $paramStr = '';		//	页面参数
	private $totalPage = 0;		//	总页数
	private $page = 0;			//	当前页数
	private $pName = 'page';	//	翻页参数名
	private $limit = '';		//	SQL -limit 
	private $style = 0;			//	翻页样式
	private $params = array();	//	参数数组

	public function __construct($totalNum, $pageSize, $params = array(), $pName = 'page')
	{
		$pageSize = intval($pageSize);
		if (empty($pageSize)) {
			return FALSE;
		}

		empty($params) && $params = $_GET;
		$this->totalNum = max(intval($totalNum), 0);
		$this->pageSize = max($pageSize, (-1*$pageSize));
		$this->params 	= $params;
		empty($pName)	|| $this->pName = $pName;
		$this->page = isset($this->params[$this->pName]) ? max($this->params[$this->pName], 1) : 1;
		$this->totalPage = ceil($this->totalNum/$this->pageSize);
		$this->prePage = max($this->page - 1 , 1);
		$this->nextPage = min($this->page + 1, $this->totalPage);
		$this->lastPage = $this->totalPage;
		$this->page || $this->page = 1;
		$this->limit = " LIMIT " . ($this->page - 1) * $this->pageSize . ',' . $this->pageSize;

		unset($this->params[$this->pName]);

		$this->paramStr = '?' . http_build_query($this->params);

		if (strpos($this->paramStr, '?') !== FALSE) {
			$this->paramStr .= '&';
		}
		else
		{
			$this->paramStr .= '?';
		}

	}

	/**
	 * [getLimit 获得LIMIT语句]
	 * @return [type] [description]
	 */
	public function getLimit()
	{
		return $this->limit;
	}

	/**
	 * [setStyle 设置分页样式]
	 * @param [type] $style [description]
	 */
	public function setStyle($style)
	{
		$this->style = $style;
	}

	public function getPageStr()
	{
		$tpl = new BaseView();
		$assign = array(
			'totalPage'	=> 	$this->totalPage,
			'pageSize'	=>	$this->pageSize,
			'prePage'	=>	$this->prePage,
			'nextPage'	=>	$this->nextPage,
			'firstPage'	=>	$this->firstPage,
			'lastPage'	=>	$this->lastPage,
			'totalPage'	=>	$this->totalPage,
			'totalNum'	=>	$this->totalNum,
			'pName'		=>	$this->pName,
			'page'		=>	$this->page,
			'paramStr'	=>	$this->paramStr
			);
		$format = isset($_REQUEST['format']) ? $_REQUEST['format'] : '';
		switch ($format) {
			case 'xml':
			case 'json':
				return (!$this->totalPage) ?  array() : $assign;
				break;
			default:
				foreach ($assign as $key => $value) {
					$tpl->assign($key, $value);
				}
				$style = intval($this->style);
				return (!$this->totalPage) ? '' : $tpl->display('page/style_'. $style .'.html');
		}
	}
}