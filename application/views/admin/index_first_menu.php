<link rel="stylesheet"
      href="<?php echo base_url()?>css/admin/pop_box.css" />
<link rel="stylesheet"
      href="<?php echo base_url()?>css/admin/layout.css" />
<style>
 td { 
 	vertical-align:middle!important;
}
</style>
<div class="col-xs-6 col-md-4 second_menu">
	<ul>
            
<?php
if (isset($my_permission) && $my_permission) {
    foreach ($my_permission as $my_permission) {
        if (in_array($my_permission['id'], $this->session->userdata('permissions'))) {
?>            
       <a class='parent' id="<?php echo $my_permission['id']?> " style="cursor: pointer;">
			<li <?php echo isset($menu_flag)&&$menu_flag==$my_permission['codename']?"class='on'":'';?>  >
                        <?php echo $my_permission['name']?> <img class="img_<?php echo $my_permission['id']?>" src="img/images/<?php echo isset($menu_flag)&&$menu_flag==$my_permission['codename']?"admin_up":'admin_down';?>.png">
            </li>
		</a>
        <?php
            if (! empty($my_permission['child']) && is_array($my_permission['child'])) {
                foreach ($my_permission['child'] as $k => $v) {
                    if (in_array($v['id'], $this->session->userdata('permissions'))) {
        ?>   
            <a href="<?php echo $v['url']?>" <?php echo isset($menu_flag)&&$menu_flag==$my_permission['codename']?"style='font-size: 12px;display:block;cursor: pointer;'":"style='font-size: 12px;display:none;cursor: pointer;'";?> class="parent_id_<?php echo $my_permission['id']?>">
			     <li <?php echo isset($second_menu_flag)&&$second_menu_flag==$v['url']?"class='smallOn'":'';?> >
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <?php echo $v['name']?>
                </li>
                <?php

                if($my_permission['id'] == '25'){
                    echo "<li>";
                    echo "<a target='_blank' href='/admin/product/express'>";
                    echo "快递管理";
                    echo "</a>";
                    echo"</li>";
                }
               
                ?>

		    </a>

                       
         <?php
                    }
                }
            }
         ?>
<?php
        }
    }
}
?>

        <?php
        if ($this->session->userdata('type') == '0' || $this->session->userdata('type') == '1') {
            if($this->session->userdata('type') == '1'){
                $dealer_id = 11;
            }else {
                $dealer_id = $this->session->userdata('dealer_id');
            }

            echo "<li>";
            echo "<a href='/admin/ex_account/getList?dealer_id=$dealer_id'>";
            echo "兑换账号(业务员)管理";
            echo "</a>";
            echo "</li>";
        }
        ?>
        <?php
        if ($this->session->userdata('type') == '3') {
            $admin_id = $this->session->userdata('admin_id');
            echo "<li>";
            echo "<a href='/admin/ex_account/exInfo?admin_id=$admin_id'>";
            echo "账号信息";
            echo "</a>";
            echo "</li>";
        }
        ?>
	</ul>
</div>
<script>
var menu_flag = "<?php echo isset($menu_flag)?$menu_flag:'';?>" ;
$(function(){
	
})
    $('.parent').click(function(){
			var parent_id = $(this).attr('id');
			if( $('.parent_id_'+parent_id).css('display') == 'none' ){
 				$('.img_'+parent_id).attr('src','img/images/admin_up.png');
 				$('.parent_id_'+parent_id).show();
			}else{
 				$('.img_'+parent_id).attr('src','img/images/admin_down.png');
 				$('.parent_id_'+parent_id).hide();
			}
    }); 
</script>