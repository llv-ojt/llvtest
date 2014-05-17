 <script type="text/javascript">
jQuery(function() {
	jQuery( "#tabs1" ).tabs();
	});
</script>
<div class="res-admin-main">
	<?php 
		if($message !== '')
			echo $message;
	?>
	<h1>A Simple option</h1>
	
	<form method="post">
		<label>Do you want gallery description and header to show up?<br>
		Currently You have selected "<?php echo get_option( "res_sajes_gallery", $default )?>"</label><br><br>
		<select name="show_header">
			<option value="yes">Yes</option>
			<option value="no">No</option>
		</select><br><br>
		<input type="submit" name="submit_show_header" value="Ok" class="button button-primary" >
	</form>
	
	<br><br>
	
	<h1>Your Image Border Color</h1>
	
	<form method="post">
		<label>What color do you want for your image border?<br>
		Currently You have selected "<?php echo get_option( "res_sajes_gallery_image_border", $default );?>"</label><br><br>
		<input type="text" name="color_code" ><br><br>
		<input type="submit" name="submit_color_code" value="Ok" class="button button-primary" >
	</form>
	
	<br><br>
	<h1>Your Galleries</h1>
	<?php
		global $wpdb;
		$galleries = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."res_gallery");
	?>
	<div id="tabs1">
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
				<span>Gallery Shortcode</span> : [responsive_gallery gallery = "<?php echo $gallery->id; ?>"]<br><br>
				<span>If you want all galleries to show up</span> : e.g [responsive_gallery gallery = "all"]<br><br>
				<span>You can also select specific galleries</span> : e.g [responsive_gallery gallery = "1, 2"]
			</div>
			<hr>
				<h4>Images</h4>
			<?php
				$attachments = $wpdb->get_results("SELECT id, thumb, desc_ FROM ".$wpdb->prefix."res_gallery_image WHERE gallery_id = ".$gallery->id);
				echo '<ul class="misc_list">';
				foreach ( $attachments as $attachment ) {
					echo '<li>';
						echo "<img src='".$attachment->thumb."'>";
						?>
							<form method="post">
								<input type="hidden" value="<?php echo $attachment->id; ?>" name="id">
								<textarea name="desc_" placeholder="Your description here"><?php echo $attachment->desc_;?></textarea>
								<input type="submit" name="submit_desc" value="Update" class="button button-primary button-large" >
							</form>
						<?php
					echo '</li>';
				}
				echo '</ul>';
			?>
			<br><hr><a href="<?php echo $_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'];?>&delete=<?php echo $gallery->id; ?>" class="button delete-button">Delete This Gallery</a>
		</div>
	<?php endforeach; ?>
	</div>
</div>