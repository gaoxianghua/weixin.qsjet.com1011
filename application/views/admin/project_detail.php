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
		onsubmit="return checkInfo()" enctype="multipart/form-data">
		<table>
			<tbody>
				<tr>
					<td colspan="2"><h3>产品信息</h3></td>
				</tr>
				<tr>
					<td class='td_name'>产品名称</td>
					<td><input class="form-control search_input" type="text" id='title' maxlength='30'
						name="name" onfocus="cencelText('span_project_title')"
						value="<?php echo isset($result['name'])&&$result['name'] ? $result['name'] : ''; ?>"
						placeholder="请输入产品名称,2-30位中文、英文、数字、下划线" /> <span
						class="span_project_title"></span></td>
				</tr>
				<tr>
					<td class='td_name'>产品链接</td>
					<td><input class="form-control search_input" type="text" id="url"
						onfocus="cencelText('span_project_url')" name="project_url"
						value="<?php echo isset($result['project_url'])&&$result['project_url'] ? $result['project_url'] : ''; ?>"
						placeholder="请输入产品链接，请输入视频链接,以http://,https://等开头" /> <span
						class="span_project_url"></span></td>

				</tr>
				<tr>
					<td class='td_name'>产品图片</td>
					<td><button style="position: relative;top: 20px;z-index: 20">上传图片</button><input class="form-control search_input" style="position: relative;z-index: 30;width: 72px;opacity: 0;top: -10px" type="file"
						id="images" onfocus="cencelText('span_project_images')"
						name="images" /> <span class="span_project_images"> <font size='2'
							color='red'>请选择jpg、jpeg、png格式，小于2M图片</font></span></td>
				</tr>
				<tr>
					<td></td>
					<td><img width='100' height='100' id='img' 
						src="<?php echo isset($result['images'])&& !empty($result['images'])? $this->download_url.'project/'.$result['images'] : ''; ?>">
					</td>
				</tr>
				<tr>
				<td class='td_name'>产品简介</td>
				<td><textarea name="remark" id='remark'
						class="form-control search_input"
						onfocus="cencelText('span_project_remark')" rows="10" cols="100" maxlength='150'
						 placeholder="限制字符数10-150个"><?php echo isset($result['remark'])&&$result['remark'] ? $result['remark'] : ''; ?></textarea>
					<span class="span_project_remark"></span></td>
				</tr>
				<tr>
					<td colspan='2'><input type="submit"
						class="submit-btn btn btn-primary" value="确定">
						 <input
						type="reset" class="submit-btn btn btn-primary" value="重置">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>


<script>       
var img_url = "<?php echo isset($result['images'])?$this->download_url.'project/'.$result['images']:''?>";
$("#images").change(function(){
    var objUrl = getObjectURL(this.files[0]) ;
    if (objUrl!=undefined){
    	$("#img").show();
        $("#img").attr("src", objUrl) ;
    }else{
    	$("#img").hide();
    }
}) ;       


	function checkInfo(){
    		if(!checkText($('#title').val(),'span_project_title',2,30)){
				return false;
            }
    		if(!checkUrl($('#url').val(),'span_project_url')){
				return false;
       		}
       		if($('#img').attr('src')==''){
       			if(!uploadImg($('#images').val(),'span_project_images')){
    				return false;
            	}
           	}else{
				if($('#img').attr('src') != img_url ){
					if(!uploadImg($('#images').val(),'span_project_images')){
	    				return false;
	            	}
				}
            }
       		if(!checkRemark($('#remark').val(),'span_project_remark',10,150)){
				return false;
        	}
			if( checkName() == 200){
				return true;
			}else{
				return false;
			}
	}

	function checkName(){
    	var a = '';
    	$.ajax({
    		url:"<?php echo base_url('admin/project/checkName') ?>",
    		data:{name:$('#title').val(),id:<?php echo isset($result['id'])&&$result['id'] ? $result['id'] : 0; ?>},
    		async: false,
    		dataType:'json',
    		success:function(msg){
    			if(msg.result_code==400){
    				$('.span_project_title').show();
    				$('.span_project_title').html('<font color=red size=2>产品名称已存在</font>');
    				a =  400;
    			}else{
    				a =  200;
    			}
    		}
       	})
       	return a;
    }
		
</script>
