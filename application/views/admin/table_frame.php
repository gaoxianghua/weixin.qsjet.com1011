<div class="container_outer">
	<div class="container_inner">
		<div class="container_header">
			<?php
if ($table_form) {
    include_once ($table_form);
} else {
    ?>
				<span class="container_name"><?php echo $table_title_name?></span>
			<?php }?>
		</div>
		<div>
			<hr class="hr_line">
			<div class="table_body auto_line">
				<?php echo $result;?>
				<?php
    
    echo @$this->pagination->create_links();
    if (isset($method)) {
        ?>     
                                        <button type='button'
					class='btn btn-success'>确认选中</button>
                                <?php
    }
    ?>
			</div>
		</div>
	</div>
</div>
<div class="clear"></div>
