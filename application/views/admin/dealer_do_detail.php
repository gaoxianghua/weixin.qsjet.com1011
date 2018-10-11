<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/detail_info.css" />
<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/pop_box.css" />
<style>
td {
	width: 50px;
	height: 50px;
	padding-left: 20px;
}

h3 {
	padding: 20px 0;
}

table {
	width: 100%;
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
	<form action="<?php echo $commit_url;?>" method="post"
		onsubmit="return checkInfo()">
		<table>
			<tbody>
				<tr>
					<td colspan="2"><h3>经销商信息</h3></td>
				</tr>
				<tr>
					<td>经销商名称</td>
					<td><input type="text" class="form-control search_input" maxlength='45'
						id="dealer_name" name="dealer_name"
						value="<?php echo isset($result['dealer_name'])&&$result['dealer_name'] ? $result['dealer_name'] : ''; ?>"
						placeholder="请输入经销商名称,2-30位中英文数字下划线"></td>
				</tr>
				<tr>
					<td>经销商邮箱</td>
					<td><input type="text" class="form-control search_input" maxlength='30'
						id="dealer_email" name="dealer_email"
						value="<?php echo isset($result['dealer_email'])&&$result['dealer_email'] ? $result['dealer_email'] : ''; ?>"
						placeholder="请输入经销商邮箱"></td>
				</tr>

				<tr>
					<td>代理区域</td>
					<td><input type="text" class="form-control search_input" maxlength='30'
						id="agent_area" name="agent_area"
						value="<?php echo isset($result['agent_area'])&&$result['agent_area'] ? $result['agent_area'] : ''; ?>"
						placeholder="代理区域,2-30位中英文数字下划线"></td>
				</tr>

				<tr>
					<td>销售经理</td>
					<td><input type="text" class="form-control search_input" maxlength='30'
						id="project_person" name="project_person"
						value="<?php echo isset($result['project_person'])&&$result['project_person'] ? $result['project_person'] : ''; ?>"
						placeholder="请输入销售经理,2-30位中英文数字下划线"></td>
				</tr>
				<tr>
					<td>销售经理电话</td>
					<td><input type="text" class="form-control search_input" maxlength='11'
						id="project_mobile" name="project_mobile"
						value="<?php echo isset($result['project_mobile'])&&$result['project_mobile'] ? $result['project_mobile'] : ''; ?>"
						placeholder="请输入销售经理电话"></td>
				</tr>
				<tr>
					<td>经销商座机</td>
					<td><input type="text" class="form-control search_input" maxlength='13'
						id="dealer_tell" name="dealer_tell"
						value="<?php echo isset($result['dealer_tell'])&&$result['dealer_tell'] ? $result['dealer_tell'] : ''; ?>"
						placeholder="请输入经销商座机,如：010-88888888"></td>
				</tr>

				<tr>
					<td>经销商地址</td>
					<td><input type="text" class="form-control search_input" maxlength='30'
						id="dealer_address" name="dealer_address"
						value="<?php echo isset($result['dealer_address'])&&$result['dealer_address'] ? $result['dealer_address'] : ''; ?>"
						placeholder="请输入经销地址,2-30位中英文数字下划线"></td>
				</tr>

				<tr>
					<td><font color="red" size="">*经销商登录账号为"经销商邮箱"，默认密码为123456。</font></td>
					<td><div style="width: 100%;height: 50px;"></div></td>
				</tr>
				<tr>
					<td colspan='2'><input type="submit"
						class="submit-btn btn btn-primary" value="确认"> <input
						type="button" onclick="resets()"
						class="submit-btn btn btn-primary" value="重置"></td>
				</tr>
			</tbody>
		</table>
	</form>
</div>
<script>         	

            
	$(function(){
		selectArea($('#area_id'))
	})		         
 	function  selectArea(obj){
			var area_id = $(obj).val();
			$.getJSON("<?php echo base_url('admin/dealer/getAreaOne') ;?>",{area_id:area_id},function(msg){
				if(msg.result_code = 200){
					$('#area_person').val(msg.info.area_person);
					return false;
				}
				if(msg.result_code = 400){
					return false;
				}	
			});
     }
	 
	function checkInfo(){
    		var REG_NAMES = /^[\u4e00-\u9fa5a-zA-Z0-9_\s\·]{2,45}$/;
    		var REG_EMAIL =	/^([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+@([a-zA-Z0-9]+[_|\_|\.]?)*[a-zA-Z0-9]+\.[a-zA-Z]{2,3}$/;	
    		var REG_MOBILE =/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{9}|147[0-9]{8}|17[07]{1}[0-9]{8}$/;
		
			var dealer_name = $("#dealer_name").val();
			var dealer_email = $("#dealer_email").val();
			var agent_area = $("#agent_area").val();
			var project_person = $("#project_person").val();
			var project_mobile = $("#project_mobile").val();
			var dealer_tell = $("#dealer_tell").val();
			var dealer_address = $("#dealer_address").val();
			if(!REG_NAMES.test(dealer_name)){ 
					alert('经销商名称输入有误');
					return false;
			}

			if(!REG_EMAIL.test(dealer_email)){ 
				alert('经销商邮箱输入有误');
				return false;
			}
			if(!REG_NAMES.test(agent_area)){ 
				alert('代理区域输入有误');
				return false;
			}

			if(!REG_NAMES.test(project_person)){ 
				alert('销售经理输入有误');
				return false;
			}

			if(!REG_MOBILE.test(project_mobile)){ 
				alert('销售经理电话输入有误');
				return false;
			}
			
			var REG_ADDRESS = /^[\w\u4e00-\u9fa5a-zA-Z0-9_\s\·]{2,35}$/;
			if(!REG_ADDRESS .test(dealer_address)){ 
				alert('经销商地址输入有误');
				return false;
			}
			return true;
	}

	function  resets(){
		history.go(0);
 }

</script>





