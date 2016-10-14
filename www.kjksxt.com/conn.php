<?php
//数据库连接文件

$conn = mysqli_connect("localhost", "root", "") or die("数据库连接失败!" . mysqli_error());
//选择数据库
mysqli_select_db($conn,"lxw_xuekuaiji");
//设置编码UTF
mysqli_query($conn,"set names utf8");

?>
