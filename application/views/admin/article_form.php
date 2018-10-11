<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/pop_box.css" />
<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/layout.css" />
<form action="admin/article/getList?page=0" method="get">
	<div class="search" style='float:left;'>
		<div class="form-inline">
			<div class="fl pr marlt-10">
				添加时间： <input type="text" data-start-date="2000" id="date_first"
					readonly="" class="form-control search_input" name="start_time"
					id="start_time"
					value="<?php echo isset($data['start_time'])&&!is_null($data['start_time'])?$data['start_time']:'';?>"
					placeholder="请选择开始时间"> <img class="time-clear"
					src="img/images/cross.png" width="20" />
			</div>
			<div class="fl pr marlt-10">
				至&nbsp;&nbsp;&nbsp;<input type="text" id="date_last" readonly=""
					class="form-control search_input" name="end_time" id="end_time"
					value="<?php echo isset($data['end_time'])&&!is_null($data['end_time'])?$data['end_time']:'';?>"
					placeholder="请选择结束时间"> <img class="time-clear"
					src="img/images/cross.png" width="20" />
			</div>&nbsp;&nbsp;&nbsp;
			<input type="text" class="form-control search_input" name="title"
				id=""
				title"" value="<?php echo isset($data['title'])&&!is_null($data['title'])?$data['title']:'';?>"
				placeholder="请输入文章标题进行搜索">
			<button type="submit" class="btn btn-primary" id="search">搜索</button>
		</div>
	</div>
	<div class="clear">
		<span class="title_desc">共<?php echo $count;?>条记录</span>
	</div>

</form>
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

