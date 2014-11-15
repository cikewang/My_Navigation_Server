<?php
/**
 * 数据库操作基础类
 * @author libo 
 * @date(2014-10-15)	   
 */
class BaseModelDB {
	
	/**
	 * 指定数据库配置
	 * @var array
	 */
	protected $DBConfig = array();

	/**
	 * 数据库配置名
	 * @var [type]
	 */
	protected $DBName;

	/**
	 * 数据库连接资源描述符
	 * @var [type]
	 */
	protected $link = NULL;

	/**
	 * 表名
	 * @var [type]
	 */
	protected $tableName;

	/**
	 * [最后一次执行的SQL语句]
	 * @var [string]
	 */
	protected $sql;

	/**
	 * [$countNum 数据库返回数据集行数]
	 * @var [number]
	 */
	protected $countNum;

	/**
	 * [$pageModel 分页实例]
	 * @var [object]
	 */
	protected $pageModel;

	/**
	 * [$pName 分页参数名]
	 * @var string
	 */
	protected $pName = 'page';
	/**
	 * [__construct description]
	 * @param string $DBName   [数据库名称，在config/DBConfig.php中配置]
	 * @param array  $DBConfig [数据库配置，可单独修改主库和从库 host、username、password、database、charset、port]
	 */
	public function __construct($DBName = '', $DBConfig = array())
	{
		$this->DBName = $DBName;
		$this->DBConfig = $DBConfig;
	}
	
	/**
	 * 设置表名
	 * @param [string] $tableName [表名]
	 */
	public function setTableName($tableName)
    {
    	$this->tableName  = $tableName;
    }

    /**
     * 获取表名
     * @return [string] [表名]
     */
    public function getTableName()
    {
    	return $this->tableName;
    }

    /**
     * [getCountNum 获取返回结果总数]
     * @return [type] [description]
     */
    public function getCountNum()
    {
    	return $this->countNum;
    } 

    /**
     * [setPName 设置分页参数名]
     * @param string $pName [description]
     */
    public function setPName($pName = 'page')
    {
    	$this->pName = $pName;
    }

    /**
     * [getPageStr 获取分页器HTML片段]
     * @return [type] [description]
     */
    public function getPageStr()
    {
    	return is_object($this->pageModel) ? $this->pageModel->getPageStr() : '';
    }

    /**
     * [setPageStyle  设置分页样式]
     * @param [type] $pageStyle [description]
     */
    public function setPageStyle($pageStyle)
    {
        is_object($this->pageModel) ? $this->pageModel->setStyle($pageStyle) : '' ;
    }

    /**
     * [getFirst  执行SQL 返回一行数据中的第一个数值]
     * @param  [type] $sql             [执行的SQL语句]
     * @param  string $data            [执行的语句中以'?'替代的变量值]
     * @param  string $master_or_slave [master：主库 | slave：从库]
     * @return [type]                  [description]
     */
    public function getFirst($sql, $data = '', $master_or_slave = 'slave')
    {
    	$query = $this->_sendQuery($sql, $data, $master_or_slave);
    	if (!is_object($query)) {
    		exit('数据库返回非资源');
    	}
    	$row = $query->fetch_row();
    	$row[0]	= is_null($row[0]) ? '' : $row[0];
    	return $row[0];
    }

    /**
     * [getRow 执行SQL，返回一行记录]
     * @param  [type] $sql             [需要执行的SQL语句]
     * @param  string $data            [查询语句中以'?' 代替的值，默认为空]
     * @param  string $master_or_slave [master:主库 | slave：从库]
     * @return [type]                  [description]
     */
    public function getRow($sql, $data = '', $master_or_slave = 'slave')
    {
    	$query = $this->_sendQuery($sql, $data, $master_or_slave);
    	if (!is_object($query)) {
    		exit('数据库返回非资源');
    	}
    	$row = $query->fetch_assoc();
    	$row = is_null($row) ? array() : $row;
    	return $row;
    }

