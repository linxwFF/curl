<?php
header("Content-type:text/html;charset=utf-8");

require_once 'function.class.php';
require_once 'conn.php';
//屏蔽空值错误
error_reporting(0);

//初始化
$url = 'http://www.kjksxt.com/Account/Login';
$cookie_file = tempnam('./temp', 'cookie');     //获取cookie
$login_url="http://www.kjksxt.com/Account/Login";
$post_fields="xttype=1&zhm=fjsy0003&mima=123456";  //模拟登录

//提交登录表单请求
$ch=curl_init($login_url);
curl_setopt($ch,CURLOPT_HEADER,0);
curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
curl_setopt($ch,CURLOPT_POST,1);
curl_setopt($ch,CURLOPT_POSTFIELDS,$post_fields);
curl_setopt($ch,CURLOPT_COOKIEJAR,$cookie_file); //存储提交后得到的cookie数据
curl_exec($ch);
curl_close($ch);

//登录成功后，获取
$url="http://www.kjksxt.com/Main/KqccJiexi?lid=1&jid=6&jname=2015%E5%B9%B4%E3%80%8A%E8%B4%A2%E7%BB%8F%E6%B3%95%E8%A7%84%E4%B8%8E%E4%BC%9A%E8%AE%A1%E8%81%8C%E4%B8%9A%E9%81%93%E5%BE%B7%E3%80%8B%E8%80%83%E5%89%8D%E5%86%B2%E5%88%BA%E4%B8%80";
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

// print_r($questions_type);
//题目
$reg = "%<div class='topic'>(.*?)</div>%si";
preg_match_all($reg, $result, $match);
$topic = $match[1];
foreach ($topic as $key => $value) {
    //单选题
    if ($key <= $questions_type_1) {
        $data['topic'][] = [
            'topic_value' => preg_replace('/(\d+)、/', '',strip_tags($value)),
            'topic_type' => '1',
            'score' => 1,
        ];
    }
    //多选题
    if ($key > $questions_type_1 && $key < ($questions_type_1+$questions_type_2)) {
        $data['topic'][] = [
            'topic_value' => preg_replace('/(\d+)、/', '',strip_tags($value)),
            'topic_type' => '2',
            'score' => 2,
        ];
    }
    //判断题
    if ($key >= ($questions_type_1+$questions_type_2) && $key <= ($questions_type_1+$questions_type_2+$questions_type_3)) {
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
// print_r($options);
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
        'topic' => $data['topic'][$i-1],
        'options' => isset($data['options'][$i-1])?$data['options'][$i-1]:'',
        'right' => $data['right'][$i-1],
        'explain' => $data['explain'][$i-1],
    ];

}
//插入数据库
$sql1 = "select * from questions order by id DESC limit 1";
$ee = mysqli_query($conn, $sql1);
$row = mysqli_fetch_array($ee,MYSQLI_NUM);

//题库的最后一个ID
$j = $row[0]+1;

$date = (string)date("Y-m-d h:i:s",time());

for ($i=1;$i<=count($result);$i++) {

    //题库表
    $sql1 = "insert into question_banks (questions_id, type, created_at, updated_at)
    VALUES (
        '{$j}',
         {$result[$i]['topic']['topic_type']},
         '$date',
         '$date'
    )";
    mysqli_query($conn, $sql1);
    echo $sql1."</br>";
    // exit();
    //关联题目表
    $sql2 = "insert into questions (
            subject,
            score,
            choose_A,
            choose_B,
            choose_C,
            choose_D,
            choose_E,
            choose_F,
            choose_G,
            choose_right,
            analysis,
            type,
            created_at,
            updated_at
     )
     VALUES (
            '{$result[$i]['topic']['topic_value']}',
            {$result[$i]['topic']['score']},
            '{$result[$i]['options'][1]}',
            '{$result[$i]['options'][2]}',
            '{$result[$i]['options'][3]}',
            '{$result[$i]['options'][4]}',
            '',
            '',
            '',
            '{$result[$i]['right']}',
            '{$result[$i]['explain']}',
             {$result[$i]['topic']['topic_type']},
            '$date',
            '$date'
     )";

    mysqli_query($conn, $sql2);
    echo $sql2."</br>";

    $j++;
}



// print_r($result);

?>
