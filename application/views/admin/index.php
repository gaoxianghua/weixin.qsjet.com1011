<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>管理平台</title>
<base href="<?php echo base_url()?>" />
<link href="css/admin/bootstrap.min.css" rel="stylesheet">
<link href="css/admin/bootstrap-theme.min.css" rel="stylesheet">
<link href="css/admin/header.css" rel="stylesheet">
<link href="css/admin/footer.css" rel="stylesheet">
<link href="css/admin/frame.css" rel="stylesheet">
<link href="css/admin/table.css" rel="stylesheet">


<script type="text/javascript" src="js/admin/jquery-1.11.1.min.js"></script>
<script type="text/javascript" src="js/admin/bootstrap.min.js"></script>
<script type="text/javascript" src="js/admin/frame.js"></script>
<script type="text/javascript" src="js/admin/table.js"></script>
<script type="text/javascript" src="js/admin/curd.js"></script>
<script type="text/javascript" src="js/Validate.js"></script>
</head>
<body>
	<?php include_once('header.php');?>
	<div class="white_container_outer">
		<div class="white_container">
			<?php
if (isset($first_menu) && $first_menu) {
    include_once ($first_menu);
}
if (isset($second_menu) && $second_menu) {
    include_once ($second_menu);
}
if (isset($table_frame) && $table_frame) {
    include_once ($table_frame);
}
if (isset($detail_frame) && $detail_frame) {
    include_once ($detail_frame);
}
?>
		</div>
	</div>
	<?php include_once('footer.php');?>
</body>
</html>