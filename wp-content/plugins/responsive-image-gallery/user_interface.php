 <script type="text/javascript">
jQuery(function() {
	jQuery( "#tabs" ).tabs();
	
	jQuery('.image_check').click(function(){
		var image = jQuery(this).attr("image");
		var thumb = jQuery(this).attr("thumbnail");
		var gallery = jQuery(this).parents(".tab_gallery").attr("var");
		var id = jQuery(this).attr('id');
		var obj = jQuery(".res-admin-main");
		
		var height = obj.height();
		jQuery(".Mask").css('display', 'block');
		jQuery(".Mask").height(height);
		jQuery(".Mask").width("100%");
		jQuery(".Mask").css('position', 'absolute');
		jQuery(".Mask").css('z-index', '100000');
		jQuery(".Mask").css('background', '#000000');
		jQuery(".Mask").css('opacity', '0.5');
				
		if(jQuery(this).is(":checked")){
			jQuery.ajax({
				url : ajaxurl,
				type: 'POST',
				data : {action : 'res_sajes_insert_image', image : image, thumb : thumb, gallery : gallery, id : id},
				success : function(){
					jQuery(".Mask").css('display', 'none');
				}
			});
		}else{
			jQuery.ajax({
				url : ajaxurl,
				type: 'POST',
				data : {action : 'res_sajes_delete_image', id: id},
				success : function(){
					jQuery(".Mask").css('display', 'none');
				}
			});
		}
	});
});
</script>
<div class="Mask"></div>
<div class="res-admin-main">
	<?php 
		if($message !== '')
			echo $message;
	?>
	<div>
		<form method="POST">
			<h3>Add new Gallery</h3>
			<input type="text" name="gallery_name" placeholder="gallery name"><br><br>
			<textarea name="desc" placeholder="Your Gallery Description" rows="5" cols="80"></textarea><br><br>
			<input type="submit" value="Enter" name="submit_gallery" class="button button-primary" >
		</form>
	</div>
	<br><hr><br>
	<h1>Your Galleries</h1>
	<?php
		$attachments = get_children( array('post_parent' => get_the_ID(), 'post_type' => 'attachment', 'post_mime_type' =>'image') );
		global $wpdb;
		$galleries = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."res_gallery");
	?>
	<div id="tabs">
	<ul>
		<?php foreach($galleries as $gallery):?>
		<li><a href="#tabs-<?php echo $gallery->id;?>"><?php echo $gallery->name; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php
		$arr = $wpdb->get_results("SELECT checked_id FROM ".$wpdb->prefix."res_gallery_image");
		$checked = array();
		foreach($arr as $val){
			array_push($checked, $val->checked_id);
		}
		foreach($galleries as $gallery):
	?>	
		<div id="tabs-<?php echo $gallery->id;?>" class="tab_gallery" var="<?php echo $gallery->id;?>">
			<div class="focus">
				<span>Gallery Id</span> : <?php echo $gallery->id; ?><br><br>
				<span>Gallery Shortcode</span> : [responsive_gallery gallery = <?php echo $gallery->id; ?>]<br><br>
				<span>If you want all galleries to show up</span> : e.g [responsive_gallery gallery = "all"]<br><br>
				<span>You can also select specific galleries</span> : e.g [responsive_gallery gallery = "1, 2"]
			</div>
			<form method="post">
				<h4>Description Text</h4>
				
				<label>Supports basic html tags like &lt;strong&gt;&lt;bold&gt;&lt;h1&gt; etc..</label><br><br>
				
				<input type="hidden" name="update_id" value="<?php echo $gallery->id; ?>">
				<textarea class="edit_desc" name="update_desc"><?php echo $gallery->desc_; ?></textarea>
				<br><br>
				<input type="submit" name="edit_desc" value="Update" class="button">
			</form>
			<hr>
				<h4>Images (include images by selecting with checkbox or remove them by disselecting)</h4>
			<?php
				echo '<ul class="list-image">';
				$count =1;
				foreach ( $attachments as $attachment_id => $attachment ) {
					echo '<li>';
					$thumb = wp_get_attachment_image_src($attachment_id, 'medium');
					?>	
						<div class="checkbox_">
							<input id="demo_box_<?php echo $gallery->id.$count;?>" class="css-checkbox image_check" type="checkbox" image="<?php echo $attachment->guid;?>" thumbnail ="<?php echo $thumb[0]; ?>" <?php echo ( in_array("demo_box_".$gallery->id.$count, $checked) ? 'checked="checked"' : '' ) ?> />
							<label for="demo_box_<?php echo $gallery->id.$count;?>" name="demo_lbl_1" class="css-label"></label>
						</div>
					<?php
					echo wp_get_attachment_image( $attachment_id, 'medium' );
					echo '</li>';
					$count++;
				}
				echo '</ul>';
			?>
			<br><hr><a href="<?php echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];?>&delete=<?php echo $gallery->id; ?>" class="button delete-button">Delete This Gallery</a>
		</div>
	<?php endforeach; ?>
	</div>
</div>