<?php
require_once 'config/conn.php';

//登录初始
function initLogin($cookie_file, $login_url, $post_fields){

    //提交登录表单请求 //模拟登录
    $ch=curl_init($login_url);
    curl_setopt($ch,CURLOPT_HEADER,0);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
    curl_setopt($ch,CURLOPT_POST,1);
    curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
    curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie_file); //存储提交后得到的cookie数据
    curl_exec($ch);
    curl_close($ch);

    return $cookie_file;
}

//采集方法

//get方法采集

function get_Curl($url) {
	//初始化
	$curl = curl_init();

	// 设置你需要抓取的URL
	curl_setopt($curl,CURLOPT_URL,$url);

	// 设置header
	curl_setopt($curl,CURLOPT_HEADER,0);

	// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

	//超时设置
	curl_setopt($curl, CURLOPT_TIMEOUT,0);   //只需要设置一个秒的数量就可以

	// 运行cURL，请求网页
	$output = curl_exec($curl);

	// 关闭URL请求
	curl_close($curl);

	return $output;
}


function post_Curl($url,$curlPost) {

	//初始化
	$curl = curl_init();

	// 设置你需要抓取的URL
	curl_setopt($curl,CURLOPT_URL,$url);

	//设置伪造头
//	curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);

	// 设置是否输出头信息
	curl_setopt($curl,CURLOPT_HEADER,0);

	// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

	//开启POST请求
	curl_setopt($curl,CURLOPT_POST,1);
	//传递参数
	curl_setopt($curl,CURLOPT_POSTFIELDS,$curlPost);

	// 运行cURL，请求网页
	$output = curl_exec($curl);

	// 关闭URL请求
	curl_close($curl);

	return $output;
}



function post_Curl_setHeader($url,$curlPost) {

	$header = set_header();
	//初始化
	$curl = curl_init();

	// 设置你需要抓取的URL
	curl_setopt($curl,CURLOPT_URL,$url);

	//设置伪造头
	curl_setopt($curl,CURLOPT_HTTPHEADER,$header);

	// 设置是否输出头信息
	curl_setopt($curl,CURLOPT_HEADER,0);

	// 设置cURL 参数，要求结果保存到字符串中还是输出到屏幕上。
	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);

	//开启POST请求
	curl_setopt($curl,CURLOPT_POST,1);
	//传递参数
	curl_setopt($curl,CURLOPT_POSTFIELDS,$curlPost);

	// 运行cURL，请求网页
	$output = curl_exec($curl);

	// 关闭URL请求
	curl_close($curl);

	return $output;
}


function set_header(){
    $headers = array();
    $headers[] = 'Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8';
    $headers[] = 'Accept-Encoding:gzip, deflate';
    $headers[] = 'Accept-Language:zh-CN,zh;q=0.8';
    $headers[] = 'Cache-Control:max-age=0';
    $headers[] = 'Connection:keep-alive';
    $headers[] = 'Content-Length:33';
    $headers[] = 'Content-Type:application/x-www-form-urlencoded';
    $headers[] = 'Host:www.kjksxt.com';
    $headers[] = 'Referer:http://www.kjksxt.com/Account/Login';
    $headers[] = 'User-Agent:Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/54.0.2824.2 Safari/537.36';
    return $headers;
}






/*
 * 抓取回来的网页进行正则查找
 * id是按ID查找内容
 * tagName是标签查找
 * className按类名查找*/

    function id($data,$id){
        preg_match('%<(.*)\s*id=.*('.$id.').*>\s*(.*)\s*<\/(.*)>%si',$data,$str);
        return $str[0];
    }

    function tagName($data,$tag){
        preg_match('%<'.$tag.'.*>\s*(.*)\s*<\/'.$tag.'>%si',$data,$str);
        return $str[1];
    }

    function className($data,$class){
        preg_match('%<(.*)\s*class=.*('.$class.').*>\s*(.*)\s*<\/(.*)>%si',$data,$str);
        return $str[1];
    }

//匹配所有ID
	function idAll($data,$id){
        preg_match_all('%<(.*)\s*id=.*('.$id.').*>\s*(.*)\s*<\/(.*)>%si',$data,$str);
        return $str;
    }
