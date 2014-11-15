<?php
header("Content-Type:text/html;charset=UTF-8");
set_time_limit(0);
define('ASSASSIN_PATH', str_replace("\\",'/',rtrim(dirname(__FILE__),'/').'/'));
require(ASSASSIN_PATH.'libs/init.php');