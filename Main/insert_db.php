<?php
//本页功能只插入数据入库
header("Content-type:text/html;charset=utf-8");

require_once 'config/conn.php';
require_once 'libs/function_db.class.php';

//屏蔽空值错误
// error_reporting(0);

$data = $_POST['data'];
$count = (int)$_POST['count'];

//插入ID
$lastID = getLastId($conn);
//查重
$rowcount = selectRepeat($conn,$data['subject']);
//当前时间
$date = (string)date("Y-m-d h:i:s",time());

//数据  --测试
// print_r($data);
// print_r($lastID);
// print_r($date);
// print_r($rowcount);

if(is_array($data['options'])){
    $str_option = "    '{$data['options'][1]}',
        '{$data['options'][2]}',
        '{$data['options'][3]}',
        '{$data['options'][4]}',";
}else {
    $str_option = "    '','','','',";
}

if($data && $rowcount == 0){
    //题目表
    $sql = "insert into questions (
            course_type,
            z_id,
            j_id,
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
            '{$data['course_type']}',
            '{$data['z_id']}',
            '{$data['j_id']}',
            '{$data['subject']}',
            '{$data['score']}',
            ".$str_option."
            '',
            '',
            '',
            '{$data['choose_right']}',
            '{$data['analysis']}',
            '{$data['type']}',
            '{$date}',
            '{$date}'
     )";

    if ($result=mysqli_query($conn,$sql))
    {
        $count++;
    }

}else if($rowcount == 1){
    $sql = "插入的数据重复";
}else{
    $sql = "错误，没有获取数据";
}

// $result['count']  = $count;
// $result['lastID'] = $lastID;
// $result['sql']    = $sql;


echo json_encode(['count' => $count, 'lastID' => $lastID, 'sql' => $sql]);

?>
