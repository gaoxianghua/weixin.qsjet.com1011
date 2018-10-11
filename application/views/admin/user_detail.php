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
				<td colspan="2"><h3>会员基本信息</h3></td>
			</tr>
			<tr>
				<td>姓名</td>
				<td><?php echo isset($result['username'])&&$result['username'] ? $result['username'] : ''; ?></td>
			</tr>
			<tr>
				<td>性别</td>
				<td><?php echo isset($result['gender'])&&$result['gender'] ? $result['gender'] : ''; ?></td>
			</tr>
			<tr>
				<td>手机号码</td>
				<td><?php echo isset($result['account'])&&$result['account'] ? $result['account']: ''; ?></td>
			</tr>
			<tr>
				<td>注射剂量</td>
				<td><?php echo isset($result['injected_dose'])&&$result['injected_dose'] ? $result['injected_dose'] : '无'; ?></td>
			</tr>
			<tr>
				<td>注射时间</td>
				<td><?php echo isset($result['medical_history'])&&$result['medical_history'] ? $result['medical_history'] : '无'; ?></td>
			</tr>
			<tr>
				<td>目前注射胰岛素</td>
				<td><?php echo isset($result['insulin'])&&$result['insulin'] ? $result['insulin'] : '无'; ?></td>
			</tr>
			<tr>
				<td>硬结</td>
				<td><?php echo isset($result['medical_history'])&&$result['medical_history']==1 ? '有' : '无'; ?></td>
			</tr>
			<tr>
				<td>地址</td>
				<td><?php echo isset($result['address'])&&$result['address'] ? $result['address'] : ''; ?></td>
			</tr>
			<tr>
				<td>注册时间</td>
				<td><?php echo isset($result['add_time'])&&$result['add_time'] ? $result['add_time'] : ''; ?></td>
			</tr>
		</tbody>
	</table>

</div>