	/**
	 * [getData 执行SQL语句]
	 * @param  [type] $sql             [需要查询的SQL语句]
	 * @param  string $data            [查询语句中以'?'替代的变量值]
	 * @param  string $pageSize        [每页结果数]
	 * @param  string $master_or_slave [master：主库 | slave：从库]
	 * @return [type]                  [description]
	 */
    public function getData($sql, $data = '', $pageSize = '', $master_or_slave = 'slave')
    {
    	if (!is_array($data) && !is_numeric($pageSize)) {
            $pageSize = $data;
            $data = '';
    	}

    	if (is_numeric($pageSize) && $pageSize > 0) {
    		$count_sql = "SELECT count(*) AS num " . substr($sql, stripos($sql, "from"));
    		$count_sql = preg_replace("/\s*ORDER\s*BY.*/i", "", $count_sql);

    		$query = $this->_sendQuery($count_sql, $data, $master_or_slave);
    		
    		if (!is_object($query)) {
    			exit('数据库返回非资源');
    		}

    		if ($query->num_rows == 1) {
    			$row = $query->fetch_row();
    			$this->countNum = $row[0];
    		}
    		else
    		{
    			$this->countNum = $query->num_rows();
    		}

    		$this->pageModel = new BaseModelPage($this->countNum, $pageSize, array(), $this->pName);
    		$sql .= $this->pageModel->getLimit();
    	}

    	$query = $this->_sendQuery($sql, $data, $master_or_slave);

    	if (!is_object($query)) {
    		exit('数据库返回非资源');
    	}

    	$arr = array();
    	while ($row = $query->fetch_assoc()) {
    		empty($row)	|| $arr[] = $row;
    	}
    	return $arr;
    }

    /**
     * [getAll 执行SQL，返回所有数据]
     * @param  [type] $sql             [需要执行的SQL语句]
     * @param  [type] $data            [查询语句中以'?'替代的变量值]
     * @param  [type] $master_or_slave [master：主库 | slave：从库]
     * @return [type]                  [description]
     */
    public function getAll($sql, $data = '', $master_or_slave = 'slave')
    {
    	$query = $this->_sendQuery($sql, $data, $master_or_slave);
    	if (!is_object($query)) {
    		exit('数据库返回非资源');
    	}
    	$arr = array();
    	while ($row = $query->fetch_assoc()) {
    		empty($row) || $arr[] = $row;
    	}
    	return $arr;
    }

    /**
     * 连接数据库
     * @param  string  $master_or_slave [master：主库 | slave：从库]
     * @param  boolean $reConnect       [description]
     * @return [type]                   [description]
     */
    public function checkLink($master_or_slave = 'slave', $reConnect = FALSE)
    {
    	$this->link = BaseModelDBConnect::connectDB($this->DBName, $master_or_slave, $this->DBConfig, 'mysql', $reConnect);
    }

    /**
     * 过滤数据
     * @param  [type] $string          [传入的数据]
     * @param  string $master_or_slave [主从库]
     * @return [type]                  [description]
     */
    protected function escape_string($string, $master_or_slave = 'slave')
    {
    	$this->checkLink($master_or_slave);
    	if (!$this->link) {
    		exit('数据库连接失败');
    	}
    	return $this->link->real_escape_string($string);
    }

