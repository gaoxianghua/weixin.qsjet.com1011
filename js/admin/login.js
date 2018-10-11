/**
 * 
 */

$(document).ready(function () {  
    if ($.cookie("rememeber_user") == "true") {  
	    $("#remember").attr("checked", 'true');  
	    $("#username").val($.cookie("username"));  
//	    $("#password").val($.cookie("password"));  
    }  
});  


$('#login').bind("click",function(){
	save();
});


function save(){
	if($('#remember').is(':checked')){
    	var str_username = $("#username").val();  
        var str_password = $("#password").val();  
        $.cookie("rememeber_user", "true", { expires: 7 }); //存储一个带7天期限的cookie  
        $.cookie("username", str_username, { expires: 7 });  
//        $.cookie("password", str_password, { expires: 7 });  
    }else{
    	$.cookie("rememeber_user", "false", { expire: -1 });  
        $.cookie("username", "", { expires: -1 });  
//        $.cookie("password", "", { expires: -1 }); 
    }
}