<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/detail_info.css" />
<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/pop_box.css" />
<style>
td {
	height: 50px;
	padding-left: 20px;
}

h3 {
	padding: 20px 0;
}

table {
	width: 70%;
	border: 1px solid #ccd1d9;
}


.eval-box {
	border: none;
	outline: none;
	width: 100%;
	height: 100%;
	overflow-y: visible;
	resize: none;
	padding-right: 20px;
}
</style>
<meta charset="utf-8">
<div class="userInfo">
	<form id="myForm" action="<?php echo $commit_url;?>" method="post"
		onsubmit="return checkInfo()" enctype="multipart/form-data">
		<table>
			<tbody>
				<tr>
					<td colspan="2"><h3>绑定</h3></td>
				</tr>
				<tr>
					<td>二维码ID</td>
					<td><input class="form-control search_input" type="text"
						name="qc_code_name"
						value="<?php echo isset($result['qc_code_name'])&&$result['qc_code_name'] ? $result['qc_code_name'] : ''; ?>"
						readonly /> <span class="span_qc_code_name"></span></td>
				</tr>

				<tr>
					<td>编号</td>
					<td><input class="form-control search_input" type="text" readonly="readonly"
                               name="doctor_name" value=" <?php echo $result['doctor_name']; ?>"
			     </td>
                    <input name="position" type="hidden" value=" <?php echo $result['position']; ?>" />
				</tr>

				<tr>
					<td>初始密码</td>
					<td><input class="form-control search_input" type="password" id="code" maxlength='15'
						placeholder="请输入初始密码,6-15位中英文数字"
						onfocus="cencelText('span_qc_code')" name="code" /> <span
						class="span_qc_code"></span></td>
				</tr>
				<tr>
					<td colspan='2' style="text-align: center;"<input type="hidden" name="type" value="1"> <input
						type="submit" class="submit-btn btn btn-primary" value="确定"></td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<script>         	
	function checkInfo(){
    		if(!checkText($('#doctor_name').val(),'span_qc_doctor_name',2,15)){
				return false;
            }
    		if(!checkText($('#department').val(),'span_qc_department',2,15)){
				return false;
            }
    		if(!checkText($('#hospital').val(),'span_qc_hospital',2,15)){
				return false;
            }
    		if(!checkText($('#code').val(),'span_qc_code',6,15)){
				return false;
            }
	}

</script>