function checkPhone(obj,classname){
    if(!(/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{9}|14[0-9]{1}[0-9]{8}|17[0-9]{1}[0-9]{8}$/.test(obj.trim()))){
       $("."+classname).html("<font color='red' size='2'> *手机号码不合法</font>");
       $("."+classname).show();  
          return false; 
    } 
    return true;
}

function checkText(obj,classname,minNum,maxNum){
	if( obj.length>maxNum || obj.length<minNum ){
		$("."+classname).html("<font color='red' size='2'> *与字符限制数量不符</font>");
        $("."+classname).show(); 
        return false; 
	}
	var reg = /^[\u4e00-\u9fa5a-zA-Z0-9_\.\s\·\-]+$/ ;
    if(!(reg.test(obj.trim()))){ 
       $("."+classname).html("<font color='red' size='2'> *输入格式不合法</font>");
       $("."+classname).show(); 
          return false; 
    }
    return true;
}

function checkRemark(obj,classname,minNum,maxNum){
	if(obj.trim() == ''){
		$("."+classname).html("<font color='red' size='2'> *与字符限制数量不符</font>");
        $("."+classname).show(); 
        return false; 
	}
	if( obj.length>maxNum || obj.length<minNum ){
		$("."+classname).html("<font color='red' size='2'> *与字符限制数量不符</font>");
        $("."+classname).show(); 
        return false; 
	}
    return true;
}

function checkAge(obj,classname){
    if(!(/^([0-9]|[0-9]{2}|100)$/.test(obj.trim()))){ 
       $("."+classname).html("<font color='red' size='2'> *年龄格式不合法</font>");
       $("."+classname).show(); 
          return false; 
    }
    return true;
}

function checkEmail(obj,classname){
    var re = /^(\w-*\.*)+@(\w-?)+(\.\w{2,})+$/;
    if(!re.test(obj.trim())){
        $("."+classname).html("<font color='red' size='2'> *邮箱格式不合法</font>");
        $("."+classname).show(); 
        return false; 
    }
    return true;
}
//上传图片
function getObjectURL(obj) {
    var url = null ; 
    if(obj!=undefined){
        if (window.createObjectURL!=undefined) { // basic
            url = window.createObjectURL(obj) ;
        } else if (window.URL!=undefined) { // mozilla(firefox)
            url = window.URL.createObjectURL(obj) ;
        } else if (window.webkitURL!=undefined) { // webkit or chrome
            url = window.webkitURL.createObjectURL(obj);
        }
    }else{
        url=undefined;
    }
    return url ;

}
function uploadImg(obj,classname){
     if(obj.length==0){
        $("."+classname).html("<font color='red' size='2'> *请选择上传文件</font>");
        $("."+classname).show();
        return false;
    }
    var dom = document.getElementById("images");  
    var fileSize =  dom.files[0].size;
    var obj1=obj.split(".")[1];
    obj1 = obj1.toLowerCase( );
    if (obj1=="png"||obj1=="jpeg"||obj1=="jpg") {
        if(parseInt(fileSize) < parseInt(1024*1024*2)) {
                return true;
        }else{
            $("."+classname).html("<font color='red' size='2'> *图片大小超过2M</font>");
            $("."+classname).show(); 
            return false;
        }
    }else{
        $("."+classname).html("<font color='red' size='2'> *请选择jpg、jpeg、png格式的图片</font>");
        $("."+classname).show(); 
        return false;
    }
   
}

function checkUrl(obj,classname){
	var reg = /^(http||https||ftp):\/\//  ;
    if(!reg.test(obj.trim())){
        $("."+classname).html("<font color='red' size='2'> *链接格式不合法</font>");
        $("."+classname).show(); 
        return false; 
    }
    return true;
}

function cencelText(classname){
	$("."+classname).hide(); 
}
