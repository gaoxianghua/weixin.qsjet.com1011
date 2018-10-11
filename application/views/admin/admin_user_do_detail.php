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
		onsubmit="return checkInfo()">
		<table>
			<tbody>
				<tr>
					<td colspan="2"><h3>账号信息</h3></td>
				</tr>
				<tr>
					<td class='td_name'>姓名</td>
					<td><input type="text" name="admin_name" id="admin_name"
						class="form-control search_input" maxlength="15"
						placeholder="请输入管理员名称,2-15位中英文数字下划线"
						value="<?php echo isset($result['admin_name']) ? $result['admin_name'] : '';?>" />
					</td>
				</tr>

				<tr>
					<td class='td_name'>账号</td>
					<td><input type="text" name="account" id="account" maxlength="25"
						class="form-control search_input" placeholder="请使用邮箱作为账号"
						value="<?php echo isset($result['account']) ? $result['account'] : '';?>" />
					</td>
				</tr>
				<tr>
					<td></td>
					<td>默认密码为123456</td>
				</tr>
				<tr>
					<td class='td_name'>权限</td>
					<td>
			     <?php
        foreach ($all_permission as $k => $v) {
            if( $k%5==0 ){
                echo "<br />";
            }
            if (isset($result['permissions']) && in_array($v['id'], $result['permissions'])) {
                ?>
			          <?php echo $v['name']; ?><input type="checkbox"
						class="input-item" onclick="isChecked(this)" name="permissions[]"
						checked value="<?php echo $v['id']; ?>">
			                 &nbsp;&nbsp;&nbsp;
			     <?php
            } else {
                ?>
			                 <?php echo $v['name']; ?><input type="checkbox"
						class="input-item" onclick="isChecked(this)" name="permissions[]"
						value="<?php echo $v['id']; ?>">
			                 &nbsp;&nbsp;&nbsp;
			     <?php
            }
        }
        ?>
			</td>
				</tr>
				<tr>
					<td colspan='2'><input type="hidden" name="type" value="1"> <input
						type="submit" class="submit-btn btn btn-primary" value="确定"> <input
						type="reset" class="submit-btn btn btn-primary" value="重置">
			         <?php if(isset($result)&!empty($result)&isset($resetPassword)){?><input
						type="button" onclick="resetPassword(<?php echo $result['id'] ?>)"
						class="submit-btn btn btn-primary" value="重置密码"><?php } ?>
		    </td>
				</tr>
				<tr>
					<td colspan='2'></td>
				</tr>
				<tr>
					<td colspan='2'></td>
				</tr>
				<tr>
					<td colspan='2'></td>
				</tr>
			</tbody>
		</table>
	</form>
</div>

<script>


			     
function checkInfo(){
	var account = $("#account").val();
    var admin_name = $("#admin_name").val();
    var account = $("#account").val();

    var REG_ACCOUNT = /\w+((-w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+/;
    var REG_NAMES = /^[\u4e00-\u9fa5a-zA-Z0-9_]{2,15}$/;
    
    if(!REG_ACCOUNT.test(account)){
        alert('账号输入有误');
        return false;
    }
    if(!REG_NAMES.test(admin_name)){
        alert('姓名输入有误');
        return false;
    }

    if(!isChecked()){
        alert('请选择权限');
        return false;
    }
    
    return true;
}

function isChecked(){
	if($(".input-item").is(":checked")){
		return true;
	}else{
		return false;
	}
}

</script>




