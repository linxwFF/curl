$(document).ready(function() {




});

    function init()
    {
        //重置
        $("#resultDiv").empty();
        console.log("清空");
        $('#requst_pro').css("width", "0%");
    }

    //选择科目->获取科目章节
    function selectCourse(url)
    {
        //初始化进度条
        init();

        $.ajax({
            url: 'curl_dir.php',
            type:'post',
            data:{
                url : 'http://www.kjksxt.com/Main/' + url
            },
            async : true, //默认为true 异步
            error:function(){
               alert('error');
            },
            success:function(data){

                var data = JSON.parse(data);
                $('#requst_pro').attr('aria-valuemax', data.data.length);
                for (var i = 0; i < data.data.length; i++) {
                    // console.log(data.data[i].jname);
                    // console.log(data.data[i].url);

                //进度条
                var per = (((i+1)/data.data.length)*100).toFixed(2);
                $('#requst_pro').css("width", per + "%");

                var course = '';

                course += '        <div class="col-md-2 mt_15">';
                course += '            <button class="btn btn-success" onclick="start(this.value,'+ i +');" value="'+data.data[i].url+'" >开始</button>&nbsp;&nbsp;';
                course += '            <button class="btn btn-warning">暂停</button>&nbsp;&nbsp;';

                course += '        </div>';
                course += '        <div class="col-md-10 mt_15">';

                course += '        <div class="col-md-12">';
                course += '        <div class="col-md-2">';
                course += '            <span class="label label-info" >获取数据进度：</span>';
                course += '        </div>';
                course += '        <div class="col-md-10">';
                course += '             <div class="progress" id="requst_pro_div_'+i+'" style="margin-top:7px;height: 3px;">';
                course += '                <div class="progress-bar" role="progressbar" id="requst_pro_'+i+'" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100"></div>';
                course += '             </div>';
                course += '        </div>';
                course += '        </div>';

                course += '        <div class="col-md-12">';
                course += '        <div class="col-md-2">';
                course += '            <span class="label label-default" >数据入库进度：</span>';
                course += '        </div>';
                course += '        <div class="col-md-10">';
                course += '            <div class="progress" style="margin-top:7px">';
                course += '              <div class="progress-bar" role="progressbar" id="progress_'+i+'" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100">';
                course += '                0%';
                course += '              </div>';
                course += '            </div>';
                course += '        </div>';
                course += '        </div>';

                course += '        </div>';

                course += '<div class="col-md-12 single_item ">';
                course += '        <div class="col-md-4 mb_15">';
                course += '        <td align="left" height="35" style="padding-left: 20px;"">&nbsp;&nbsp;' + data.data[i].jname + '</td>';
                course += '        </div>';
                course += '        <div class="col-md-4 mb_15"><span id="count_'+i+'" style="color:red">0</span>/<span id="sum_'+i+'">0</span></div>';
                course += '        <div class="col-md-4 mb_15"><span id="countFail_'+i+'" style="color:red">0</span>/<span id="countSuccess_'+i+'">0</span></div>';
                course += '</div>';

                     $("#resultDiv").append(course);
                    }
             }
        });
    }

    function start(url, i)
    {
        console.log(url);
        //初始化进度条

        $.ajax({
            url: 'curl_course.php',
            type:'post',
            data:{
                url : 'http://www.kjksxt.com' + url
            },
            async : true, //默认为true 异步
            error:function(){
               alert('error');
            },
            success:function(data){
                var data = JSON.parse(data);
                console.log(i);
                var sum = data.sum;
                $('#sum_'+ i).html(data.sum);
                console.log(data.sum);
                //获取数据进度条
                $('#requst_pro_'+i).css("width", "100%");
                //--------------数据存入数据库
                for (var j = 1; j <= sum; j++) {
                    console.log('第'+j+'数据');
                    $.ajax({
                        url: 'insert_db.php',
                        type:'post',
                        data:{
                            data : data.data[j],
                            count : j
                        },
                        async : false, //默认为true 异步
                        error:function(){
                           alert('error');
                        },
                        success:function(data){

                            var data = JSON.parse(data);

                            //进度条
                            var count = data.count;
                            console.log(count);
                            var per = ((j/sum)*100).toFixed(2);
                            $('#progress_'+ i).css("width", per + "%");
                            $('#progress_'+ i).html(per + "%");
                            $('#count_'+ i).html(count);

                            //完成时计算成功失败个数
                            if(j == sum){
                                $('#countFail_'+ i).html("总失败："+ (sum - count));
                                $('#countSuccess_'+ i).html("总成功："+ count);
                            }
                        }
                    });
                }
                //------------数据存入数据库
            }
        });
    }
