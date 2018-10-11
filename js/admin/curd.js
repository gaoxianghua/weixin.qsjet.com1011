
//医师解绑
function unbundling ( doctor_id,qc_code ){
	if( confirm('您将要解绑此二维码，所有绑定信息将会被清除，请确认此操作！？') ){
		    $.getJSON("admin/doctor/unbundling",{'doctor_id':doctor_id,qc_code:qc_code},function(msg){
		    	if( msg.result_code == 200 ){
		    		alert(msg.info)
		    		history.go(0);
		    	}
		    	if( msg.result_code == 400 ){
		    		alert(msg.error_msg)
		    		history.go(0);
		    	}
		    })
	}
}


//用户解绑
function customer_unbundling ( doctor_id,qc_code ){
	if( confirm('您将要解绑此二维码，该客户所有绑定信息将会被清除，请确认此操作！？') ){
		    $.getJSON("admin/customer/unbundling",{'doctor_id':doctor_id,qc_code:qc_code},function(msg){
		    	if( msg.result_code == 200 ){
		    		alert(msg.info)
		    		history.go(0);
		    	}
		    	if( msg.result_code == 400 ){
		    		alert(msg.error_msg)
		    		history.go(0);
		    	}
		    })
	}
} 
//结算
function settle(doctor_id) {
    if( confirm('您将要结算此编号，该编号所有绑定客户将会被清除，请确认此操作！？') ) {
        $.getJSON("admin/doctor/settle", {'doctor_id': doctor_id}, function (msg) {
            if (msg.result_code == 200) {
                alert(msg.info)
                history.go(0);
            }
            if (msg.result_code == 400) {
                alert(msg.error_msg)
                history.go(0);
            }
        })
    }
}

//经销商编辑
function dealerEdit ( id ){
	location.href="admin/dealer/dealerEdit?id="+id;
} 