    /**
     * [构造SQL语句]
     * @param [type] $sql             [执行的SQL语句]
     * @param string $data            [传入的数据]
     * @param string $master_or_slave [master：主库 |　slave：从库]
     */
    protected function setSql($sql, $data = '', $master_or_slave = 'slave')
    {
    	$this->sql = $sqlShow = '';
    	if (strpos($sql , '?') && is_array($data) && count($data) > 0) {
    		if (substr_count($sql , '?') != count($data)) {
    			exit('传参不符合构造规范，无法正确翻译SQL语句![sql] '.$sql.' [data] '.var_export($data, TRUE));
    		}
    		else
    		{
    			$sqlArr = explode('?', $sql);
    			$last	= array_pop($sqlArr);
    			foreach ($sqlArr as $k => $v) {
    				if (!empty($v) && isset($data[$k])) {
    					if (!is_array($data[$k])) 
                        {
    						$value = "'".$this->escape_string($data[$k], $master_or_slave)."'";
    					}
    					else
    					{
    						foreach ($data[$k] as $val) 
                            {
    							$valueArr[] = "'".$this->escape_string($val, $master_or_slave)."'";
    						}
    						$value = '('.implode(',', $valueArr).')';
    					}
    					$sqlShow .= $v . $value;
    				}
    				else
    				{
    					exit('传参不符合构造规范，无法正确翻译SQL语句![sql] '.$sql.' [data] '.var_export($data, TRUE));
    				}
    				
    			}
    			$sqlShow .= $last;
    		}
    	}
    	else
    	{
    		$sqlShow = $sql;
    	}
    	$this->sql = $sqlShow;
    }

    /**
     * [执行SQL语句]
     * @param  [type] $sql             [需要执行的SQL语句]
     * @param  [type] $data            [执行的语句中以'?'替代的变量值]
     * @param  [type] $master_or_slave [master：主库 | slave：从库]
     * @param  [like] result           [数据库资源]  
     * @return [type]                  [description]
     */
    private function _sendQuery($sql, $data, $master_or_slave ='slave', &$result = array())
    {
    	$this->checkLink($master_or_slave);
    	if (!$this->link) {
    		exit('数据库连接失败');
    	}

    	$this->setSql($sql, $data, $master_or_slave);

    	if (empty($this->sql)) {
    		exit('SQL 不能为空');
    	}

    	$query = $this->link->query($this->sql);
        if (strtoupper(substr(ltrim($this->sql), 0, 6)) !== 'SELECT') {
            $result['affected_num'] = $this->link->affected_rows;
        }

    	if ($this->link->errno !== 0) {
    		return $this->link->errno;
    	}

    	return $query;

    }

