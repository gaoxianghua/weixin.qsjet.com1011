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
	position: fixed;
	top: 25%;
	left: 35%;
	width: 35%;
	background-color: #FFF;
	z-index: 1002;
	overflow: auto;
	text-align: center;
}
</style>


<form action="admin/customer/getList?page=0" method="get">
	<span class="title_desc">&nbsp;&nbsp;
	<div class="search" style='float:none;;'>
		<div class="form-inline" style="margin-left: 18px">
		<input type="text" class="form-control search_input" name="username"
               id="username"
               value="<?php echo isset($data['username'])&&!is_null($data['username'])?$data['username']:'';?>"
               placeholder="请输入客户名称或者绑定编号">
			<button type="submit" class="btn btn-primary" id="search">搜索</button>
	</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

			<!--<button type="button" class="btn btn-primary"
				onclick="uploadCustomerInfo()">客户信息导出</button>-->
		</div>
	</div>
	<div class="clear">
		<span class="title_desc">共<?php echo $count;?>条记录</span>
	</div>

</form>

<!-- 指派二维码 遮罩层-->
<div id="bg" class="bg" style="display: none;"></div>
<div id="userDiv" class="mydiv" style="display: none;">
	<p style="text-align: center; margin-top: 3%; font-weight: bold;">客户状态更改</p>
	<p style="margin-top: 3%; text-align: center;">
		<span id='span_info'></span>
	</p>
	<div class="anniu"></div>
</div>


<script src="js/admin/jquery.datatime.min.js"></script>
<script>
	function uploadCustomerInfo(){
		var dealer_id = $('#dealer_id').val();
		var illness_type = $('#illness_type').val();
		var gender = $('#gender').val();
		var username = $('#username').val();
		location.href="<?php echo base_url('admin/customer/downloadCustomer') ?>?illness_type="+illness_type+'&gender='+gender+'&username='+username;
	}
</script>
