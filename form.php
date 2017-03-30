<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">


    <title> 抢票开始</title>
    <meta name="keywords" content="">
    <meta name="description" content="">

    <link rel="shortcut icon" href="favicon.ico"> <link href="css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="css/font-awesome.css?v=4.4.0" rel="stylesheet">
    <link href="css/animate.css" rel="stylesheet">
    <link href="css/style.css?v=4.1.0" rel="stylesheet">

</head>

<body class="gray-bg" style="width:100%;">
    <div class="wrapper wrapper-content animated fadeInRight">

        <div class="row" style="margin:20px auto;width:600px;" >
            <div class="col-sm-6" style="margin:0 auto;width:600px;">
                <div class="ibox float-e-margins" >
                    <div class="ibox-title">
                        <h5>抢票</h5>

                    </div>
                    <div class="ibox-content">
                        <form class="form-horizontal m-t" id="commentForm">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">出发站：</label>
                                <div class="col-sm-8">
                                    <?php
                                    $file=file('station.list');
                                    ?>
                                    <select class="form-control m-b" id="chufa" style="height:35px;" >
                                        <?php foreach ($file as $v){?>
                                        <option><?php echo $v;?></option>
                                       <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">终点站：</label>
                                <div class="col-sm-8">
                                    <select class="form-control m-b" id="zhongdian" style="height:35px;">
                                        <?php foreach ($file as $v){?>
                                        <option><?php echo $v;?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">E-mail：</label>
                                <div class="col-sm-8">
                                    <input id="cemail" type="email" class="form-control" name="email" required="" aria-required="true" value="340562435@qq.com" style="height:35px;" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">日期：</label>
                                <div class="col-sm-8">
                                    <input id="hello"  class="laydate-icon form-control layer-date"  style="max-width:350px;height:35px;">
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-3">
                                    <button class="btn btn-primary" type="button" id="sub">开抢</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

            </div>



    </div>
</div>

    <!-- 全局js -->
    <script src="js/jquery.min.js?v=2.1.4"></script>
    <script src="layer/layer.js"></script>
    <script>
        $('#sub').click(function(){
            var data={
                'start':$('#chufa').val(),
                'end':$('#zhongdian').val(),
                'email':$('#cemail').val(),
                'date':$('#hello').val()
            }
            $.ajax({ url: "12306.php", data: data,type:'post', success: function(res){
				if(res){
					 layer.alert(res) ;
				}
               

            }
            });
        })
         setInterval(function(){
             $('#sub').trigger('click');
         },1200000);
    </script>
    <script src="js/bootstrap.min.js?v=3.3.6"></script>

    <!-- 自定义js -->
    <script src="js/content.js?v=1.0.0"></script>

    <!-- jQuery Validation plugin javascript-->
    <script src="js/plugins/validate/jquery.validate.min.js"></script>
    <script src="js/plugins/validate/messages_zh.min.js"></script>
    <script src="js/plugins/layer/laydate/laydate.js"></script>
    <script src="js/demo/form-validate-demo.js"></script>
    <script>
        //外部js调用
        laydate({
            elem: '#hello', //目标元素。由于laydate.js封装了一个轻量级的选择器引擎，因此elem还允许你传入class、tag但必须按照这种方式 '#id .class'
            event: 'focus' //响应事件。如果没有传入event，则按照默认的click
        });

        //日期范围限制
        var start = {
            elem: '#start',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: laydate.now(), //设定最小日期为当前日期
            max: '2099-06-16 23:59:59', //最大日期
            istime: true,
            istoday: false,
            choose: function (datas) {
                end.min = datas; //开始日选好后，重置结束日的最小日期
                end.start = datas //将结束日的初始值设定为开始日
            }
        };
        var end = {
            elem: '#end',
            format: 'YYYY/MM/DD hh:mm:ss',
            min: laydate.now(),
            max: '2099-06-16 23:59:59',
            istime: true,
            istoday: false,
            choose: function (datas) {
                start.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(start);
        laydate(end);

    </script>
    
</body>

</html>
