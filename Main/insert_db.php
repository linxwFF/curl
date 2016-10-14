<?php
//本页功能只插入数据入库
header("Content-type:text/html;charset=utf-8");

require_once 'config/conn.php';

//屏蔽空值错误
// error_reporting(0);

$data = $_POST['data'];
$count = $_POST['count'];
$false = 0;

$count++;

$result['count'] = $count;
$result['false'] = $false;

echo json_encode(['data' => $result]);

?>
