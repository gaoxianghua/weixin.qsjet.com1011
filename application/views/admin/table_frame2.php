
<?php foreach ($table as $value) {?>
<div class="container_outer">
	<div class="container_inner" style="margin-left: 4px;">
		<div style="margin-top: 10px;">
			<span class="basicinfo">更多信息</span>
			<hr class="hr_line" style="margin-top: 10px;">
			<span style="margin-top: 10px; display: inline-block;"><?php echo $value['table_title_name']?></span>
			<div class="table_body">
					<?php echo $value['table'];?>
				</div>
		</div>
	</div>
</div>
<div class="clear"></div>
<?php }?>