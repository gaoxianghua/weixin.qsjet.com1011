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
	<form id="myForm" action="<?php echo $commit_url;?>"
		onsubmit="return checkInfo()" method="post"
		enctype="multipart/form-data">
		<table>
			<tbody>
				<tr>
					<td colspan="2"><h3>视频信息</h3></td>
				</tr>
				<tr>
					<td class='td_name'>标题</td>
					<td><input class="form-control search_input" type="text" id='title' maxlength='30'
						name="title" onfocus="cencelText('span_videos_title')"
						value="<?php echo isset($result['title'])&&$result['title'] ? $result['title'] : ''; ?>"
						placeholder="请输入视频标题,2-30位中英文数字下划线" /> <span
						class="span_videos_title"></span></td>
				</tr>
				<tr>
					<td class='td_name'>链接</td>
					<td><input class="form-control search_input" type="text" id='url'
						name="url" onfocus="cencelText('span_videos_url')"
						value="<?php echo isset($result['url'])&&$result['url'] ? $result['url'] : ''; ?>"
						placeholder="请输入视频链接,以http://,https://等开头" /> <span
						class="span_videos_url"></span></td>
				</tr>
				<tr>
					<td class='td_name'>图片</td>
					<td><button style="position: relative;top: 20px;z-index: 20">上传图片</button><input class="form-control search_input" type="file"
						id="images" name="images" style="position: relative;z-index: 30;width: 72px;opacity: 0;top: -10px" 
						onfocus="cencelText('span_videos_images')" /> <span
						class="span_videos_images"> <font size='2' color='red'>请选择jpg、jpeg、png格式，小于2M图片</font></span></td>
				</tr>
				<tr>
					<td></td>
					<td><img width='100' height='100' id='img'
						src="<?php echo isset($result['images'])&& !empty($result['images'])? $this->download_url.'videos/'.$result['images'] : ''; ?>">
					</td>
				</tr>
				<tr>
					<td class='td_name'>类别</td>
					<td><select name='type_id'>
			         <?php
            if (isset($videos_type) && ! empty($videos_type)) {
                foreach ($videos_type as $k => $v) {
                    if (isset($result['type_id']) && ! empty($result['type_id']) && $result['type_id'] == $v['id']) {
                        echo "<option value='" . $v['id'] . "' selected>" . $v['name'] . "</option>";
                    } else {
                        echo "<option value='" . $v['id'] . "'>" . $v['name'] . "</option>";
                    }
                }
            }
            ?>
			     </select></td>
				</tr>
				<tr>
					<td class='td_name'>简介</td>
					<td><textarea name="remark" class="form-control search_input" maxlength='150'
							id='remark' rows="10" placeholder="限制字符数10-150个"
							onfocus="cencelText('span_videos_remark')" cols="100"><?php echo isset($result['remark'])&&$result['remark'] ? $result['remark'] : ''; ?></textarea>
						<span class="span_videos_remark"></span></td>
				</tr>
				<tr>
					<td colspan='2'><input type="hidden" name="type" value="1"> <input
						type="submit" class="submit-btn btn btn-primary" value="确定">
						 <input
						type="reset" class="submit-btn btn btn-primary" value="重置">
					</td>
				</tr>
			</tbody>
		</table>
	</form>
</div>


<script>        
var img_url = "<?php echo isset($result['images'])?$this->download_url.'videos/'.$result['images']:''?>";
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
			if(!checkText($('#title').val(),'span_videos_title',2,30)){
					return false;
            }
            if(!checkUrl($('#url').val(),'span_videos_url')){
				return false;
       		}
            if($('#img').attr('src')==''){
       			if(!uploadImg($('#images').val(),'span_videos_images')){
    				return false;
            	}
           	}else{
				if($('#img').attr('src') != img_url ){
					if(!uploadImg($('#images').val(),'span_videos_images')){
	    				return false;
	            	}
				}
            }
        	if(!checkRemark($('#remark').val(),'span_videos_remark',10,150)){
				return false;
        	}
        	
        	if( checkName() == 200){
				return true;
			}else{
				return false;
			}
	}
	//视频名称唯一性检查
    function checkName(){
    	var a = '';
    	$.ajax({
    		url:"<?php echo base_url('admin/videos/checkName') ?>",
    		data:{title:$('#title').val(),id:<?php echo isset($result['id'])&&$result['id'] ? $result['id'] : '0'; ?>},
    		async: false,
    		dataType:'json',
    		success:function(msg){
    			if(msg.result_code==400){
    				$('.span_videos_title').show();
    				$('.span_videos_title').html('<font color=red size=2>视频名称已存在</font>');
    				a =  400;
    			}else{
    				a =  200;
    			}
    		}
       	})
       	return a;
    }

</script>
