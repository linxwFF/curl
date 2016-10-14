<?php
//本页功能只返回科目列表
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
$url=$_POST['url'];
$ch=curl_init($url);
curl_setopt($ch,CURLOPT_HEADER,0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file); //使用提交后得到的cookie数据做参数
$result=curl_exec($ch);
curl_close($ch);
//处理结果

//匹配章节练习 科目目录
$reg = "%<td align='left' height='35' style='padding-left: 20px;'>&nbsp;&nbsp;(.*?)<img src='%si";
preg_match_all($reg, $result, $text);


foreach ($text[0] as $key => $value) {
    //文字
    $reg = "%<td align='left' height='35' style='padding-left: 20px;'>&nbsp;&nbsp;(.*?)</td>%si";
    preg_match_all($reg, $value, $text);
    $textArr[$key] = $text[1];
    //链接
    $reg = "%><a href=\"(.*?)\">%si";
    preg_match_all($reg, $value, $url);
    $urlArr[$key] = $url[1];
}


for ($i=0; $i < count($textArr); $i++) {
    $arr[$i]['jname'] = $textArr[$i][0];
    $arr[$i]['url']  = $urlArr[$i][0];
}



echo json_encode(['data' => $arr]);

?>
