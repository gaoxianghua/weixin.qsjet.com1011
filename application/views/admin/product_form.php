<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/pop_box.css" />
<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/layout.css" />

<form action="admin/product/getList?page=0" method="get">
	<div class="search" style='float:none;'>
		<div class="form-inline">
		    <span class="title_desc"> 
        		注册产品类型： <select name="project_type"  id="project_type">
                    			<option value=''>不限</option>
                    			<option value='QS-M无针注射器'
                    				<?php echo isset($data['project_type'])&&$data['project_type']=='QS-M无针注射器'?'selected':''; ?>>QS-M无针注射器</option>
                    			<option value='QB-P智能药盒'
                    				<?php echo isset($data['project_type'])&&$data['project_type']=='QB-P智能药盒'?'selected':''; ?>>QB-P智能药盒</option>
                                <option value='QS-P无针注射器'
                                    <?php echo isset($data['project_type'])&&$data['project_type']=='QS-P无针注射器'?'selected':''; ?>>QS-P无针注射器</option>
                    	</select>
        	</span>&nbsp;&nbsp;&nbsp;
			<input style='width:300px;' type="text" class="form-control search_input"
				name="key" value="<?php echo isset($data['key'])&&!is_null($data['key'])?$data['key']:'';?>"
				placeholder="请输入产品编号或会员名称进行搜索">
			<button type="submit" class="btn btn-primary" id="search">搜索</button>
		</div>
	</div>
	<div class="clear">
		<span class="title_desc">共<?php echo $count;?>条记录</span>
        <!--<span class="express" style="margin-left: 6%">
            <a href="/admin/product/express"><b>快递管理</b></a>
        </span>-->
	</div>


</form>
<script src="js/admin/jquery.datatime.min.js"></script>
