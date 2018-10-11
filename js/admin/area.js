//页面一加载请求城市数据
function initCity(proId,cityId,areaId){
	var lisLv2 = "";
	$.ajax({
        type:"get",
        url:"admin/areas/getList",
        contentType: "application/x-www-form-urlencoded; charset=utf8",
        data:{parent_id:proId},
        dataType: "json",
        success:function(data){
        	var area_arr=data.info;
			if(area_arr){
				var i = 0;
				for(; i<area_arr.length; i++) {
					lisLv2 += '<li id="'+ area_arr[i].area_id +'" onclick="selectArea('+ area_arr[i].area_id +',\''+cityId+'\',\''+areaId+'\');">' + area_arr[i].area_name + '</li>';
				}
				$('#'+cityId).html(lisLv2);
			} else {
				$('#'+cityId).html("");
				alert("该地区没有市数据");
			}
            },
        error:function(err){
            console.log("网络错误");
        }
        });
}
//页面一加载请求区数据
function initArea(cityId,areaId,proId){
	var lisLv3="";
	$.ajax({
        type:"get",
        url:"admin/areas/getList",
        contentType: "application/x-www-form-urlencoded; charset=utf8",
        data:{parent_id:cityId},
        dataType: "json",
        success:function(data){
        	var area_arr=data.info;
			if(area_arr){
				var k = 0;
				for(;k < area_arr.length; k ++){
					lisLv3 += '<li style="width:100%;" id="'+ area_arr[k].area_id +'">' + area_arr[k].area_name + '</li>';
				}
				$('#'+proId).html(lisLv3);
			} else {
				$('#'+proId).html("");
				alert("该地区没有区/县数据");
			}
            },
        error:function(err){
            console.log("网络错误");
        }
        });
}
//一级联动
function proList(proId,cityId,areaId){
	$.ajax({
        type:"get",
        url:"admin/areas/getList",
        contentType: "application/x-www-form-urlencoded; charset=utf8",
        data:{parent_id:1},
        dataType: "json",
        success:function(data){
        	var area_arr=data.info;
        	var lisLv1 = "";
			var i = 0;
			for(; i<area_arr.length; i++) {
				lisLv1 += '<li id="'+ area_arr[i].area_id +'" onclick="selectCity('+ area_arr[i].area_id +',\''+cityId+'\',\''+areaId+'\',\''+area_arr[i].area_name+'\');">' + area_arr[i].area_name + '</li>';
			}
			$('#'+proId).html(lisLv1);
            },
        error:function(err){
            console.log("网络错误");
        }
        });
	
}
//二级联动
function selectCity(proId,cityId,areaId,proName){
    $("#province").val(proName);
    $("#city").val("");
    $("#area").val("");
	var e = event ||window.event;
    	e.stopPropagation();
    	$('#ul1').slideUp(300);
	var lisLv2 = "";
	$.ajax({
        type:"get",
        url:"admin/areas/getList",
        contentType: "application/x-www-form-urlencoded; charset=utf8",
        data:{parent_id:proId},
        dataType: "json",
        success:function(data){
        	var area_arr=data.info;
			if(area_arr){
				var i = 0;
				for(; i<area_arr.length; i++) {
					lisLv2 += '<li id="'+ area_arr[i].area_id +'" onclick="selectArea('+ area_arr[i].area_id +',\''+cityId+'\',\''+areaId+'\');">' + area_arr[i].area_name + '</li>';
				}
				$('#'+cityId).html(lisLv2);
				$('#'+cityId).slideDown(300);
			} else {
				$('#'+cityId).html("");
				alert("该地区没有市数据");
			}
            },
        error:function(err){
            console.log("网络错误");
        }
        });
}
//三级联动 areaId-->要填充的ul的ID  proId-->省的ID
function selectArea(cityId,areaId,proId){
	var lisLv3="";
	$("#area").val("");
	$.ajax({
        type:"get",
        url:"admin/areas/getList",
        contentType: "application/x-www-form-urlencoded; charset=utf8",
        data:{parent_id:cityId},
        dataType: "json",
        success:function(data){
        	var area_arr=data.info;
			if(area_arr){
				var k = 0;
				for(;k < area_arr.length; k ++){
					lisLv3 += '<li style="width:100%;" id="'+ area_arr[k].area_id +'">' + area_arr[k].area_name + '</li>';
				}
				$('#'+proId).html(lisLv3);
				$('#'+proId).slideDown(300);
			} else {
				$('#'+proId).html("");
				alert("该地区没有区/县数据");
			}
            },
        error:function(err){
            console.log("网络错误");
        }
        });
}