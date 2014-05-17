<script type="text/javascript">

    // All images need to be loaded for this plugin to work so
    // we end up waiting for the whole window to load in this example
	
	jQuery(function() {
		jQuery(window).load(function () {
			jQuery(document).ready(function(){
				collage();
				jQuery('.Collage').collageCaption();
			});
		});
		
		jQuery(".fancybox").fancybox({helpers     : {
			title: {
				type: 'inside'
			}
		}});
		
		// Here we apply the actual CollagePlus plugin
		function collage() {
			jQuery('.Collage').removeWhitespace().collagePlus(
				{
					'fadeSpeed'     : 2000,
					'targetHeight'  : 200,
					'effect'        : 'effect-1',
					'direction'     : 'vertical',
					'allowPartialLastRow' : true
				}
			);
		};

		// This is just for the case that the browser window is resized
		var resizeTimer = null;
		jQuery(window).bind('resize', function() {
			// hide all the images until we resize them
			jQuery('.Collage .Image_Wrapper').css("opacity", 0);
			// set a timer to re-apply the plugin
			if (resizeTimer) clearTimeout(resizeTimer);
			resizeTimer = setTimeout(collage, 200);
		});
	
	});

    </script>
	<style type="text/css">
		.Collage img:hover{
			border-color: <?php echo (stripos(get_option( "res_sajes_gallery_image_border"), "#") !== false ? get_option( "res_sajes_gallery_image_border" ) : "#".get_option( "res_sajes_gallery_image_border" )); ?> !important;
		}
		.fancybox-skin{
			background-color: <?php echo (stripos(get_option( "res_sajes_gallery_image_border"), "#") !== false ? get_option( "res_sajes_gallery_image_border" ) : "#".get_option( "res_sajes_gallery_image_border" )); ?> !important;
		}
	</style>
	<?php
		global $wpdb;
		$query = "";
		$param = '';
		if(isset($_GET['gallery'])){
			$query =  "WHERE id = ".$_GET['gallery'];
		}else{
			if($gallery_id == 'all'){
				$param = ", thumb";
				$query = " JOIN ".$wpdb->prefix."res_gallery_image gal_img ON gal.id = gal_img.gallery_id GROUP BY gal.id";
			}else if(stripos($gallery_id, ',') !== false){
				$param = ", thumb";
				$query = " JOIN ".$wpdb->prefix."res_gallery_image gal_img ON gal.id = gal_img.gallery_id WHERE gal.id IN ($gallery_id) GROUP BY gal.id";
			}else{
				$query =  "WHERE id = $gallery_id";
			}
		}
		$gallery = $wpdb->get_results("SELECT gal.desc_ as desc_, name$param, gal.id as id FROM ".$wpdb->prefix."res_gallery gal ".$query);
		
		if(is_numeric($gallery_id) || isset($_GET['gallery'])){
			if(get_option( "res_sajes_gallery", $default ) === 'yes'){
				echo "<h3>".$gallery[0]->name."</h3>";
				echo "<p>".stripslashes($gallery[0]->desc_)."</p>";
			}			
	?>
	<section class="Collage effect-parent">
		<?php 
		if(isset($_GET['gallery']))
			$g_id = $_GET['gallery'];
		else
			$g_id = $gallery_id;
		
		$images = $wpdb->get_results("SELECT image, thumb, desc_ FROM ".$wpdb->prefix."res_gallery_image WHERE gallery_id = $g_id");
		foreach($images as $image):
		?>
		<div class="Image_Wrapper" <?php echo ($image->desc_ != '')? "data-caption='".stripslashes($image->desc_)."'" : '' ;?>>
			<a title="<?php echo ($image->desc_ != '')? substr(stripslashes($image->desc_), 0, 30)."..'" : '' ;?>"  class="fancybox" rel="gallery1" href="<?php echo $image->image;?>">
				<img src="<?php echo $image->thumb;?>">
			</a>
		</div>
		<?php endforeach; ?>
	</section>
	<?php 
	}else{
		echo '<section class="Collage effect-parent">';
		foreach($gallery as $key => $image){
			?>
				<div class="Image_Wrapper" <?php echo ($image->desc_ != '')? "data-caption='".substr(stripslashes($image->desc_), 0, 30)."..'" : '' ;?>>
					<a title="<?php echo ($image->desc_ != '')? substr(stripslashes($image->desc_), 0, 30)."..'" : '' ;?>" href="<?php the_permalink(); ?>?gallery=<?php echo $image->id; ?>">
						<img src="<?php echo $image->thumb;?>">
					</a>
				</div>
			<?php
		}
		echo '</section>';
	}
	?>