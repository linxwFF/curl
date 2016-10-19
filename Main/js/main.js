$(document).ready(function() {




});
    //全部开始
    function startAll(url)
    {
        if(url == ''){
            alert("请选择项目");
            return false;
        }

        $.ajax({
            url: 'curl_dir.php',
            type:'post',
            data:{
                url : 'http://www.kjksxt.com/Main/' + url
            },
            async : false, //默认为true 异步
            beforeSend: function(){
                //初始化进度条
                $('#requst_pro').css("width", "0%");
            },
            error:function(){
               alert('error');
            },
            success:function(data){
                var data = JSON.parse(data);
                $('#requst_pro').attr('aria-valuemax', data.data.length);
                for (var i = 0; i < data.data.length; i++)
                {
                    var url = (data.data[i].url).replace(new RegExp("&amp;","gm"),"&");
                    console.log("第"+(i+1)+"子项");
                    //批处理
                    start(url, i);
                    //获取数据进度条
                    var per = (((i+1)/data.data.length)*100).toFixed(2);
                    $('#requst_pro').css("width", per + "%");
                    //批处理进度条
                    $('#progressAll').css("width", per + "%");
                    $('#progressAll').html(per + "%");
                    //总记录数
                    $('#countAll').html("总记录数："+i);
                }
             }
        });
    }

    //选择科目->获取科目章节
    function selectCourse(url)
    {
        $("#startAll").attr("value", url);
        $.ajax({
            url: 'curl_dir.php',
            type:'post',
            data:{
                url : 'http://www.kjksxt.com/Main/' + url
            },
            async : false, //默认为true 异步
            beforeSend: function(){
                //初始化进度条
                $("#resultDiv").empty();
                $('#requst_pro').css("width", "0%");
                $('#progressAll').css("width", "0%");
            },
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
                course += '        <div class="col-md-3 mb_15">';
                course += '        <td align="left" height="35" style="padding-left: 20px;"">&nbsp;&nbsp;' + data.data[i].jname + '</td>';
                course += '        </div>';
                course += '        <div class="col-md-3 mb_15"><span id="count_'+i+'" style="color:red">0</span>/<span id="sum_'+i+'">0</span></div>';
                course += '        <div class="col-md-3 mb_15"><span id="countFail_'+i+'" style="color:red">0</span>/<span id="countSuccess_'+i+'">0</span></div>';
                course += '        <div class="col-md-3 mb_15"><span id="countRepeat_'+i+'" style="color:red">重复个数:0</div>';

                course += '        <div class="col-md-12 mb_15" style="height:100px;" >';
                course += ' <textarea class="form-control" rows="4" id="resultInfo_'+i+'"></textarea>';

                course += '</div>';

                course += '</div>';

                     $("#resultDiv").append(course);
                    }
             }
        });
    }

    function start(url, i)
    {
        $.ajax({
            url: 'curl_course.php',
            type:'post',
            data:{
                url : 'http://www.kjksxt.com' + url
            },
            async : false, //默认为true 异步
            beforeSend: function(){
                //初始化进度条
                $('#requst_pro_'+i).css("width", "0%");
                $('#progress_'+ i).css("width", "0%");
                $('#progress_'+ i).html("0%");
            },
            error:function(){
               alert('error');
            },
            success:function(data){
                var data = JSON.parse(data);
                var sum = data.sum;     //总处理条目
                $('#sum_'+ i).html(sum);
                //获取数据进度条
                $('#requst_pro_'+i).css("width", "100%");
                //计数器常量
                var count = 0; //处理进度条目
                var countRepeat = 0; //重复
                var countFail = 0; //失败
                //--------------数据存入数据库
                for (var j = 1; j <= sum; j++) {
                    $.ajax({
                        url: 'insert_db.php',
                        type:'post',
                        data:{
                            data : data.data[j],
                            count : count,
                            countRepeat : countRepeat,
                            countFail : countFail
                        },
                        async : false, //默认为true 异步
                        error:function(){
                           alert('error');
                        },
                        success:function(data){

                            var data = JSON.parse(data);

                            count = data.count;  //处理进度条目（一定是插入数据库成功标志）
                            countRepeat = data.countRepeat;
                            countFail = data.countFail;

                            //进度条
                            var per = ((j/sum)*100).toFixed(2);
                            $('#progress_'+ i).css("width", per + "%");
                            $('#progress_'+ i).html(per + "%");
                            $('#count_'+ i).html("预处理条数："+ count);
                            $('#resultInfo_'+ i).prepend( j + ".正在处理ID："+ data.lastID +"---" + data.message +"\r\n");  //返回基本信息

                            //完成时计算成功失败个数
                            if(j == sum){
                                $('#countFail_'+ i).html("总失败条数："+ countFail);
                                $('#countSuccess_'+ i).html("总插入数据库条数："+ count);
                                $('#countRepeat_'+ i).html("总重复条数"+ countRepeat);
                            }
                        }
                    });
                }
                //------------数据存入数据库
            }
        });
    }
