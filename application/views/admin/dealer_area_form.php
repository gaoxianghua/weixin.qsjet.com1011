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
	position: fixed !important; /*FF IE7*/
}
#area_name1,#area_name,#area_person1,#area_person{
	border: 1px solid #999;
	padding-left: 10px;
	border-radius: 3px;
	line-height:30px;
	width: 60%; height: 30px;
}
#area_name1:focus,#area_name:focus,#area_person1:focus,#area_person:focus{
	border: 1px solid #43A1E7;
}
.mydiv {
	display: none;
	position: fixed;
	top: 25%;
	left: 35%;
	width: 480px;
	text-align: center;
	background-color: #FFF;
	z-index: 1002;
	overflow: auto;
}
</style>
<form action="admin/dealer/areaList" method="get">
	<div class="search">
		<div class="form-inline">
			<input type="text" class="form-control search_input"
				name="large_area" id="large_area" value="<?php echo isset($large_area)&&!is_null($large_area)?$large_area:'';?>"
				placeholder="请输入大区名称进行搜索">
			<button type="submit" class="btn btn-primary" id="search">搜索</button>
			&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' class="btn btn-primary"
				onclick="areaAdd()" value='新建大区' />
		</div>
	</div>
	<div class="clear">
		<span class="title_desc">共<?php echo $count;?>条记录</span>
	</div>

</form>
<script src="js/admin/jquery.datatime.min.js"></script>

<!-- 新建大区 遮罩层-->
<div id="bg" class="bg" style="display: none;"></div>
<div id="areaDiv" class="mydiv" style="display: none;">
	<p class="area_title" style="text-align: center; margin-top: 3%; font-weight: bold;"></p>
	<p style="margin-top: 3%; text-align: center;">
		&nbsp;&nbsp; 大区名称：<input type='text' name="area_name" maxlength='15'
			placeholder="请输入2-15位中英文数字下划线" id="area_name"
		/><br /> 
		大区负责人：<input type='text'
			name="area_person" placeholder="请输入2-15位中英文数字下划线"  maxlength='15'
			id="area_person"  />
	</p>
	<div class="anniu">
		<button type="button" class="btn btn-primary" id="area_commit">确认</button>
		&nbsp;&nbsp;
		<button type="button" class="btn btn-primary" id="area_cancel"
			onclick="closeAreadiv()">取消</button>
	</div>
</div>

<div id="areaDiv1" class="mydiv" style="display: none;">
	<p class="area_title1" style="text-align: center; margin-top: 3%; font-weight: bold;"></p>
	<p style="margin-top: 3%; text-align: center;">
		&nbsp;&nbsp; 大区名称：<input type='text' name="area_name" maxlength='15'
			placeholder="请输入2-15位中英文数字下划线" id="area_name1"
			 /><br /> 
		大区负责人：<input type='text'
			name="area_person" placeholder="请输入2-15位中英文数字下划线"  maxlength='15'
			id="area_person1" style="" />
	</p>
	<div class="anniu1">
		<button type="button" class="btn btn-primary" id="area_commit1">确认</button>
		&nbsp;&nbsp;
		<button type="button" class="btn btn-primary" id="area_cancel1"
			onclick="closeAreadiv()">取消</button>
	</div>
</div>
