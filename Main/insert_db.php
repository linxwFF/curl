<?php
//本页功能只插入数据入库
header("Content-type:text/html;charset=utf-8");

require_once 'config/conn.php';
require_once 'libs/function_db.class.php';

//屏蔽空值错误
// error_reporting(0);

$data = $_POST['data'];
$count = (int)$_POST['count'];
$countRepeat = (int)$_POST['countRepeat'];
$countFail = (int)$_POST['countFail'];

//插入ID
$lastID = getLastId($conn);


//数据  --测试
// print_r($data);
// print_r($lastID);
// print_r($date);

if ($data) {
    //查重
    if(selectRepeat_qustion($conn,$data['subject']))
    {
        //插入数据题目表
        if ($result=insertDb_questions($conn,$data))
        {
            $count++;
            $message = "插入成功";
        }else {
            $message = "插入数据失败";
            $countFail++;
        }
    }else{
        $message = "插入的数据重复";
        $countRepeat++;
    }


    //插入数据章节表
    if (selectRepeat_chapter($conn,$data['z_name'],$data['j_name']))
    {
        //插入数据题目表
        if ($result=insertDb_chapter($conn,$data))
        {
            // $count++;
            // $message = "插入成功";
        }else {
            $message = "插入数据失败";
            $countFail++;
        }
    }else {
        // $message = "插入的数据重复";
        // $countFail++;
    }

}else {
    $message = "错误，没有获取数据";
    $countFail++;
}


// $result['count']  = $count;
// $result['lastID'] = $lastID;
// $result['sql']    = $sql;

echo json_encode(['count' => $count, 'countRepeat' => $countRepeat, 'countFail' =>$countFail, 'lastID' => $lastID, 'message' => $message]);

?>
