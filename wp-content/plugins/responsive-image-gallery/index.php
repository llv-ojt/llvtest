<?php
/**
 * Plugin Name: Responsive Image Gallery
 * Description: Image gallery made by integrating "collagePlus" and "Fancybox" jquery plugin
 * Version: 1.1
 * Author: Sajesh Bahing
 * Author URI: http://www.sajes-bahing.com.np
 * Plugin URI: http://wordpress.org/plugins/responsive-image-gallery/
 * License: Its free
 */
 
 class Index{
	
	function __construct(){
		add_shortcode('responsive_gallery', array($this, 'gallery_show') );
		add_action('admin_menu', array($this, 'add_option_page_function'));
		add_action('wp_footer', array($this, 'add_scripts'));
		add_action('admin_head', array($this, 'add_script_admin'));
		register_activation_hook(__FILE__, array($this, 'prepare_db_table'));
		add_action("wp_ajax_res_sajes_insert_image", array($this, 'insert_image'));
		add_action("wp_ajax_res_sajes_delete_image", array($this, 'delete_image'));
	}
	
	function add_script_admin(){
		if($_GET['page'] === 'gallery-responsive.php' || $_GET['page'] === 'gallery-misc.php'){
			wp_enqueue_style('responsive_sajes_style', plugins_url('css/admin.css', __FILE__));
			wp_enqueue_script( 'jquery-ui-tabs' );
		}
	}
	
	function add_scripts(){
		global $add_my_script, $light;

		if ( ! $add_my_script )
			return;
			
		wp_enqueue_style('responsive_sajes_style', plugins_url('css/transitions.css', __FILE__));
		
		if(!wp_script_is('jquery')) {
			wp_enqueue_script('jquery');
		}
		wp_enqueue_script('responsive_sajes_collage', plugins_url('js/jquery.collagePlus.js', __FILE__));
		wp_enqueue_script('responsive_sajes_whitespace', plugins_url('js/jquery.removeWhitespace.min.js', __FILE__));
		
		//if($light)
			wp_enqueue_script('responsive_sajes_light', plugins_url('js/jquery.fancybox.js', __FILE__));
	}
	
	function add_option_page_function(){
		add_menu_page( "Responsive Gallery", "Responsive Gallery", 'manage_options', "gallery-responsive.php", array($this, 'Gallery') );
		add_submenu_page("gallery-responsive.php", "Gallery Misc", "Gallery Misc", 'manage_options', "gallery-misc.php", array($this, 'misc_page'));
	}
	
	function Gallery(){
		$message = '';
		
		if(isset($_POST['submit_gallery'])){
			global $wpdb;
			$insert = $wpdb->insert($wpdb->prefix.'res_gallery',
									array(
										'time_' => date('y-m-d h:i:s'),
										'name' => $_POST['gallery_name'],
										'desc_' => $_POST['desc']
									));
			if($insert)
				$message = "<div class='success'>Gallery Successfully Created</div>";
		}else if(isset($_GET['delete'])){
			global $wpdb;
			$delete = $wpdb->delete($wpdb->prefix.'res_gallery', array('id'=>$_GET['delete']));
		}else if(isset($_POST['edit_desc'])){
			global $wpdb;
			$update = $wpdb->update($wpdb->prefix.'res_gallery', array('desc_' => $_POST['update_desc']), array('id'=>$_POST['update_id']));
			if($update){
				$message = '<div class="success">Gallery Description updated successfully</div>';
			}else{
				$message = '<div class="error">Gallery Description couldn\'t be updated</div>';
			}
		}
		
		include ABSPATH . 'wp-content/plugins/responsive-image-gallery/user_interface.php';
	}
	
	function prepare_db_table(){
		global $wpdb;
		$sql = "CREATE TABLE ".$wpdb->prefix."res_gallery (
		  id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
		  time_ datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  name tinytext NOT NULL,
		  desc_ longtext
		);";
		
		$sql1 = "CREATE TABLE ".$wpdb->prefix."res_gallery_image (
		  id int NOT NULL PRIMARY KEY AUTO_INCREMENT,
		  gallery_id int,
		  thumb longtext,
		  image longtext,
		  time_ datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		  desc_ longtext,
		  checked_id varchar(255)
		);";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $sql );
		dbDelta( $sql1 );
		
		$insert = $wpdb->insert($wpdb->prefix.'options', 
								array('option_name' => 'res_sajes_gallery',
										'option_value' => 'yes',
										'autoload' => 'no'));
	}
	
	function insert_image(){
		global $wpdb;
		$insert = $wpdb->insert($wpdb->prefix.'res_gallery_image',
								array(
									'time_' => date('y-m-d h:i:s'),
									'image' => $_POST['image'],
									'thumb' => $_POST['thumb'],
									'gallery_id' => $_POST['gallery'],
									'checked_id' => $_POST['id']
								));
	}
	
	function delete_image(){
		global $wpdb;
		$delete = $wpdb->delete($wpdb->prefix.'res_gallery_image',
								array('checked_id' => $_POST['id']));
	}
	
	function gallery_show($atts){		
		global $add_my_script;
		ob_start();

		$add_my_script = true;
		
		extract( shortcode_atts( array(
			'gallery' => 'gallery',
		), $atts ) );
		
		$gallery_id = "{$gallery}";
		
		if(is_numeric($gallery_id) || isset($_GET['gallery'])){
			global $light;
			$light = true;
		}
		include ABSPATH ."wp-content/plugins/responsive-image-gallery/gallery.php";
		$output = ob_get_clean();
		return $output;
	}
	
	function misc_page(){
		if(isset($_POST['submit_desc'])){
			global $wpdb;
			$update = $wpdb->update($wpdb->prefix."res_gallery_image",
									array(
										'desc_' => $_POST['desc_']
									),
									array(
										'id' => $_POST['id']
									)
									);
		}else if(isset($_GET['delete'])){
			global $wpdb;
			$delete = $wpdb->delete($wpdb->prefix.'res_gallery', array('id'=>$_GET['delete']));
		}else if(isset($_POST['submit_show_header'])){
		
			update_option("res_sajes_gallery", $_POST['show_header']);
			
		}else if(isset($_POST['submit_color_code'])){
		
			update_option("res_sajes_gallery_image_border", $_POST['color_code']);
			
		}
		include ABSPATH . 'wp-content/plugins/responsive-image-gallery/misc.php';
	}
 }
 
 new Index();