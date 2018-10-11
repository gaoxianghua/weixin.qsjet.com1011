/**
 * 
 */
function operate(url){
	if(confirm("确认进行此操作？")){
		$.get(url,function(data){
			eval("var data="+data+";");
			if(data.result_code==200){
				alert('操作成功');
				window.location.reload();
			}else{
				alert('操作失败:'+data.error_msg);
			}
		});
	};
}

function showDetail(url){
	window.location.href=url;
}