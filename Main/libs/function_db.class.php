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

    function selectRepeat($conn,$args)
    {
        //查重
        $sql = "select * from questions where subject = '{$args}' limit 1";

        if ($result=mysqli_query($conn,$sql))
        {
            $rowcount = mysqli_num_rows($result);
            mysqli_free_result($result);
        }

        return $rowcount;
    }



?>
