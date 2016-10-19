<?php

    function getLastId($conn)
    {
        //查询数据库
        $sql = "select * from questions order by id DESC limit 1";
        $ee = mysqli_query($conn, $sql);
        if($result = mysqli_query($conn, $sql)){
            $row = mysqli_fetch_array($ee,MYSQLI_NUM);
            mysqli_free_result($result);
            //题库的最后一个ID
            $lastID = $row[0];
        }

        return $lastID;
    }

    //查重
    function selectRepeat_qustion($conn,$args)
    {
        $flag = true;
        $sql = "select * from questions where subject = '{$args}' limit 1";

        if ($result=mysqli_query($conn,$sql))
        {
            if(mysqli_num_rows($result)){
                $flag = false;
            }
            mysqli_free_result($result);
        }

        return $flag;
    }

    //查重
    function selectRepeat_chapter($conn,$z_name,$j_name)
    {
        $flag = true;
        $sql = "select * from chapter where z_name = '{$z_name}' and j_name = '{$j_name}' limit 1";

        if ($result=mysqli_query($conn,$sql))
        {
            if(mysqli_num_rows($result)){
                $flag = false;
            }
            mysqli_free_result($result);
        }

        return $flag;
    }

    function insertDb_questions($conn,$data)
    {
        //当前时间
        $date = (string)date("Y-m-d h:i:s",time());

        if(is_array($data['options'])){
            $str_option = "    '{$data['options'][1]}',
                '{$data['options'][2]}',
                '{$data['options'][3]}',
                '{$data['options'][4]}',";
        }else {
            $str_option = "    '','','','',";
        }

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
         return mysqli_query($conn,$sql);

    }


    function insertDb_chapter($conn,$data)
    {
        //当前时间
        $date = (string)date("Y-m-d h:i:s",time());

        $sql = "insert into chapter (
                course_type,
                z_id,
                j_id,
                z_name,
                j_name,
                created_at,
                updated_at
         )
         VALUES (
                '{$data['course_type']}',
                '{$data['z_id']}',
                '{$data['j_id']}',
                '{$data['z_name']}',
                '{$data['j_name']}',
                '{$date}',
                '{$date}'
         )";
         return mysqli_query($conn,$sql);

    }



?>
