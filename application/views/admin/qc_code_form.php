<link rel="stylesheet"
      href="<?php echo base_url()?>css/admin/pop_box.css" />
<link rel="stylesheet"
      href="<?php echo base_url()?>css/admin/layout.css" />

<style>
    .bg {
        background-color: #aaa;
        width: 100%;
        height: 100%;
        text-align: center;
        top: 0;
        left: 0;
        bottom: 0;
        right: 0;
        filter: alpha(opacity = 50); /*IE*/
        opacity: 0.5; /*FF*/
        z-index: 1000;
        overflow: auto;
        position: fixed !important;
    }
    .mydiv {
        display: none;
        position: fixed;
        top: 24%;
        left: 36%;
        bottom: 0;
        right: 0;
        width: 20%;
        height:322px;
        text-align: center;
        background-color: #FFF;
        z-index: 1002;
        overflow: auto;
    }
</style>

<form action="admin/qc_code/getList?page=0" method="get">
	<span class="title_desc">
        <?php if($this->session->userdata('type')=='0'){
            echo "业务员：";
        } else{
            echo "经销商：";
        }
        ?><select name="dealer_id" id="dealer_id"
			<option value=''>不限</option>
        <?php
        if (isset($dealer_name) && ! empty($dealer_name)) {
            foreach ($dealer_name as $k => $v) {
                if ($data['dealer_id'] == $v['id']) {
                    echo "<option value='" . $v['id'] . "' selected>" . $v['dealer_name'] . "</option>";
                } else {
                    echo "<option value='" . $v['id'] . "'>" . $v['dealer_name'] . "</option>";
                }
            }
        }
        ?>
        </select>
	</span>
    <div class="search" style='float: none;'>
        <div class="form-inline">
            <div class="fl pr" style="margin-left: 18px">
                添加时间： <input type="text" data-start-date="2000" id="date_first"
                             readonly="" class="form-control search_input" name="start_time"
                             id="start_time"
                             value="<?php echo isset($data['start_time'])&&!is_null($data['start_time'])?$data['start_time']:'';?>"
                             placeholder="请选择开始时间"> <img class="time-clear"
                                                         src="img/images/cross.png" width="20" />
            </div>
            <div class="fl pr marlt-10">
                至 <input type="text" id="date_last" readonly=""
                         class="form-control search_input" name="end_time" id="end_time"
                         value="<?php echo isset($data['end_time'])&&!is_null($data['end_time'])?$data['end_time']:'';?>"
                         placeholder="请选择结束时间"> <img class="time-clear"
                                                     src="img/images/cross.png" width="20" />
            </div>&nbsp;&nbsp;&nbsp;
            <button type="submit" class="btn btn-primary" id="search">搜索</button>
            <button type="button" class="btn btn-primary" id="search"
                    onclick="stamp('<?php echo base_url('admin/qc_code/printCode');?>')">打印
            </button>
            <?php
            if ($this->session->userdata('type') == '3') {
                echo " <button type='button' class='btn btn-primary' id='search' onclick='generateCode()'>生成二维码</button>";
            }
                ?>


        </div>
    </div>
    <div class="clear">
        <span class="title_desc">共<?php echo $count; ?>
            条记录
        </span>
    </div>
</form>
<script src="js/admin/jquery.datatime.min.js"></script>
<script src="js/admin/curd.js"></script>
<!-- 指派二维码 遮罩层-->
<div id="bg" class="bg" style="display: none;"></div>
<div id="popDiv" class="mydiv" style="display: none;">
    <p style="text-align: center; margin-top: 3%; font-weight: bold;">二维码指派</p>
    <p style="margin-top: 3%; text-align: center;">
        经销商： <select name="dealer_id" id="dealer_id">
            <option></option>
        </select>
    </p>
    <div class="anniu">
        <button type="button" class="btn btn-primary" id="assgin_commit">确认</button>
        &nbsp;&nbsp;
        <button type="button" class="btn btn-primary" id="assgin_cancel">取消</button>
    </div>
</div>
<!-- 生成二维码数量 遮罩层-->
<div id="numDiv" class="mydiv" style="display: none;">
    <p style="text-align: center; margin-top: 3%; font-weight: bold;">生成二维码数量</p>
    <p style="margin-top: 2%; text-align: center;">
        <select name="code_num" id="code_num">
            <?php
            for ($i = 1; $i < 2; $i ++) {
                echo "<option value=" . $i . ">" . $i . "</option>";
            }
            ?>
        </select>
    </p>
    <div class="anniu">
        <button type="button" class="btn btn-primary" id="num_commit">确认</button>
        &nbsp;&nbsp;
        <button type="button" class="btn btn-primary" id="num_cancel"
                onclick="closeNumdiv()">取消</button>
    </div>
</div>

<!-- 预览二维码 遮罩层-->
<div id="bg" class="bg" style="display: none;"></div>
<div id="codeDiv" class="mydiv" style="display: none; min-width: 400px;">
    <img id='img' width="80%">
</div>
<script src="js/admin/jquery.datatime.min.js"></script>
<script>
    // 双联动日期示例
    (function(){
        var dateFirst = $('#date_first');
        var dateLast = $('#date_last');
        var dateFirstApi;
        var dateLastApi;

        dateFirst.cxCalendar(function(api){
            dateFirstApi = api;
        });

        dateLast.cxCalendar(function(api){
            dateLastApi = api;
        });

        dateFirst.bind('change', function(){
            var firstTime = parseInt(dateFirstApi.getDate('TIME'), 10);
            var lastTime = parseInt(dateLastApi.getDate('TIME'), 10);

            if (lastTime < firstTime) {
                dateLastApi.clearDate();
            };

            dateLastApi.setOptions({
                startDate: firstTime
            });
            dateLastApi.show();
        });
    })();
    $(document).ready(function(){
        $(".time-clear").click(function(){
            $(this).prev("input").val("");
        });
    });
</script>
