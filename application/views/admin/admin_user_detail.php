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
.td_name {
	width: 100px;;
}

img {
	width: 100px;
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
				<td colspan="2"><h3>医生基本信息</h3></td>
			</tr>
			<tr>
				<td class='td_name'>姓名</td>
				<td><?php echo isset($result['username'])&&$result['username'] ? $result['username'] : ''; ?></td>
			</tr>
			<tr>
				<td class='td_name'>性别</td>
				<td class='td_name'><?php echo isset($result['gender'])&&$result['gender'] ? $result['gender'] : ''; ?></td>
			</tr>
			<tr>
				<td class='td_name'>手机号码</td>
				<td class='td_name'><?php echo isset($result['mobile'])&&$result['mobile'] ? $result['mobile']: ''; ?></td>
			</tr>
			<tr>
				<td class='td_name'>地址</td>
				<td class='td_name'><?php echo isset($result['address'])&&$result['address'] ? $result['address'] : ''; ?></td>
			</tr>
			<tr>
				<td class='td_name'>推荐医生</td>
				<td><?php echo isset($result['doctor_name'])&&$result['doctor_name'] ? $result['doctor_name']:''; ?></td>
			</tr>
			<tr>
				<td class='td_name'>糖尿病类型</td>
				<td><?php echo isset($result['illness_type'])&&$result['illness_type'] ? $result['illness_type'] : '0'; ?></td>
			</tr>
			<tr>
				<td class='td_name'>病史</td>
				<td><?php echo isset($result['medical_history'])&&$result['medical_history'] ? $result['medical_history'] : '0'; ?></td>
			</tr>
			<tr>
				<td class='td_name'>目前注射胰岛素</td>
				<td><?php echo isset($result['insulin'])&&$result['insulin'] ? $result['insulin'] : ''; ?></td>
			</tr>
			<tr>
				<td class='td_name'>用户状态</td>
				<td><?php
    if (($result['status']) && $result['status']) {
        switch ($result['status']) {
            case 1:
                echo '未成交';
                break;
            case 2:
                echo '已成交';
                break;
            case 3:
                echo '审核未通过';
                break;
            case 4:
                echo '审核已通过';
                break;
        }
    }
    ?></td>
			</tr>
		</tbody>
	</table>

</div>
