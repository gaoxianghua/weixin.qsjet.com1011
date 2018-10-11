<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/pop_box.css" />
<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/layout.css" />
<form action="admin/dealer/getList" method="get">
	<div class="search" style='float:none;'>
		<div class="form-inline">
		    <span class="title_desc"> 
		          所属大区： <select name=area_id id="area_id">
                        			<option value=''>不限</option>
                        			<?php
                                        if (isset($large_area) && ! empty($large_area)) {
                                            foreach ($large_area as $k => $v) {
                                                if ($data['area_id'] == $v['id']) {
                                                    echo "<option value='" . $v['id'] . "' selected>" . $v['area_name'] . "</option>";
                                                } else {
                                                    echo "<option value='" . $v['id'] . "'>" . $v['area_name'] . "</option>";
                                                }
                                            }
                                        }
                                    ?>
        		          </select>&nbsp;&nbsp;&nbsp;
        		合同状态： <select name="contract" id="contract">
                    			<option value=''>不限</option>
                    			<option value='合同一'
                    				<?php echo isset($data['contract'])&&$data['contract']=='合同一'?'selected':''; ?>>合同一</option>
                    			<option value='合同二'
                    				<?php echo isset($data['contract'])&&$data['contract']=='合同二'?'selected':''; ?>>合同二</option>
                    	</select>
        	</span>&nbsp;&nbsp;&nbsp;
			<input type="text" class="form-control search_input"
				name="dealer_name" id=""
				dealer_name"" value="<?php echo isset($data['dealer_name'])&&!is_null($data['dealer_name'])?$data['dealer_name']:'';?>"
				placeholder="请输入经销商名称进行搜索">
			<button type="submit" class="btn btn-primary" id="search">搜索</button>
		</div>
	</div>
	<div class="clear">
		<span class="title_desc">共<?php echo $count;?>条记录</span>
	</div>

</form>
<script src="js/admin/jquery.datatime.min.js"></script>

