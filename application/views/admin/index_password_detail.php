<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/detail_info.css" />
<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/pop_box.css" />
<meta charset="utf-8">
<div class="userInfo">

	<ul class="info-list">
		<li><font color="red" size="2">密码为6-15位字母、数字、下划线构成</font></li>
		<li>原密码：<input type="password" name="pwd" id="pwd" /></li>
		<li>新密码：<input type="password" name="newPwd" id="newPwd" /></li>
		<li>再次确认：<input type="password" name="newPws" id="newPws" />&nbsp;<font
			color="red"><span id="cue"></span></font></li>
		<li><button class="submit-btn btn btn-primary" onclick="savePwd()">确定</button></li>
	</ul>
</div>
<script>
    function savePwd(){
            var pwd = $('#pwd').val();
            var newPwd = $('#newPwd').val();
            var newPws = $('#newPws').val();
            if( pwd=='' || pwd=='' || pwd=='' ){
                    alert('密码不能为空');
                    return false;
            }
            if( newPwd != newPws ){
                    alert('两次密码输入不一致');
                    return false;
            }

            if( !( /^[0-9a-zA-Z_]{6,15}$/.test( pwd ) ) || !( /^[0-9a-zA-Z_]{6,15}$/.test( newPws ) ) ){
                    alert('密码格式不正确');
                    return false;
            }
            if( window.confirm( '确认修改？' ) ){
                   $.post('admin/index/doPassword',{pwd:pwd,newPwd:newPwd},function( msg ){
                                if( msg.result_code == 200 ){
                                        alert( msg.info );
                                        location.href='admin';
                                }
                                
                                if( msg.result_code == 400 ){
                                        alert( msg.error_msg );
                                }
                   },'json');
            }
            return false;
    }
</script>