//经销商编辑
function dealerDelete ( id){
	if( confirm('确认删除？') ){
		$.getJSON('admin/dealer/delete',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 


//注册产品列表删除
function productDelete ( id){
	if( confirm('确认删除？') ){
		$.getJSON('admin/product/delete',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 

//大区删除
function areaDelete ( id ){
	if( confirm('你将要删除此大区？,此操作会影响经销商信息完整性，请确认是否删除？') ){
		$.getJSON('admin/dealer/areaDelete',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 


//大区删除
function adminDelete ( id ){
	if( confirm('确认删除？') ){
		$.getJSON('admin/admin/delete',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 

//文章删除
function articleDelete ( id ){
	if( confirm('确认删除？') ){
		$.getJSON('admin/article/delete',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 


//视频删除
function videosDelete ( id ){
	if( confirm('确认删除？') ){
		$.getJSON('admin/videos/delete',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 

//二维码注销
function cancel ( id ){
	if( confirm('您将要清除此二维码，所有包含信息将会被清除，请确认此操作！') ){
		$.getJSON('admin/qc_code/cancel',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 

//二维码 解绑
function qc_unbinding ( id ){
	if( confirm('您将要注销此二维码，所有包含医生将会被清除，请确认此操作！？') ){
		$.getJSON('admin/qc_code/unbinding',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 


//二维码 指派
function assign ( id ){
	showdiv();
	$.getJSON('admin/qc_code/getDealerAllJson',function(msg){
		if( msg.result_code == 200 ){
			var str = "";
			for( var i in msg.info ){
				str += " <option value="+msg.info[i].id+" >"+msg.info[i].dealer_name+"</option> ";
			}
			$('#dealer_id').html(str);
		}
		
		if( msg.result_code == 400 ){
			alert('经销商信息获取失败');
			closediv();
		}
	})
	$('#assgin_commit').click(function(){
		assgin_commit(id)
	})
	
	$('#assgin_cancel').click(function(){
		assgin_cancel(id)
	})
} 

//二维码 指派  确认
function assgin_commit ( qc_code_id ){
	var dealer_id = $('#dealer_id').val();
	if( confirm('确认指派？') ){
		$.getJSON('admin/qc_code/doAssign',{qc_code_id:qc_code_id,dealer_id:dealer_id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
	closediv();
} 

//二维码 指派  取消
function assgin_cancel ( id ){
	closediv();
} 

//遮罩层
function showdiv(){
    document.getElementById('popDiv').style.display='block';
    document.getElementById('bg').style.display='block';
}

function closediv(){
    document.getElementById('bg').style.display='none';
    document.getElementById('popDiv').style.display='none';
}


/*
 * 	生产二维码  数量
 */
function generateCode(){
	showNumdiv();
	$('#num_commit').click(function(){
		var code_num = $('#code_num').val();
		$.getJSON('admin/qc_code/generateCode',{code_num:code_num},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	})
}

//遮罩层
function showNumdiv(){
    document.getElementById('numDiv').style.display='block';
    document.getElementById('bg').style.display='block';
}

function closeNumdiv(){
    document.getElementById('bg').style.display='none';
    document.getElementById('numDiv').style.display='none';
}




/*
 * 	新加大区
 */
function areaAdd(){
	showAreadiv();
	$('.area_title').html('新建大区');
	$('#area_commit').click(function(){
		var area_name = $('#area_name').val();
		var area_person = $('#area_person').val();
		var REG_NAMES = /^[\u4e00-\u9fa5a-zA-Z0-9_\s]{2,15}$/;
		if(!REG_NAMES.test(area_name)){ 
			alert('大区名称输入有误');
			return false;
		}
		if(!REG_NAMES.test(area_person)){ 
			alert('大区负责人名称输入有误');
			return false;
		}
		$.getJSON('admin/dealer/doAddArea',{area_name:area_name,area_person:area_person},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
	    	$('#area_name').val();
			$('#area_person').val();
			return false;
		})
	});
}

function areaSave(id,$this){
		showAreadiv1();
		$('.area_title1').html('编辑大区信息');
		var areaName=$($this).parent().parent("tr").children('td').eq(1).text();
		var areaPerson=$($this).parent().parent("tr").children('td').eq(2).text();
		$("#area_name1").val(areaName);
		$('#area_person1').val(areaPerson);

		$('#area_commit1').click(function(){
			var area_name = $('#area_name1').val();
			var area_person = $('#area_person1').val();
			var REG_NAMES = /^[\u4e00-\u9fa5a-zA-Z0-9_\s]{2,15}$/;
			if(!REG_NAMES.test(area_name)){ 
				alert('大区名称输入有误');
				return false;
			}
			if(!REG_NAMES.test(area_person)){ 
				alert('大区负责人名称输入有误');
				return false;
			}
			$.getJSON('admin/dealer/doSaveArea',{id:id,area_name:area_name,area_person:area_person},function(msg){
				if( msg.result_code == 200 ){
		    		alert(msg.info)
		    		history.go(0);
		    	}
		    	if( msg.result_code == 400 ){
		    		alert(msg.error_msg)
		    		history.go(0);
		    	}
			})
			return false;
		});
}	


//遮罩层
function showAreadiv(){
    document.getElementById('areaDiv').style.display='block';
    document.getElementById('bg').style.display='block';
}
function showAreadiv1(){
    document.getElementById('areaDiv1').style.display='block';
    document.getElementById('bg').style.display='block';
}

function closeAreadiv(){
	history.go(0);
    document.getElementById('bg').style.display='none';
    document.getElementById('areaDiv').style.display='none';
    document.getElementById('areaDiv1').style.display='none';
}


function selectAll(){  
    if($(".select0").is(":checked")) {  
        $("input[name=select]").prop("checked", true);
    } else {  
        $("input[name=select]").prop("checked", false);  
    }  
}


$(document).on('click',"input[name=select]",function(){
	var a = 0;
	for(var i = 0;i<$("input[name=select]").length;i++){
		if($("input[name=select]").eq(i).prop("checked")==false){
			a = 1;
		}
	}	
	if(a == 1){
		$(".select0").prop("checked", false);
	}else{
		$(".select0").prop("checked", true);
	}
})



function stamp(commit_url){  
		var str = '';
        $("[name='select']").each(function(){ 
        	if($(this).prop('checked') == true){
        		str += $(this).val() + ','; 
        	}
		}) 
		if( str == '' ){
			alert('请选择需要打印的二维码');
			return false;
		}
        document.write("<form action="+commit_url+" method=post name=formx1 style='display:none'>");
        document.write("<input type=hidden name='codeId' value='"+str+"' >");
        document.write("</form>");
        document.formx1.submit();
}



//用户审核
function customerCheck ( id , user_type ){
		showUserdiv(user_type);
		if(user_type == '1'){
			$('#span_info').html('您将要改变此用户的状态，请确认此用户是否通过审核？');
			var str = "<button type=\"button\" class=\"btn btn-primary\" id=\"user_commit\" onclick='handlCustomerCheck("+id+",1)' >审核不通过</button>&nbsp;&nbsp;&nbsp;&nbsp;";
			str += "<button type=\"button\" class=\"btn btn-primary\" id=\"user_commit\" onclick='handlCustomerCheck("+id+",2)' >审核通过</button>&nbsp;&nbsp;&nbsp;&nbsp;";
			str += "<button type=\"button\" class=\"btn btn-primary\" id=\"user_commit\" onclick='closeUserdiv()' >取消</button>";
		}
		if(user_type == '2'){
			$('#span_info').html('您将要改变此用户的状态，请确认此用户是否已经购买过产品？');
			var str = "<button type=\"button\" class=\"btn btn-primary\" id=\"user_commit\" onclick='handlCustomerCheck("+id+",4)' >是，已经购买过</button>&nbsp;&nbsp;&nbsp;&nbsp;";
			str += "<button type=\"button\" class=\"btn btn-primary\" id=\"user_commit\" onclick='closeUserdiv()' >还没购买</button>";
		}
		
		$('.anniu').html(str)
}

//用户操作
function handlCustomerCheck ( id , user_status ){
	$.getJSON('admin/customer/doStatus',{id:id,status:user_status},function(msg){
		if( msg.result_code == 200 ){
    		alert(msg.info)
    		history.go(0);
    	}
    	if( msg.result_code == 400 ){
    		alert(msg.error_msg)
    		history.go(0);
    	}
	})
}

//用户审核 遮罩层
function showUserdiv(user_type){
	
    document.getElementById('userDiv').style.display='block';
    document.getElementById('bg').style.display='block';
}

function closeUserdiv(){
    document.getElementById('bg').style.display='none';
    document.getElementById('userDiv').style.display='none';
}



//产品删除
function projectDelete ( id ){
	if( confirm('确认删除？') ){
		$.getJSON('admin/project/delete',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 

//重置密码
function resetPassword ( id ){
	if( confirm('确认重置密码？') ){
		$.getJSON('admin/admin/resetPassword',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 

//打印预览
function printCode ( qc_name ){
	showCodediv();
	$('#img').attr('src',qc_name+'.png');
	$('.bg').click(function(){
		closeCodediv();
	})
	$('#codeDiv').click(function(){
		closeCodediv();
	})
	$('#img').click(function(){
		closeCodediv();
	})
} 
//打印预览 遮罩层
function showCodediv(){
	
    document.getElementById('codeDiv').style.display='block';
    document.getElementById('bg').style.display='block';
}

function closeCodediv(){
    document.getElementById('bg').style.display='none';
    document.getElementById('codeDiv').style.display='none';
}

//客户删除
function customerDelete ( id ){
	if( confirm('您将要删除此客户，删除后所有信息将会被清除，请确认此操作！？') ){
		$.getJSON('admin/customer/delete',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
}
//二维码删除
function qc_codeDelete ( id ){
    if( confirm('您将要删除此客户，删除后所有信息将会被清除，请确认此操作！？') ){
        $.getJSON('admin/qc_code/delete',{id:id},function(msg){
            if( msg.result_code == 200 ){
                alert(msg.info)
                history.go(0);
            }
            if( msg.result_code == 400 ){
                alert(msg.error_msg)
                history.go(0);
            }
        })
    }
}

//会员删除
function userDelete ( id ){
	if( confirm('您将要删除此会员，删除后所有信息将会被清除，请确认此操作！？') ){
		$.getJSON('admin/user/delete',{id:id},function(msg){
			if( msg.result_code == 200 ){
	    		alert(msg.info)
	    		history.go(0);
	    	}
	    	if( msg.result_code == 400 ){
	    		alert(msg.error_msg)
	    		history.go(0);
	    	}
		})
	}
} 

function get_delete_id(){
	var str = '';
    $("[name='select']").each(function(){ 
    	if($(this).prop('checked') == true){
    		str += $(this).val() + ','; 
    	}
	})
	return str;
}

function customer_delete_all(){
	str = get_delete_id();
	if(str!=''){
		customerDelete(str);
	}else{
		alert('请选择需要删除的选项')
	}
}

function qc_code_delete_all(){
    str = get_delete_id();
    if(str!=''){
        qc_codeDelete(str);
    }else{
        alert('请选择需要删除的选项')
    }
}

function article_delete_all(){
	str = get_delete_id();
	if(str!=''){
		articleDelete(str);
	}else{
		alert('请选择需要删除的选项')
	}
}

function area_delete_all(){
	str = get_delete_id();
	if(str!=''){
		areaDelete(str);
	}else{
		alert('请选择需要删除的选项')
	}
}

function videos_delete_all(){
	str = get_delete_id();
	if(str!=''){
		videosDelete(str);
	}else{
		alert('请选择需要删除的选项')
	}
}

function project_delete_all(){
	str = get_delete_id();
	if(str!=''){
		projectDelete(str);
	}else{
		alert('请选择需要删除的选项')
	}
}

function product_delete_all(){
	str = get_delete_id();
	if(str!=''){
		productDelete(str);
	}else{
		alert('请选择需要删除的选项')
	}
}

function admin_delete_all(){
	str = get_delete_id();
	if(str!=''){
		adminDelete(str);
	}else{
		alert('请选择需要删除的选项')
	}
}

function dealer_delete_all(){
	str = get_delete_id();
	if(str!=''){
		dealerDelete(str);
	}else{
		alert('请选择需要删除的选项')
	}
}

function user_delete_all(){
	str = get_delete_id();
	if(str!=''){
		userDelete(str);
	}else{
		alert('请选择需要删除的选项')
	}
}