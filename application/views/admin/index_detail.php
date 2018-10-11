<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/detail_info.css" />
<link rel="stylesheet"
	href="<?php echo base_url()?>css/admin/pop_box.css" />
<meta charset="utf-8">
<div>
        <?php
        if (isset($result) && $result == 'index') {
            ?>
    <h2 style="margin-top: 150px; margin-left: 470px;">快舒尔管理端</h2>
        <?php
        } else {
            echo '正在建设中。。。';
        }
        ?>
</div>

