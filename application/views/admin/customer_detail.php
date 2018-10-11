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
				<td colspan="2"><h3>客户基本信息</h3></td>
			</tr>
			<tr>
				<td>姓名</td>
				<td><?php echo isset($result['username'])&&$result['username'] ? $result['username'] : ''; ?></td>
			</tr>
			<tr>
				<td>绑定编号</td>
				<td><?php echo isset($result['doctor_name'])&&$result['doctor_name'] ? $result['doctor_name']:''; ?></td>
			</tr>

			<tr>
				<td>手机号码</td>
				<td><?php echo isset($result['mobile'])&&$result['mobile'] ? $result['mobile']: ''; ?></td>
			</tr>
			
			<tr>
				<td>注册时间</td>
				<td><?php echo isset($result['add_time'])&&$result['add_time'] ? $result['add_time'] : ''; ?></td>
			</tr>
		</tbody>
	</table>

</div>
