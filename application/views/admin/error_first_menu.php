
<div class="col-xs-6 col-md-4 second_menu">
	<ul>
		<?php
if (isset($my_permission) && $my_permission) {
    foreach ($my_permission as $my_permission) {
        ?>
                            <a href="<?php echo $my_permission['url']?>">
			<li
			class="<?php echo isset($menu_flag)&&$menu_flag==$my_permission['codename']?'on':'';?>">
                                            <?php echo $my_permission['name']?>
                                    </li>
		</a>
                        <?php
        if (! empty($my_permission['child']) && is_array($my_permission['child'])) {
            foreach ($my_permission['child'] as $k => $v) {
                ?>   
                                                <a
			href="<?php echo $v['url']?>" style="font-size: 12px;"
			class="<?php echo isset($second_menu_flag)&&$second_menu_flag==$v['url']?'second_menu_flag':'';?>">

			<li>
                                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                                <?php echo $v['name']?>
                                                        </li>
		</a>            
                        <?php
            }
        }
        ?>
		<?php
    }
}
?>
	</ul>
</div>
