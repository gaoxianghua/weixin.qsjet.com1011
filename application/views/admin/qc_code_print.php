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
	width: 70%;
	border: 1px solid #ccd1d9;
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

<!–startprint1–>
<?php
if (isset($result) && ! empty($result)) {
    foreach ($result as $k => $v) {
        $x = $k + 1;
        echo "<img width='300' height='300' src='" . $qcode_url . 'qc_code/' . $v['qc_code_name'] . ".png'>";
    }
}
?>
<!–endprint1–>
<p class="noprint" style="text-align: center; margin-top: 40px;margin-bottom: 20px;">
	<input id="btnPrint" class='btn btn-primary' type="button" value="确认打印"
		onclick="preview(1);" />
</p>

<style type="text/css" media=print>
.noprint {
	display: none;
}
</style>

<script>
function preview(oper)
{
	
	if (oper < 10)
	{
		bdhtml=$('.detail_body').html();
		sprnstr="<!–startprint"+oper+"–>";//设置打印开始区域
		eprnstr="<!–endprint"+oper+"–>";//设置打印结束区域
		prnhtml=bdhtml.substring(bdhtml.indexOf(sprnstr)); //从开始代码向后取html
		prnhtmlprnhtml=prnhtml.substring(0,prnhtml.indexOf(eprnstr));//从结束代码向前取html
		
		window.document.body.innerHTML=prnhtml;
		$('body').css('text-align','left');
		$('img').css({"width":"300px","height":"300px",'position':'relative','top':'30px','left':'30px'});
		
		window.print();
		window.document.body.innerHTML=prnhtmlprnhtml;
		history.go(0);
	} else {
		window.print();
	}
}
</script>