    /**
     * [insert 插入数据]
     * @param  [type] $insert_value [插入的数据 array('key1'=> $value1, 'key2'=>$value2)]
     * @param  string $affix        [description]
     * @param  array  $result       [description]
     * @param  string $sqlType      [description]
     * @return [type]               [TRUE: 插入成功 | FALSE :插入失败]
     */
    public function insert($insert_value, $affix = '', &$result = array(), $sqlType = 'INSERT')
    {
        $sqlType = strtoupper($sqlType) !== 'REPLACE' ? 'INSERT' : 'REPLACE';
        if (!is_array($insert_value) || empty($insert_value)) {
            exit('insert 中 insert_value 传参错误');
        }

        $inKeyArr = $inValArr = array();
        foreach ($insert_value as $key => $value) {
            $inKeyArr[] = ' `' . $key . '` ';
            $inValArr[] = ' ? ';
        }

        if (empty($inKeyArr)) {
            exit('insert_value 传参错误');
        }

        $sql = "{$sqlType} INTO " . $this->getTableName() . " (" . implode(',', $inKeyArr) . ") VALUES (" . implode(',', $inValArr) . ")";
        $query = $this->_sendQuery($sql, array_values($insert_value), 'master', $result);
        if (is_int($result['affected_num']) && $result['affected_num'] > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * [getInsertId 获得插入数据ID]
     * @return [type] [description]
     */
    public function getInsertId()
    {
        $sql = "SELECT last_insert_id()";
        return $this->getFirst($sql, '', 'master');
    }

    /**
     * [getLastSQL 获取最后执行的SQL语句]
     * @return [type] [description]
     */
    public function getLastSQL()
    {
        return $this->sql;
    }

    /**
     * [replace 替换数据]
     * @param  [type] $replace_value [description]
     * @param  string $affix         [description]
     * @param  array  $result        [description]
     * @return [type]                [description]
     */
    public function replace($replace_value, $affix = '', &$result = array())
    {
        return $this->insert($replace_value, $affix = '', $result, 'REPLACE');
    }

    /**
     * [update 更新数据]
     * @param  [type] $update_value [要更新的数据 array('key1'=>$value1, 'key2'=>$value2);]
     * @param  [type] $where        [更新的条件   array('key1'=>$value1) | 'id = 1']
     * @param  array  $result       [description]
     * @return [type]               [description]
     */
    public function update($update_value, $where, &$result = array())
    {
        if (!is_array($update_value)) 
        {
            exit('update中update_value传参错误');
        }

        $whereStr = '';
        $whereArr = array();
        if (is_string($where)) 
        {
            $tmp_where = strtolower($where);
            if (!strpos($tmp_where, '=') && !strpos($tmp_where, 'in') && !strpos($tmp_where, 'like')) 
            {
                exit('upate中where条件错误');
            }
            $whereStr = $where;
        } 
        elseif (is_array($where)) 
        {
            $tmp = array();
            foreach ($where as $key => $value) {
                if (is_array($value)) 
                {
                    $tmp[] = "`" . $key . "` in ?";
                }
                else
                {
                    $tmp[] = "`" . $key . "` = ? ";
                }
                $whereArr[] = $value;
            }
            $whereStr = implode(' AND ', $tmp);
        }
        else
        {
            exit('upate 中where条件错误');
        }

        $upArr = array();
        foreach ($update_value as $key => $value) {
            $upArr[] = ' `' . $key . '` = ?';
        }
        $sql = "UPDATE `" . $this->getTableName() . "` SET " . implode(',', $upArr) . " WHERE {$whereStr}";
        $query = $this->_sendQuery($sql , array_merge(array_values($update_value), $whereArr), 'master', $result);
        
        if (is_int($result['affected_num']) && $result['affected_num'] > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * [delete 删除数据]
     * @param  [type] $where  [删除条件 array('key1'=>$value1, 'key2'=>$value2) | 'id = 1']
     * @param  array  $result [description]
     * @return [type]         [description]
     */
    public function delete($where , &$result = array())
    {
        if (is_array($where)) 
        {
            $tmp = $whereArr = array();

            foreach ($where as $key => $value) 
            {
                if (is_array($value)) 
                {
                    $tmp[] = " `" . $key . "` IN ? ";
                }
                else 
                {
                    $tmp[] = "`" . $key . "` = ? ";
                }
                $whereArr[] = $value;
            }
            $whereStr = implode(" AND ", $tmp);
        }
        else
        {
            $tmp_where = strtolower($where);
            if (!strpos($tmp_where, '=') && !strpos($tmp_where, 'in') && !strpos($tmp_where, 'like')) {
                exit('delte中where条件错误');
            }
            $wherestr = $where;
            $whereArr = '';
        }
        $sql = "DELETE FROM `" . $this->getTableName() . "` WHERE {$whereStr}";
        $query = $this->_sendQuery($sql, $whereArr, 'master', $result);
        if (is_int($result['affected_num']) && $result['affected_num'] > 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * [sqlexec 执行一条SQL语句]
     * @param  [type] $sql             [description]
     * @param  string $data            [description]
     * @param  string $master_or_slave [description]
     * @param  array  $result          [description]
     * @return [type]                  [description]
     */
    public function sqlexec($sql, $data = '', $master_or_slave = 'salve', &$result = array())
    {
        $this->_sendQuery($sql, $data, $master_or_slave, $result);
        if (is_int($result['affected_num']) && $result['affected_num'] >= 0) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * [析构释放内存]
     */
    public function __destruct()
    {
    	unset($this->tableName);
    	unset($this->master_or_slave);
    	unset($this->sql);
    }
}