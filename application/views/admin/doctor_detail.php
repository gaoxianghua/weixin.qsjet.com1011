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
	<table>
		<tbody>
			<tr>
				<td colspan="2"><h3>绑定编号基本信息</h3></td>
			</tr>
			<tr>
				<td>绑定编号</td>
				<td><?php echo isset($result['doctor_name'])&&$result['doctor_name'] ? $result['doctor_name'] : ''; ?></td>
			</tr>

			<tr>
				<td>绑定日期</td>
				<td><?php echo isset($result['add_time'])&&$result['add_time'] ? $result['add_time']:''; ?></td>
			</tr>
			<tr>
				<td>推荐客户数</td>
				<td><?php echo isset($result['recommend'])&&$result['recommend'] ? $result['recommend'] : '0'; ?></td>
			</tr>
			<tr>
				<td>代金券使用数</td>
				<td><?php echo isset($result['deal'])&&$result['deal_m'] ? $result['deal_m'] : '0'; ?></td>
			</tr>
			<tr>
				<td>所属经销商</td>
				<td><?php echo isset($result['dealer_name'])&&$result['dealer_name'] ? $result['dealer_name'] : ''; ?></td>
			</tr>
			<tr>
				<td>二维码ID</td>
				<td><?php echo isset($result['qc_code'])&&$result['qc_code'] ? $result['qc_code'] : ''; ?></td>
			</tr>
		</tbody>
	</table>

</div>
<script>
	//编码
			function html_encode(str){
			    var s = "";   
				  if (str.length == 0) return "";   
				  s = str.replace(/&/g, "&gt;");   
				  s = s.replace(/</g, "&lt;");   
				  s = s.replace(/>/g, "&gt;");   
				  s = s.replace(/ /g, "&nbsp;");   
				  s = s.replace(/\'/g, "&#39;");   
				  s = s.replace(/\"/g, "&quot;");   
				  s = s.replace(/\n/g, "<br>");   
				  return s;   
			}
			
			$(document).ready(function(){
				$(".server-addr").html(html_encode('<?php echo isset($result['doctor']['vill'])&&$result['doctor']['vill'] ? $result['doctor']['vill']:""; ?>')+'&nbsp;&nbsp;'+html_encode('<?php echo isset($result['doctor']['door_num'])&&$result['doctor']['door_num'] ? $result['doctor']['door_num']:""; ?>'));	
			});
</script>
