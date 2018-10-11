<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/pop_box.css" />
<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/layout.css" />
<style>
.bg {
	background-color: #aaa;
	width: 100%;
	height: 100%;
	left: 0;
	top: 0; /*FF IE7*/
	filter: alpha(opacity = 50); /*IE*/
	opacity: 0.5; /*FF*/
	z-index: 1;
	position: fixed !

important; /*FF IE7*/
}

.mydiv {
	display: none;
	position: absolute;
	top: 25%;
	left: 35%;
	width: 30%;
	background-color: #FFF;
	z-index: 1002;
	overflow: auto;
	text-align: center;
}
</style>


<form action="admin/user/getList?page=0" method="get">
    <div class="search" style='float:left;'>
		<div class="form-inline">
        	<span class="title_desc"> 性别： <select name="gender" id="gender">
        			<option value=''>不限</option>
        			<option value='男'
        				<?php echo isset($data['gender'])&&$data['gender']=='男'?'selected':''; ?>>男</option>
        			<option value='女'
        				<?php echo isset($data['gender'])&&$data['gender']=='女'?'selected':''; ?>>女</option>
        	</select>&nbsp;&nbsp;&nbsp;糖尿病类型： <select name="illness_type" id="illness_type">
        			<option value=''>不限</option>
        			<option value='I型'
        				<?php echo isset($data['illness_type'])&&$data['illness_type']=='I型'?'selected':''; ?>>I型</option>
        			<option value='II型'
        				<?php echo isset($data['illness_type'])&&$data['illness_type']=='II型'?'selected':''; ?>>II型</option>
        	</select>
        	</span>&nbsp;&nbsp;&nbsp;
			<input type="text" class="form-control search_input" name="username"
				id="username"
				value="<?php echo isset($data['username'])&&!is_null($data['username'])?$data['username']:'';?>"
				placeholder="请输入会员姓名">
			<button type="submit" class="btn btn-primary" id="search">搜索</button>
			<button type="button" class="btn btn-primary"
				onclick="uploadUserInfo()">会员信息导出</button>
		</div>
	</div>
	<div class="clear">
		<span class="title_desc">共<?php echo $count;?>条记录</span>
	</div>

</form>

<!-- 指派二维码 遮罩层-->
<div id="bg" class="bg" style="display: none;"></div>
<div id="userDiv" class="mydiv" style="display: none;">
	<p style="text-align: center; margin-top: 3%; font-weight: bold;">用户状态更改</p>
	<p style="margin-top: 3%; text-align: center;">
		<span id='span_info'></span>
	</p>
	<div class="anniu"></div>
</div>


<script src="js/admin/jquery.datatime.min.js"></script>
<script>
	function uploadUserInfo(){
		var illness_type = $('#illness_type').val();
		var gender = $('#gender').val();
		var username = $('#username').val();
		location.href="<?php echo base_url('admin/user/downloadUser') ?>?illness_type="+illness_type+'&gender='+gender+'&username='+username;
	}
</script>

