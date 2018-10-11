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
	<table>
		<tbody>
			<tr>
				<td colspan="2"><h3>经销商信息</h3></td>
			</tr>
			<tr>
				<td>经销商名称</td>
				<td><?php echo isset($result['dealer_name'])&&$result['dealer_name'] ? $result['dealer_name'] : ''; ?></td>
			</tr>
			<tr>
				<td>经销商邮箱</td>
				<td><?php echo isset($result['dealer_email'])&&$result['dealer_email'] ? $result['dealer_email'] : ''; ?></td>
			</tr>

			<tr>
				<td>代理区域</td>
				<td><?php echo isset($result['agent_area'])&&$result['agent_area'] ? $result['agent_area']:''; ?></td>
			</tr>

			<tr>
				<td>销售经理</td>
				<td><?php echo isset($result['project_person'])&&$result['project_person'] ? $result['project_person'] : '0'; ?></td>
			</tr>
			<tr>
				<td>销售经理电话</td>
				<td><?php echo isset($result['project_mobile'])&&$result['project_mobile'] ? $result['project_mobile'] : ''; ?></td>
			</tr>
			<tr>
				<td>经销商座机</td>
				<td><?php echo isset($result['dealer_tell'])&&$result['dealer_tell'] ? $result['dealer_tell'] : ''; ?></td>
			</tr>

			<tr>
				<td>经销商地址</td>
				<td><?php echo isset($result['dealer_address'])&&$result['dealer_address'] ? $result['dealer_address'] : ''; ?></td>
			</tr>

			<tr>
				<td>二维码数</td>
				<td><?php echo isset($result['qc_total'])&&$result['qc_total'] ? $result['qc_total'] : '0'; ?></td>
			</tr>
			<tr>
				<td>绑定数</td>
				<td><?php echo isset($result['doctor_total'])&&$result['doctor_total'] ? $result['doctor_total'] : '0'; ?></td>
			</tr>
			<tr>
				<td>客户数</td>
				<td><?php echo isset($result['user_total'])&&$result['user_total'] ? $result['user_total'] : '0'; ?></td>
			</tr>
		</tbody>
	</table>

</div>
