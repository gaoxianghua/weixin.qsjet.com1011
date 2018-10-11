<div class="container_outer">
	<div class="container_inner">
		<div class="container_header row">
			<div class="header_title_left">
				<span class="container_name"><?php echo $detail_title_name?></span>
				<span class="detail_title_desc"><?php echo isset($detail_title_desc)?$detail_title_desc:'';?></span>
			</div>
			<div class="header_title_right">
				<?php echo isset($detail_title_buttons)?$detail_title_buttons:'';?>
				<?php echo isset($detail_form)?include_once($detail):'';?>
			</div>
		</div>
		<div>
			<hr class="hr_line">
			<div class="detail_body">
				<?php
    if (isset($detail)) {
        include_once ($detail);
    }
    ?>
			</div>
		</div>
	</div>
</div>
<div class="clear"></div>