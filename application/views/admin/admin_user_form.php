<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/pop_box.css" />
<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/layout.css" />
<form action="admin/admin/getList?page=0" method="get">
	<div class="search">
		<div class="form-inline">
			<input type="text" class="form-control search_input"
				name="admin_name" id=""
				admin_name"" value="<?php echo isset($data['admin_name'])&&!is_null($data['admin_name'])?$data['admin_name']:'';?>"
				placeholder="请输入姓名">
			<button type="submit" class="btn btn-primary" id="search">搜索</button>
		</div>
	</div>
	<div class="clear">
		<span class="title_desc">共<?php echo $count;?>条记录</span>
	</div>

</form>
<script src="js/admin/jquery.datatime.min.js"></script>

