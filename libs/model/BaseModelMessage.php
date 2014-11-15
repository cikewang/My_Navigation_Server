<?php
/**
 * 信息提示类
 * @time  2014/10/24
 */
class BaseModelMessage {
	
	/**
	 * [showError 错误信息提示]
	 * @param  [type]  $msg          [提示信息]
	 * @param  array   $data         [数据内容]
	 * @param  integer $code         [错误号]
	 * @param  string  $url          [提示完成后跳转的URL]
	 * @param  integer $returnTime   [提示完成后，等待跳转时间，默认3秒]
	 * @param  string  $fromEncoding [数据输入内容编码]
	 * @param  string  $outEncoding  [数据输出内容编码]
	 * @return [type]                [description]
	 */
	public static function showError($msg, $data = array(), $code = 11, $url = '', $returnTime = 3, $fromEncoding = '', $outEncoding = 'UTF-8')
	{
		self::message($code, $msg, $data, $url, $returnTime, $fromEncoding, $outEncoding);
	}

	/**
	 * [message 输出提示信息]
	 * @param  [type]  $code         [错误号]
	 * @param  [type]  $msg          [提示信息]
	 * @param  [type]  $data         [数据内容]
	 * @param  [type]  $url          [提示完成后跳转的URL]
	 * @param  integer $returnTime   [提示完成后，等待跳转时间，默认3秒]
	 * @param  string  $fromEncoding [数据输入内容编码]
	 * @param  string  $outEncoding  [数据输出内容编码]
	 * @return [type]                [description]
	 */
	protected static function message($code, $msg, $data, $url, $returnTime = 3, $fromEncoding = '', $outEncoding = 'UTF-8')
	{
		$tpl = new BaseView();
        $tpl->assign('msg', $msg);
        $tpl->assign('url', $url);
        $tpl->assign('returnTime', $returnTime);
        if ($code == '0') {
            $tpl->display('message/message.html');
        } else {
            $tpl->display('message/error.html');
        }
        exit();
	}
}