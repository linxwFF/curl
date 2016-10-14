<?php
//本页功能只返回课程
header("Content-type:text/html;charset=utf-8");

require_once 'libs/function.class.php';
require_once 'config/conn.php';

//屏蔽空值错误
// error_reporting(0);

//初始化
$cookie_file = tempnam('./temp', 'cookie');     //获取cookie
$login_url="http://www.kjksxt.com/Account/Login";
$post_fields="xttype=1&zhm=fjsy0003&mima=123456";
$cookie_file = initLogin($cookie_file, $login_url, $post_fields);

//登录成功后，获取
$url="http://www.kjksxt.com/Main/ZjJiexi?lid=1&jid=41&zname=%E7%AC%AC%E4%B8%80%E7%AB%A0%20%E4%BC%9A%E8%AE%A1%E6%B3%95%E5%BE%8B%E5%88%B6%E5%BA%A6&jname=%E7%AC%AC%E4%B8%80%E8%8A%82%20%E4%BC%9A%E8%AE%A1%E6%B3%95%E5%BE%8B%E5%88%B6%E5%BA%A6%E7%9A%84%E6%A6%82%E5%BF%B5%E4%B8%8E%E6%9E%84%E6%88%90";
$ch=curl_init($url);
curl_setopt($ch,CURLOPT_HEADER,0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file); //使用提交后得到的cookie数据做参数
$result=curl_exec($ch);
curl_close($ch);
//处理结果

//匹配题型
$reg = "%bgcolor='#FFFFFF'>(.*?)</td>%si";
preg_match_all($reg, $result, $match);
$questions_type = $match[1];
//单选题
$questions_type_1 = $questions_type[0];
//多选题
$questions_type_2 = $questions_type[1];
//判断题
$questions_type_3 = $questions_type[2];
//总题
$questions_type_sum = $questions_type[3];

print_r($questions_type_sum);


?>