//匹配所有HTML标签
    function tagNameAll($data,$tag){
        preg_match_all('#<'.$tag.'>(.*)</'.$tag.'>#',$data,$str);
        return $str;
    }
//匹配所有class里面的内容
    function classNameAll($data,$class){
        preg_match_all('%<(.*)\s*class=.*('.$class.').*?(.*?)<\/(.*)>%si',$data,$str);
        return $str;
    }


//获取表格里面的数组变成数组
function get_td_array($table) {
//匹配去掉Table标签
  $table = preg_replace("'<table[^>]*?>'si","",$table);
//匹配去掉Tr标签
  $table = preg_replace("'<tr[^>]*?>'si","",$table);
//匹配去掉Td标签
  $table = preg_replace("'<td[^>]*?>'si","",$table);
//匹配把结束</tr>变成{tr}标签
  $table = str_replace("</tr>","{tr}",$table);
//匹配把结束</td>变成{td}标签
  $table = str_replace("</td>","{td}",$table);
//去掉 HTML 其他标记
  $table = preg_replace("'<[/!]*?[^<>]*?>'si","",$table);
//去掉空白字符
  $table = preg_replace("'([rn])[s]+'","",$table);
  $table = str_replace(" ","",$table);
  $table = str_replace(" ","",$table);
//通过标识符 将字符串打成数组
  $table = explode('{tr}', $table);
//删除最后一个没用的标签防止生成空数组元素
  array_pop($table);
//循环生成多维元素
  foreach ($table as $key=>$tr) {
    $td = explode('{td}', $tr);
	//删除最后一个没用的标签防止生成空数组元素
    array_pop($td);
    $td_array[] = $td;
  }
  return $td_array;
}

//匹配开头和结尾中间的数据
function cut($cutLeft,$cutRight,$dataA)
{
        $dataA = explode($cutLeft,$dataA);
        $dataA = explode($cutRight,$dataA[1]);
        $dataA = $dataA[0];
        return $dataA;
}



//删除HTML代码、去掉多余的空格、删除回车换行符等等
function DeleteHtml($str)
{
$str = trim($str);
$str = strip_tags($str,"");
$str = ereg_replace("\t","",$str);
$str = ereg_replace("\r\n","",$str);
$str = ereg_replace("\r","",$str);
$str = ereg_replace("\n","",$str);
$str = ereg_replace(" "," ",$str);
return trim($str);
}


//去除空格  换行等

function removeBlankLinefeed($str){
	return trim(str_replace(array("\r","\n"), '',$str));
}

//去除数组中的空格  换行等

function trimall($str)//删除空格
{
    $qian=array(" ","　","\t","\n","\r");$hou=array("","","","","");
    return str_replace($qian,$hou,$str);
}



/**
 * 指定位置插入字符串
 * @param $str  原字符串
 * @param $i    插入位置
 * @param $substr 插入字符串
 * @return string 处理后的字符串
 */
function insertToStr($str, $i, $substr){
    //指定插入位置前的字符串
    $startstr="";
    for($j=0; $j<$i; $j++){
        $startstr .= $str[$j];
    }

    //指定插入位置后的字符串
    $laststr="";
    for ($j=$i; $j<strlen($str); $j++){
        $laststr .= $str[$j];
    }

    //将插入位置前，要插入的，插入位置后三个字符串拼接起来
    $str = $startstr . $substr . $laststr;

    //返回结果
    return $str;
}




    //--------------------------------------------------采集图片保存到本地-------------------------------------------
	/*
     * $url 图片地址
     * $filepath 图片保存地址
	 * $fileName  图片名字
     * return 返回下载的图片路径和名称
     */
    function getimg($url,$filepath,$fileName) {

        if ($url == '') {
            return false;
        }


        	//判断路经是否存在
        !is_dir($filepath)?mkdir($filepath):null;

        //图片名
        $filename = $fileName;

        //读取图片
        $img = get_Curl($url);
        //指定打开的文件
        $fp = fopen($filepath.'/'.$filename, 'a');
        //写入图片到指定的文本
        fwrite($fp, $img);
        fclose($fp);
        return '/'.$filepath.'/'.$filename;
    }

?>
