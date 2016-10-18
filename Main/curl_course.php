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
$url=$_POST['url'];
// $url="http://www.kjksxt.com/main/zjjiexi?lid=1&jid=41&zname=%E7%AC%AC%E4%B8%80%E7%AB%A0%20%E4%BC%9A%E8%AE%A1%E6%B3%95%E5%BE%8B%E5%88%B6%E5%BA%A6&jname=%E7%AC%AC%E4%B8%80%E8%8A%82%20%E4%BC%9A%E8%AE%A1%E6%B3%95%E5%BE%8B%E5%88%B6%E5%BA%A6%E7%9A%84%E6%A6%82%E5%BF%B5%E4%B8%8E%E6%9E%84%E6%88%90";
$ch=curl_init($url);
curl_setopt($ch,CURLOPT_HEADER,0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_COOKIEFILE,$cookie_file); //使用提交后得到的cookie数据做参数
$result=curl_exec($ch);
curl_close($ch);
//处理结果

//科目id  科目名   章id  节id  章名  节名
$query = explode("&",parse_url($url)['query']);
foreach ($query as $key => $value) {
    if($key == 0){
        $course_id = str_replace("lid=", " ", $value);  //科目ID
    }elseif($key == 1){
        $j_id = str_replace("jid=", " ", $value);       //章id
        $z_id = $j_id;                                  //节id
    }elseif($key == 2){
        $z_name = urldecode(str_replace("zname=", " ", $value));   //章名
    }elseif($key == 3){
        $j_name = urldecode(str_replace("jname=", " ", $value));   //节名
    }
}

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

// print_r($questions_type);

//题目
$reg = "%<div class='topic'>(.*?)</div>%si";
preg_match_all($reg, $result, $match);
$topic = $match[1];

foreach ($topic as $key => $value) {
    //单选题
    if ($key <= $questions_type_1-1) {
        $data['topic'][] = [
            'topic_value' => preg_replace('/(\d+)、/', '',strip_tags($value)),
            'topic_type' => '1',
            'score' => 1,
        ];
    //多选题
    }elseif ($key > $questions_type_1-1 && $key <= $questions_type_1+$questions_type_2-1) {
        $data['topic'][] = [
            'topic_value' => preg_replace('/(\d+)、/', '',strip_tags($value)),
            'topic_type' => '2',
            'score' => 2,
        ];
    //判断题
    }else {
        $data['topic'][] = [
            'topic_value' => preg_replace('/(\d+)、/', '',strip_tags($value)),
            'topic_type' => '3',
            'score' => 1,
        ];
    }
}

//选项
$reg = "%<div class='options'>(.*?)</div>%si";
preg_match_all($reg, $result, $match);
$options = $match[1];
foreach ($options as $key => $value) {
    $temps = explode('.', strip_tags($value));
    foreach ($temps as $key => $value) {
        $temp[] = trim(str_replace(array('A', 'B', 'C', 'D'), array('', '', '',''), $value));
    }
    $data['options'][] =  $temp;
    unset($temp);
}
// print_r($data['options']);

//答案
$reg = "%【标准答案】(.*?)</li>%si";
preg_match_all($reg, $result, $match);
$right = $match[1];
foreach ($right as $key => $value) {
    $data['right'][] = str_replace('：', '',strip_tags($value));
}
// print_r($right);
//解析
$reg = "%【试题解析】(.*?)</p>%si";
preg_match_all($reg, $result, $match);
$explain = $match[1];
foreach ($explain as $key => $value) {
    $data['explain'][] = str_replace('：', '',strip_tags($value));
}
// print_r($explain);
//最后组成数据记录块

$result = [];
for ($i=1;$i<=count($data['topic']);$i++) {

    $result[$i] = [
        //题库表
        'subject'         => $data['topic'][$i-1]['topic_value'],
        'type'            => $data['topic'][$i-1]['topic_type'],
        'score'           => $data['topic'][$i-1]['score'],
        'options'         => isset($data['options'][$i-1])?$data['options'][$i-1]:'',
        'choose_right'    => $data['right'][$i-1],
        'analysis'        => $data['explain'][$i-1],
        //科目章节目录表
        'course_type'       => $course_id,
        'z_id'            => $z_id,
        'j_id'            => $j_id,
        'z_name'          => $z_name,
        'j_name'          => $j_name,
    ];

}

//结果JSON格式输出
echo json_encode(['data' => $result, 'sum' => $questions_type_sum]);

?>
