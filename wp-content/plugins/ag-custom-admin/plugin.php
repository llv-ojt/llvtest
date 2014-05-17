<?php
/*
Plugin Name: AG Custom Admin
Plugin URI: http://agca.argonius.com/ag-custom-admin/category/ag_custom_admin
Description: All-in-one tool for admin panel customization. Change almost everything: admin menu, dashboard, login page, admin bar etc. Apply admin panel themes.
Author: Argonius
Version: 1.3.7
Author URI: http://www.argonius.com/

	Copyright 2014. Argonius (email : info@argonius.com)
 
    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
	
$agca = new AGCA();

class AGCA{
	private $colorizer="";	
	private $active_plugin;
	private $agca_version;    
	private $agca_debug = false;    
	private $admin_capabilities;    	
    private $context = "";
    private $saveAfterImport = false;	
	private $templateCustomizations = "";
	private $templates_ep = "http://wordpressadminpanel.com/configuration.php";	
	public function __construct()
	{   	        			
        $this->reloadScript();		
		$this->checkPOST();
		$this->checkGET();		
            
                if(function_exists("add_filter")){
                    add_filter('admin_title', array(&$this,'change_title'), 10, 2); 		
                    add_filter('plugin_row_meta', array(&$this,'jk_filter_plugin_links'), 10, 2);
                }		
		add_action('admin_init', array(&$this,'agca_register_settings'));
		add_action('admin_init', array(&$this,'agca_init_session'));
		add_action('admin_head', array(&$this,'print_admin_css'));		
		add_action('login_head', array(&$this,'print_login_head'));	
		add_action('admin_menu', array(&$this,'agca_create_menu'));		
		add_action('wp_head', array(&$this,'print_page'));			
		register_deactivation_hook(__FILE__, array(&$this,'agca_deactivate'));	
	
		/*Initialize properties*/		
		$this->colorizer = $this->jsonMenuArray(get_option('ag_colorizer_json'),'colorizer');
                //fb($this->colorizer);
		$this->agca_version = "1.3.7";
		
		/*upload images programmaticaly*/
		//TODO upload with AJAX one by one, use post data to send urls one by one
		/*function my_sideload_image() {
			$file = media_sideload_image( 'http://a2.twimg.com/a/1318451435/phoenix/img/twitter_logo_right.png', 0 );
			$file1 = media_sideload_image( 'http://agca.argonius.com/templates/trunk/images/templates/monday/j1.jpg', 0 );
			$file2 = media_sideload_image( 'http://agca.argonius.com/templates/trunk/images/templates/monday/2.jpg', 0 );
			$url=explode("'",explode("src='",$file)[1])[0];			
			$url.=explode("'",explode("src='",$file1)[1])[0];	
			$url.=explode("'",explode("src='",$file2)[1])[0];	
			echo $url;
			
		}
		add_action( 'admin_init', 'my_sideload_image' );*/
		/*upload images programmaticaly*/
	}
	// Add donate and support information
	function jk_filter_plugin_links($links, $file)
	{
		if ( $file == plugin_basename(__FILE__) )
		{
		$links[] = '<a href="tools.php?page=ag-custom-admin/plugin.php">' . __('Settings') . '</a>';
		$links[] = '<a href="http://agca.argonius.com/ag-custom-admin/category/ag_custom_admin">' . __('Support') . '</a>';
		$links[] = '<a href="http://agca.argonius.com/ag-custom-admin/support-for-future-development">' . __('Donate') . '</a>';
		}
		return $links;
	}
	
	function agca_init_session(){
		if (!session_id())
		session_start();
	}
	
	function checkGET(){
		if(isset($_GET['agca_action'])){
			if($_GET['agca_action'] =="remove_templates"){
				$this->delete_template_images_all();
				update_option('agca_templates', "");
				update_option('agca_selected_template', "");
			}
		}
	}
	
	function checkPOST(){
		if(isset($_POST['_agca_save_template'])){
		  //print_r($_POST);					  
		  $data = $_POST['templates_data'];
		  $parts = explode("|||",$data);
		  $common_data = $parts [0];
		  $admin_data = $parts [1];
		  $login_data = $parts [2];
		  $settings = $parts [3];
		  $images = $parts [4];
		  $template_name = $_POST['templates_name'];	
			
			update_option('agca_selected_template', $template_name);
			
			$templates = get_option( 'agca_templates' );			
			if($templates == ""){
				$templates = array();			
			}	
			
			$templates[$template_name] = array(
				'common'=>$common_data,
				'admin'=>$admin_data,
				'login'=>$login_data,
				'images'=>$images,
				'settings'=>$settings
				);
			update_option('agca_templates', $templates);
			
			$_POST = array();
			
		}else if(isset($_POST['_agca_templates_session'])){			
			$this->agcaAdminSession();
			if($_POST['template'] !="")
				$_SESSION["AGCA"]["Templates"][$_POST['template']] = array("license"=>$_POST['license']);			
			
			print_r($_SESSION);
			echo "_agca_templates_session:OK";
			exit;
		}else if(isset($_POST['_agca_templates_session_remove_license'])){			
			$this->agcaAdminSession();
			if($_POST['template'] !="")
				$_SESSION["AGCA"]["Templates"][$_POST['template']] = null;						
			print_r($_SESSION);
			echo "_agca_templates_session_remove_license:OK";
			exit;
		}else if(isset($_POST['_agca_get_templates'])){
			$templates = get_option( 'agca_templates' );
			if($templates == "") $templates = array();	
			$results = array();
			foreach($templates as $key=>$val){
				$results[]=$key;
			}
			echo json_encode($results);
			exit;
		}else if(isset($_POST['_agca_activate_template'])){
			update_option('agca_selected_template', $_POST['_agca_activate_template']);
			$_POST = array();
			//unset($_POST);
			exit;
		}else if(isset($_POST['_agca_template_settings'])){
			$settings = $_POST['_agca_template_settings'];
			
			$templates = get_option( 'agca_templates' );			
			if($templates == ""){
				$templates = array();			
			}			
			$template_name = $_POST["_agca_current_template"];
			
			$templates[$template_name]["settings"] = $settings;
			update_option('agca_templates', $templates);
			
			$_POST = array();			
			//print_r($templates);
			exit;
		}else if(isset($_POST['_agca_upload_image'])){		
			function my_sideload_image() {
				$remoteurl = $_POST['_agca_upload_image'];			
				$file = media_sideload_image( $remoteurl, 0 ,"AG Custom Admin Template Image (do not delete)");	
				$fileparts = explode("src='",$file);
				$url=explode("'",$fileparts[1]);						
				echo $url[0];				
				exit;				
			}
			add_action( 'admin_init', 'my_sideload_image' );
		
		}else if(isset($_POST['_agca_remove_template_images'])){		
			$this->delete_template_images($_POST['_agca_remove_template_images']);			
			exit;
		}
	}
	
	function delete_template_images_all(){
		$templates = get_option('agca_templates');			
			if($templates != null && $templates != ""){
				foreach($templates as $template){
					if($template != null && $template['images'] != null && $template['images'] != ""){
						//print_r($template['images']);
						$imgs = explode(',',$template['images']);
						foreach($imgs as $imageSrc){
							$this->delete_attachment_by_src($imageSrc);
						}
						//print_r($imgs);
					}
				}			
			}
		//print_r($templates);
	}
	
	function delete_template_images($template_name){
		$templates = get_option('agca_templates');			
			if($templates != null && $templates != ""){
				$template = $templates[$template_name];
				if($template != null && $template['images'] != null && $template['images'] != ""){
					//print_r($template['images']); exit;
					$imgs = explode(',',$template['images']);
					foreach($imgs as $imageSrc){
						$this->delete_attachment_by_src($imageSrc);
					}
					//print_r($imgs);
				}
			}
		//print_r($templates);
	}
	
	function delete_attachment_by_src ($image_src) {
		  global $wpdb;
		  $query = "SELECT ID FROM {$wpdb->posts} WHERE guid='$image_src'";
		  $id = $wpdb->get_var($query);
		  wp_delete_attachment( $id, $true );
	}
	
	function get_installed_agca_templates(){
		$templates = get_option( 'agca_templates' );
		if($templates == "")return '[]';
		$results = array();
		foreach($templates as $key=>$val){
			$results[]=$key;
		}
		return json_encode($results);		
	}
	
	function isGuest(){
		global $user_login;
		if($user_login) {
			return false;
		}else{
			return true;
		}
	}
	function check_active_plugin(){
		
		$ozh = false;			
			
		if (is_plugin_active('ozh-admin-drop-down-menu/wp_ozh_adminmenu.php')) {		
			$ozh = true;
		}		
		
		$this->active_plugin = array(
			"ozh" => $ozh
		);
	}
	function change_title($admin_title, $title){		
	//return get_bloginfo('name').' - '.$title;
		if(get_option('agca_custom_title')!=""){
			$blog = get_bloginfo('name');
			$page = $title;
			$customTitle = get_option('agca_custom_title');				
			$customTitle = str_replace('%BLOG%',$blog,$customTitle);
			$customTitle = str_replace('%PAGE%',$page,$customTitle);
			return $customTitle;
		}else{
			return $admin_title;
		}	
	}
	function agca_get_includes() {            
            ?>		
                        <script type="text/javascript">
                            <?php 
                                //AGCA GLOBALS                            
                                echo "var agca_global_plugin_url = '".trailingslashit(plugins_url(basename(dirname(__FILE__))))."';"; 
                            ?>
                        </script>
			<link rel="stylesheet" type="text/css" href="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>style/ag_style.css?ver=<?php echo $this->agca_version; ?>" />                       
			<script type="text/javascript" src="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>script/ag_script.js?ver=<?php echo $this->agca_version; ?>"></script>	                        	
                        <?php 					    
						echo $this->templateCustomizations; 
						
                        if(!((get_option('agca_role_allbutadmin')==true) and  (current_user_can($this->admin_capability())))){	
                            ?>
                             <style type="text/css">							 
                                 <?php
                                    echo get_option('agca_custom_css'); 
                                 ?>
                             </style>
                             <script type="text/javascript">
                                 <?php
                                    echo get_option('agca_custom_js'); 
                                 ?>
                             </script>
                            <?php
                        }			
	}
	
	function agca_enqueue_scripts() {			
		wp_enqueue_script('jquery');	
	}
	
	function reloadScript(){
		$isAdmin = false;
		if(defined('WP_ADMIN') && WP_ADMIN == 1){
			$isAdmin = true;
		}
        if(in_array($GLOBALS['pagenow'], array('wp-login.php', 'wp-register.php')) || $isAdmin){              			
			add_action('init', array(&$this,'agca_enqueue_scripts'));				
        }             
	}
	
	function agca_register_settings() {	
		register_setting( 'agca-options-group', 'agca_role_allbutadmin' );
		register_setting( 'agca-options-group', 'agca_screen_options_menu' );
		register_setting( 'agca-options-group', 'agca_help_menu' );
		register_setting( 'agca-options-group', 'agca_logout' );
		register_setting( 'agca-options-group', 'agca_remove_your_profile' );
		register_setting( 'agca-options-group', 'agca_logout_only' );
		register_setting( 'agca-options-group', 'agca_options_menu' );
		register_setting( 'agca-options-group', 'agca_custom_title' );
		register_setting( 'agca-options-group', 'agca_howdy' );
		register_setting( 'agca-options-group', 'agca_header' );
		register_setting( 'agca-options-group', 'agca_header_show_logout' );		
		register_setting( 'agca-options-group', 'agca_footer' );
		register_setting( 'agca-options-group', 'agca_privacy_options' );
		register_setting( 'agca-options-group', 'agca_header_logo' );
		register_setting( 'agca-options-group', 'agca_header_logo_custom' );
		register_setting( 'agca-options-group', 'agca_wp_logo_custom' );
		register_setting( 'agca-options-group', 'agca_remove_site_link' );
        register_setting( 'agca-options-group', 'agca_wp_logo_custom_link' );
                
		register_setting( 'agca-options-group', 'agca_site_heading' );
		register_setting( 'agca-options-group', 'agca_custom_site_heading' );
		register_setting( 'agca-options-group', 'agca_update_bar' );
		
		register_setting( 'agca-options-group', 'agca_footer_left' );
		register_setting( 'agca-options-group', 'agca_footer_left_hide' );		
		register_setting( 'agca-options-group', 'agca_footer_right' );
		register_setting( 'agca-options-group', 'agca_footer_right_hide' );
		
		register_setting( 'agca-options-group', 'agca_login_banner' );
		register_setting( 'agca-options-group', 'agca_login_banner_text' );
		register_setting( 'agca-options-group', 'agca_login_photo_remove' );
		register_setting( 'agca-options-group', 'agca_login_photo_url' );
		register_setting( 'agca-options-group', 'agca_login_photo_href' );
        register_setting( 'agca-options-group', 'agca_login_round_box' );
		register_setting( 'agca-options-group', 'agca_login_round_box_size' );		
		
		register_setting( 'agca-options-group', 'agca_dashboard_icon' );
		register_setting( 'agca-options-group', 'agca_dashboard_text' );
		register_setting( 'agca-options-group', 'agca_dashboard_text_paragraph' );
        register_setting( 'agca-options-group', 'agca_dashboard_widget_welcome' );
		register_setting( 'agca-options-group', 'agca_dashboard_widget_activity' );			
		register_setting( 'agca-options-group', 'agca_dashboard_widget_il' );	
		register_setting( 'agca-options-group', 'agca_dashboard_widget_plugins' );	
		register_setting( 'agca-options-group', 'agca_dashboard_widget_qp' );	
		register_setting( 'agca-options-group', 'agca_dashboard_widget_rn' );	
		register_setting( 'agca-options-group', 'agca_dashboard_widget_rd' );	
		register_setting( 'agca-options-group', 'agca_dashboard_widget_primary' );	
		register_setting( 'agca-options-group', 'agca_dashboard_widget_secondary' );	

		//WP3.3
		register_setting( 'agca-options-group', 'agca_admin_bar_comments' );
		register_setting( 'agca-options-group', 'agca_admin_bar_new_content' );
		register_setting( 'agca-options-group', 'agca_admin_bar_new_content_post' );
		register_setting( 'agca-options-group', 'agca_admin_bar_new_content_link' );
		register_setting( 'agca-options-group', 'agca_admin_bar_new_content_page' );
		register_setting( 'agca-options-group', 'agca_admin_bar_new_content_user' );
		register_setting( 'agca-options-group', 'agca_admin_bar_new_content_media' );		
		register_setting( 'agca-options-group', 'agca_admin_bar_update_notifications' );	
		register_setting( 'agca-options-group', 'agca_remove_top_bar_dropdowns' );	
		register_setting( 'agca-options-group', 'agca_admin_bar_frontend' );	
		register_setting( 'agca-options-group', 'agca_admin_bar_frontend_hide' );
		register_setting( 'agca-options-group', 'agca_login_register_remove' );
		register_setting( 'agca-options-group', 'agca_login_register_href' );
		register_setting( 'agca-options-group', 'agca_login_lostpassword_remove' );
		register_setting( 'agca-options-group', 'agca_admin_capability' );		
		register_setting( 'agca-options-group', 'agca_disablewarning' );
		register_setting( 'agca-template-group', 'agca_selected_template' );	
		register_setting( 'agca-template-group', 'agca_templates' );						
		//delete_option( 'agca_templates' );			


		/*Admin menu*/
		register_setting( 'agca-options-group', 'agca_admin_menu_turnonoff' );	
		register_setting( 'agca-options-group', 'agca_admin_menu_agca_button_only' );	
		register_setting( 'agca-options-group', 'agca_admin_menu_separator_first' );	
		register_setting( 'agca-options-group', 'agca_admin_menu_separator_second' );	
		register_setting( 'agca-options-group', 'agca_admin_menu_icons' );	
		register_setting( 'agca-options-group', 'agca_admin_menu_collapse_button' );
        register_setting( 'agca-options-group', 'agca_admin_menu_arrow' );
        register_setting( 'agca-options-group', 'agca_admin_menu_submenu_round' );	
        register_setting( 'agca-options-group', 'agca_admin_menu_submenu_round_size' );
        register_setting( 'agca-options-group', 'agca_admin_menu_brand' );
        register_setting( 'agca-options-group', 'agca_admin_menu_brand_link' );                
		register_setting( 'agca-options-group', 'agca_admin_menu_autofold' );                
		register_setting( 'agca-options-group', 'ag_edit_adminmenu_json' );
		register_setting( 'agca-options-group', 'ag_add_adminmenu_json' );	
		register_setting( 'agca-options-group', 'ag_colorizer_json' );	
		register_setting( 'agca-options-group', 'agca_colorizer_turnonoff' ); 		 		
                
        register_setting( 'agca-options-group', 'agca_custom_js' );
        register_setting( 'agca-options-group', 'agca_custom_css' );                
             
                
                if(!empty($_POST)){
                 // fb($_POST);
                    if(isset($_POST['_agca_import_settings']) && $_POST['_agca_import_settings']=="true"){                            
                            if(isset($_FILES) && isset($_FILES['settings_import_file']) ){
                                if($_FILES["settings_import_file"]["error"] > 0){                                      
                                }else{                                     
                                    $file = $_FILES['settings_import_file'];
                                    if($this->startsWith($file['name'],'AGCA_Settings')){  
                                        if (file_exists($file['tmp_name'])) {
                                            $fh = fopen($file['tmp_name'], 'r');
                                            $theData = "";
                                            if(filesize($file['tmp_name']) > 0){
                                                $theData = fread($fh,filesize($file['tmp_name']));
                                            }  
                                            fclose($fh);                                          
                                            $this->importSettings($theData); 
                                        }                                         
                                    }
                                }                                
                            }
                    }else if(isset($_POST['_agca_export_settings']) && $_POST['_agca_export_settings']=="true"){
                            $this->exportSettings();  
                    }    
                }
				
				if(isset($_GET['agca_action'])){
						if($_GET['agca_action'] == "disablewarning"){
							update_option('agca_disablewarning', true);
						}                       
                }
	}

	function agca_deactivate() {	
		
	}  
	
    function getOptions(){
            return Array(
                'agca_role_allbutadmin',
				'agca_admin_bar_frontend',
				'agca_admin_bar_frontend_hide',
				'agca_login_register_remove',
				'agca_login_register_href',
				'agca_login_lostpassword_remove',
				'agca_admin_capability',
                'agca_screen_options_menu',
                'agca_help_menu',
                'agca_logout',
                'agca_remove_your_profile',
                'agca_logout_only',
                'agca_options_menu',
				'agca_custom_title',
                'agca_howdy',
                'agca_header',
                'agca_header_show_logout',
                'agca_footer',
                'agca_privacy_options',
                'agca_header_logo',
                'agca_header_logo_custom',
				'agca_remove_site_link',
                'agca_wp_logo_custom',
                'agca_wp_logo_custom_link',
                'agca_site_heading',
                'agca_custom_site_heading',
                'agca_update_bar',
                'agca_footer_left',
                'agca_footer_left_hide',
                'agca_footer_right',
                'agca_footer_right_hide',
                'agca_login_banner',
                'agca_login_banner_text',
                'agca_login_photo_remove',
                'agca_login_photo_url',
                'agca_login_photo_href',
                'agca_login_round_box',
                'agca_login_round_box_size',
                'agca_dashboard_icon',
                'agca_dashboard_text',
                'agca_dashboard_text_paragraph',
                'agca_dashboard_widget_welcome',
				'agca_dashboard_widget_activity',
                //'agca_dashboard_widget_rc', deprecated in 3.8 and 1.3.1
                'agca_dashboard_widget_il',
                'agca_dashboard_widget_plugins',
                'agca_dashboard_widget_qp',
                'agca_dashboard_widget_rn',
                'agca_dashboard_widget_rd',
                'agca_dashboard_widget_primary',
                'agca_dashboard_widget_secondary',
                'agca_admin_bar_comments',
                'agca_admin_bar_new_content',
                'agca_admin_bar_new_content_post',
                'agca_admin_bar_new_content_link',
                'agca_admin_bar_new_content_page',
                'agca_admin_bar_new_content_user',
                'agca_admin_bar_new_content_media',
                'agca_admin_bar_update_notifications',
                'agca_remove_top_bar_dropdowns',
                'agca_admin_menu_turnonoff',
                'agca_admin_menu_agca_button_only',
                'agca_admin_menu_separator_first',
                'agca_admin_menu_separator_second',
                'agca_admin_menu_icons',
                'agca_admin_menu_arrow',
                'agca_admin_menu_submenu_round',
                'agca_admin_menu_submenu_round_size',
                'agca_admin_menu_brand',
                'agca_admin_menu_brand_link',  
				'agca_admin_menu_autofold',
				'agca_admin_menu_collapse_button',
                'ag_edit_adminmenu_json',
                'ag_add_adminmenu_json',
                'ag_colorizer_json',
                'agca_colorizer_turnonof',
                'agca_custom_js',
                'agca_custom_css',
                'agca_colorizer_turnonoff',				
				'agca_disablewarning',
				'agca_selected_template',
				'agca_templates',
            ); 
        }  
		
		function getTextEditor($name){
				$settings = array(
				'textarea_name' => $name,				
				'media_buttons' => true,				
				'tinymce' => array(							
					'theme_advanced_buttons1' => 'formatselect,|,bold,italic,underline,|,' .
						'bullist,blockquote,|,justifyleft,justifycenter' .
						',justifyright,justifyfull,|,link,unlink,|' .
						',spellchecker,wp_fullscreen,wp_adv'
				)
			);
			wp_editor( get_option($name), $name, $settings );
		}
        
        function importSettings($settings){
            $exploaded = explode("|^|^|", $settings);
           // $str = "EEE: ";
            
            $savedOptions = array();
            
            foreach ($exploaded as $setting){
               
                $key = current(explode(':', $setting));
                $value = substr($setting, strlen($key)+1);                
                $cleanedValue = str_replace('|^|^|','',$value);                
                $savedOptions[$key] = $cleanedValue;        
            } 
            
           // print_r($savedOptions);
            
            $optionNames = $this->getOptions();
            
            foreach ($optionNames as $optionName){
                $optionValue = "";              
                $optionValue = $savedOptions[$optionName];
                
                if($optionName == "ag_edit_adminmenu_json" || $optionName == "ag_add_adminmenu_json" ||$optionName == "ag_colorizer_json"){
                    $optionValue = str_replace("\\\"", "\"", $optionValue);
                    $optionValue = str_replace("\\\'", "\'", $optionValue);                   
                }else if($optionName == "agca_custom_js" || $optionName == "agca_custom_css"){
                    //fb($optionValue);
                    $optionValue = htmlspecialchars_decode($optionValue);
                    $optionValue = str_replace("\'", '"', $optionValue);
                    $optionValue = str_replace('\"', "'", $optionValue);
                    //fb($optionValue);
                }else{
                    
                }  
                update_option($optionName, $optionValue);                
                $str.="/".$optionName."/".$optionValue."\n";
            } 
            
            //Migration from 1.2.6. to 1.2.5.1 - remove in later versions
            //agca_script_css
            //
           // fb($savedOptions);
           if($savedOptions['agca_script_css'] != null){
                    $optionValue = "";  
                    $optionValue = str_replace("\'", '"', $savedOptions['agca_script_css']);            
                    $optionValue = str_replace('\"', "'", $optionValue);
                     update_option('agca_custom_css', $optionValue);
           }
           if($savedOptions['agca_script_js'] != null){
                    $optionValue = "";  
                    $optionValue = str_replace("\'", '"', $savedOptions['agca_script_js']);            
                    $optionValue = str_replace('\"', "'", $optionValue);
                     update_option('agca_custom_js', $optionValue);
           }            
                     
           //echo $str;
           
           //save imported settings
           $this->saveAfterImport = true;         
        }
        
        function exportSettings(){
            $str = "";
            
            $include_menu_settings = false;
            if(isset($_POST['export_settings_include_admin_menu'])){               
                if($_POST['export_settings_include_admin_menu'] == 'on'){
                    $include_menu_settings = true;
                }
            }

            foreach ($_POST as $key => $value) {
                if ($this->startsWith($key,'ag')||$this->startsWith($key,'color')) {
                    if($this->startsWith($key,'ag_edit_adminmenu')){
                        if($include_menu_settings) $str .=$key. ":".$value."|^|^|";
                    }else{
                        $str .=$key. ":".$value."|^|^|";
                    }
                 }               
            }
          
             $filename = 'AGCA_Settings_'.date("Y-M-d_H-i-s").'.agca';             
             header("Cache-Control: public");
             header("Content-Description: File Transfer");            
             header("Content-Disposition: attachment; filename=$filename");
             header("Content-Type: text/plain; "); 
             header("Content-Transfer-Encoding: binary");
             echo $str;
             die();
        }       
        
        function startsWith($haystack, $needle)
        {
            $length = strlen($needle);
            return (substr($haystack, 0, $length) === $needle);
        }
        
 
                
	function agca_create_menu() {
	//create new top-level menu		
		add_management_page( 'AG Custom Admin', 'AG Custom Admin', 'administrator', __FILE__, array(&$this,'agca_admin_page') );
	}
	
	function agca_create_admin_button($name,$arr) {
		$class="";
                $wpversion = $this->get_wp_version();
		$href = $arr["value"];
		$target =$arr["target"];
		
                $button ="";
                
                if($wpversion >=3.5 ){
                        $button .= '<li id="menu-'.$name.'" class="ag-custom-button wp-not-current-submenu menu-top">';
                        $button .= '<a target="'.$target.'" class="wp-has-submenu wp-not-current-submenu menu-top" href="'.$href.'">';                                
                        $button .= '<div style="padding-left:5px;" class="wp-menu-name">'.$name.'</div>';
                        $button .= '</a>';
                        $button .= '</li>';
                }else{
                        $button .= '<li id="menu-'.$name.'" class="ag-custom-button menu-top menu-top-first '.$class.' menu-top-last">';			
			$button .= '<div class="wp-menu-toggle" style="display: none;"><br></div>';
			$button .=  '<a tabindex="1" target="'.$target.'" class="menu-top menu-top-last" href="'.$href.'">'.$name.'</a>';
		        $button .=  '</li>';                    
                }
		
		return $button;		
	}	
	function agca_decode($code){
		$code = str_replace("{","",$code);
		$code = str_replace("}","",$code);
        $code = str_replace("\", \"","\"|||\"",$code);
		$elements = explode("|||",$code);
		
		return $elements;
	}
	
	function jsonMenuArray($json,$type){
		$arr = explode("|",$json);
		$elements = "";
		$array ="";
		$first = true;
		//print_r($json);
		if($type == "colorizer"){
			$elements = json_decode($arr[0],true);
			if($elements !=""){
				return $elements;
			}
		}else if($type == "buttons"){
			$elements = json_decode($arr[0],true);
			if($elements !=""){
				foreach($elements as $k => $v){		
					$array.=$this->agca_create_admin_button($k,$v);			
				}	
			}
		}else if($type == "buttonsJq"){
			$elements = json_decode($arr[0],true);
			if($elements !=""){
				foreach($elements as $k => $v){	
					$array.='<tr><td colspan="2"><button target="'.$v["target"].'" title="'.$v["value"].'" type="button">'.$k.'</button>&nbsp;(<a style="cursor:pointer" class="button_edit">edit</a>)&nbsp;(<a style="cursor:pointer" class="button_remove">remove</a>)</td><td></td></tr>';							
				}	
			}
		}else{				
			if(isset($arr[$type])){
				$elements = $this->agca_decode($arr[$type]);
			}
                       
			if($elements !=""){
				foreach($elements as $element){                                     
					if(!$first){
						$array .=",";
					}
					$parts = explode(" : ",$element);
					if(isset($parts[0]) && isset($parts[1])){
						$array.="[".$parts[0].", ".$parts[1]."]";
					}
					$first=false;
				}	
			}	
		}
			
		return $array;			
	}
	
	function remove_dashboard_widget($widget,$side)	
	{
		//side can be 'normal' or 'side'
		global $wp_meta_boxes;
		remove_meta_box($widget, 'dashboard', $side); 
	}
	
	function get_wp_version(){
		global $wp_version;
		$array = explode('-', $wp_version);		
		$version = $array[0];		
		return $version;
	}
		
	function print_page()
	{
	if($this->isGuest()){
		return false;
	}
	
	if(get_option('agca_admin_bar_frontend_hide')==true){
		add_filter( 'show_admin_bar', '__return_false' );
	?>
		  <style type="text/css">
                            #wpadminbar{
                                display: none;                       
                            }   													
                        </style>
						 <script type="text/javascript">
                            window.setTimeout(function(){document.getElementsByTagName('html')[0].setAttribute('style',"margin-top:0px !important");},50);                            
                        </script>
                 <?php 
	}
		if(get_option('agca_admin_bar_frontend')!=true){ 				 		
		
            $this->context = "page";
            $wpversion = $this->get_wp_version();
		?>
                       
                 
                 <script type="text/javascript">      
                    var wpversion = "<?php echo $wpversion; ?>";
                    var agca_version = "<?php echo $this->agca_version; ?>";
					var agca_debug = <?php echo ($this->agca_debug)?"true":"false"; ?>;
                    var jQueryScriptOutputted = false;
                    var agca_context = "page";
                    function initJQuery() {
                        //if the jQuery object isn't available
                        if (typeof(jQuery) == 'undefined') {
                            if (! jQueryScriptOutputted) {
                                //only output the script once..
                                jQueryScriptOutputted = true;
                                //output the script (load it from google api)
                                document.write("<scr" + "ipt type=\"text/javascript\" src=\"http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js\"></scr" + "ipt>");
                            }
                            setTimeout("initJQuery()", 50);
                        } else {
                            jQuery(function() {  
                                try
                                { 
                                    <?php if(get_option('agca_header')!=true){ ?>
                                                jQuery('#wpadminbar').show();
                                    <?php } ?>
                                    
                                    <?php  $this->print_admin_bar_scripts(); ?>
                                }catch(ex){}
                            });                             
                        }
                    }
                    initJQuery();                  
                </script>
                 <script type="text/javascript"> 
                     <?php echo "var agca_global_plugin_url = '".trailingslashit(plugins_url(basename(dirname(__FILE__))))."';"; ?>
                 </script>
                <script type="text/javascript" src="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>script/ag_script.js?ver=<?php echo $this->agca_version; ?>"></script>
				<script type="text/javascript"> 
				jQuery(document).ready(function(){				
                <?php if(get_option('agca_colorizer_turnonoff') == 'on' && (get_option('agca_admin_bar_frontend_hide')!=true)){				
						foreach($this->colorizer as $k => $v){
							if(($k !="") and ($v !="")){	
								if(
									$k == "color_header" ||
									$k == "color_font_header"
								){
									?> updateTargetColor("<?php echo $k;?>","<?php echo $v;?>"); <?php
								}
								
							}
						}
					?>					
					
					<?php
					}
					 ?>
				});	
                </script>  
                    <?php
		}
               
	}
        
        function print_admin_bar_scripts(){
            ?>
            if(isWPHigherOrEqualThan("3.3")){ 
                <?php if(get_option('agca_remove_top_bar_dropdowns')==true){ ?>
                   	
                        
                        //remove on site page
                         jQuery("#wpadminbar #wp-admin-bar-root-default > #wp-admin-bar-wp-logo .ab-sub-wrapper").hide();
                         jQuery("#wpadminbar #wp-admin-bar-root-default > #wp-admin-bar-site-name .ab-sub-wrapper").hide();
                         
                         jQuery("#wpadminbar #wp-admin-bar-root-default > #wp-admin-bar-wp-logo .ab-item").attr('title','');
                     
                        
                         var abitemSelector = "#wpadminbar .ab-top-menu > li.menupop > .ab-item";
                         var originalBkg = jQuery(abitemSelector).css('background');
                         var originalColor = jQuery(abitemSelector).css('color');
                       jQuery(abitemSelector).mouseover(function(){
                            jQuery(this).css({'background':'#222222','color':'#fafafa'});
                       }).mouseout(function(){
                              jQuery(this).css({'background':originalBkg,'color':originalColor});
                       });                       

                        <?php if(get_option('agca_admin_bar_new_content')!=""){  ?> 
                                jQuery(".new_content_header_submenu").hide();
                        <?php } ?>					

                <?php } ?>	
                }
                
                if(isWPHigherOrEqualThan("3.3")){
                        <?php if(get_option('agca_admin_bar_comments')!=""){  ?>
                                jQuery("ul#wp-admin-bar-root-default li#wp-admin-bar-comments").css("display","none");
                        <?php } ?>
                        <?php if(get_option('agca_admin_bar_new_content')!=""){  ?> 
                                jQuery("ul#wp-admin-bar-root-default li#wp-admin-bar-new-content").css("display","none");								
                        <?php } ?>
                        <?php if(get_option('agca_admin_bar_new_content_post')!=""){  ?>
                                jQuery("ul#wp-admin-bar-root-default li#wp-admin-bar-new-content li#wp-admin-bar-new-post").css("display","none");
                        <?php } ?>
                        <?php if(get_option('agca_admin_bar_new_content_link')!=""){  ?>
                                jQuery("ul#wp-admin-bar-root-default li#wp-admin-bar-new-content li#wp-admin-bar-new-link").css("display","none");
                        <?php } ?>
                        <?php if(get_option('agca_admin_bar_new_content_page')!=""){  ?>
                                jQuery("ul#wp-admin-bar-root-default li#wp-admin-bar-new-content li#wp-admin-bar-new-page").css("display","none");
                        <?php } ?>
                        <?php if(get_option('agca_admin_bar_new_content_user')!=""){  ?>
                                jQuery("ul#wp-admin-bar-root-default li#wp-admin-bar-new-content li#wp-admin-bar-new-user").css("display","none");
                        <?php } ?>
                        <?php if(get_option('agca_admin_bar_new_content_media')!=""){  ?>
                                jQuery("ul#wp-admin-bar-root-default li#wp-admin-bar-new-content li#wp-admin-bar-new-media").css("display","none");
                        <?php } ?>								
                        <?php if(get_option('agca_admin_bar_update_notifications')!=""){  ?>
                                jQuery("ul#wp-admin-bar-root-default li#wp-admin-bar-updates").css("display","none");
                        <?php } ?>
                }
                
                
                <?php if(get_option('agca_header_logo')==true){ ?>
                                jQuery("#wphead #header-logo").css("display","none");							
                                jQuery("ul#wp-admin-bar-root-default li#wp-admin-bar-wp-logo").css("display","none");

                <?php } ?>
                <?php if(get_option('agca_header_logo_custom')!=""){ ?>	


                                if(isWPHigherOrEqualThan("3.3")){
                                        var img_url = '<?php echo addslashes(get_option('agca_header_logo_custom')); ?>';							

                                        advanced_url = img_url;
                                        image = jQuery("<img />").attr("src",advanced_url);								
                                        jQuery(image).load(function() {										
                                                jQuery("#wpbody-content").prepend(image);
                                        });
                                }else{
                                        jQuery("#wphead img#header-logo").attr('src','');
                                        jQuery("#wphead img#header-logo").hide(); 							
                                        var img_url = '<?php echo addslashes(get_option('agca_header_logo_custom')); ?>';							
                                        advanced_url = img_url+ "?" + new Date().getTime();
                                        image = jQuery("<img />").attr("src",advanced_url);								
                                        jQuery(image).load(function() {	
                                                jQuery("#wphead img#header-logo").attr('src', advanced_url);
                                                jQuery("#wphead img#header-logo").attr('width',this.width);			
                                                jQuery("#wphead img#header-logo").attr('height',this.height);	
                                                jQuery("#wphead").css('height', (14 + this.height)+'px');
                                                jQuery("#wphead img#header-logo").show();										
                                        });
                                }					

                <?php } ?>	
                <?php if(get_option('agca_wp_logo_custom')!=""){ ?>		                                                                     
                                         jQuery("li#wp-admin-bar-wp-logo a.ab-item span.ab-icon").html("<img style=\"height:28px;margin-top:-4px\" src=\"<?php echo get_option('agca_wp_logo_custom'); ?>\" />");
                                         jQuery("li#wp-admin-bar-wp-logo a.ab-item span.ab-icon").css('background-image','none');
                                         jQuery("li#wp-admin-bar-wp-logo a.ab-item span.ab-icon").css('width','auto');										 									 
                                         jQuery("li#wp-admin-bar-wp-logo a.ab-item").attr('href',"<?php echo get_bloginfo('wpurl'); ?>");                                       
                                         jQuery("#wpadminbar #wp-admin-bar-root-default > #wp-admin-bar-wp-logo .ab-item:before").attr('title','');    
										 jQuery('body #wpadminbar #wp-admin-bar-wp-logo > .ab-item .ab-icon').attr('class','ab-icon2');	
                <?php }?>
				<?php if(get_option('agca_remove_site_link')==true){ ?>
                                jQuery("#wp-admin-bar-site-name").css("display","none");							                            

                <?php } ?>
                <?php if(get_option('agca_wp_logo_custom_link')!=""){ ?>						
                                if(isWPHigherOrEqualThan("3.3")){       
                                         var href = "<?php echo get_option('agca_wp_logo_custom_link'); ?>";                                                        
                                         href = href.replace("%BLOG%", "<?php echo get_bloginfo('wpurl'); ?>");
                                         if(href == "%SWITCH%"){                                         
                                            href = "<?php echo get_bloginfo('wpurl'); ?>";
                                            <?php if($this->context == "page"){
                                                ?>href+="/wp-admin";<?php    
                                            }
                                            ?>
                                         }
                                         jQuery("li#wp-admin-bar-wp-logo a.ab-item").attr('href',href);                                        
                                }
                <?php }?>
                <?php if(get_option('agca_site_heading')==true){ ?>
                                jQuery("#wphead #site-heading").css("display","none");
                <?php } ?>
                <?php if(get_option('agca_custom_site_heading')!=""){ ?>	
                                jQuery("#wphead #site-heading").after('<h1><?php echo addslashes(get_option('agca_custom_site_heading')); ?></h1>');
                                //3.3FIX
                                if(isWPHigherOrEqualThan("3.3")){
                                        jQuery("#wp-admin-bar-site-name a:first").html('<?php echo addslashes(get_option('agca_custom_site_heading')); ?>');
                                }
                <?php } ?>	                           
                <?php if(get_option('agca_header')==true && $this->context =='admin'){ 										
										?>
                                        jQuery("#wpadminbar").css("display","none");	
                                        jQuery("body.admin-bar").css("padding-top","0");
                                        jQuery("#wphead").css("display","none");  
										jQuery('html.wp-toolbar').css("padding-top","0");									

                <?php } ?>	
                <?php if((get_option('agca_header')==true)&&(get_option('agca_header_show_logout')==true)){ ?>									
								<?php
									$agca_logout_text = ((get_option('agca_logout')=="")?"Log Out":get_option('agca_logout'));
								?>
                                if(isWPHigherOrEqualThan("3.3")){	
                                jQuery("#wpbody-content").prepend('<a href="../wp-login.php?action=logout" tabindex="10" style="float:right;margin-right:20px" class="ab-item agca_logout_button"><?php echo $agca_logout_text; ?></a>');								
                                }else{
                                        var clon ="";
                                        jQuery("div#user_info a").each(function(){
                                                if(jQuery(this).text() =="Log Out"){
                                                        clon = jQuery(this).clone();
                                                }								
                                        });
                                        if(clon !=""){
                                                jQuery(clon).attr('style','float:right;padding:15px');	
                                                jQuery(clon).html('<?php echo $agca_logout_text; ?>');	
                                        }													
                                        jQuery("#wphead").after(clon);
                                }

                <?php } ?>
                <?php if(get_option('agca_howdy')!=""){ ?>
                                    if(isWPHigherOrEqualThan("3.5")){	
                                                    var alltext="";								
                                                    alltext="";
                                                    jQuery('li#wp-admin-bar-my-account').css('cursor','default');
                                                    alltext = jQuery('li#wp-admin-bar-my-account').html();
                                                    if(alltext!=null){                                                        								
                                                        var parts = alltext.split(',');	
                                                        alltext = "<?php echo get_option('agca_howdy'); ?>" + ", " + parts[1];
                                                    }    
                                                    jQuery("li#wp-admin-bar-my-account").html("<a href=\"#\" class=\"ab-item\">"+alltext+"</a>");
                                    }else if(isWPHigherOrEqualThan("3.3")){	
                                                    var alltext="";								
                                                    alltext="";
                                                    jQuery('li#wp-admin-bar-my-account').css('cursor','default');
                                                    alltext = jQuery('li#wp-admin-bar-my-account').html();
                                                    if(alltext!=null){                                                        								
                                                        var parts = alltext.split(',');	
                                                        alltext = "<?php echo get_option('agca_howdy'); ?>" + ", " + parts[1];
                                                    }    
                                                    jQuery("li#wp-admin-bar-my-account").html(alltext);
                                    }else if(isWPHigherOrEqualThan("3.2")){	
                                                    var alltext="";								
                                                    alltext="";
                                                    alltext = jQuery('#user_info div.hide-if-no-js').html();
                                                    if(alltext!=null){
                                                        var parts = alltext.split(',');	
                                                        alltext = "<?php echo get_option('agca_howdy'); ?>" + ", " + parts[1];									
                                                    }
                                                    jQuery("#user_info div.hide-if-no-js").html(alltext);
                                                    
                                    }else{	
                                                    var howdyText = jQuery("#user_info").html();
                                                    if(howdyText !=null){
                                                    jQuery("#user_info").html("<p>"+"<?php echo get_option('agca_howdy'); ?>"+howdyText.substr(9));
                                            }
                                    }
                                    if(isWPHigherOrEqualThan("3.5")){
                                        //jQuery("#wp-admin-bar-my-account").css("padding-left","10px");
                                        //jQuery("#wp-admin-bar-my-account").css("padding-right","10px");
                                        //jQuery("#wp-admin-bar-my-account img").css({"margin-left":"5px","margin-bottom":"-4px"});
                                        
                                    }
                                    
                    <?php } ?>
					<?php 
					 if(get_option('agca_custom_title')!=""){
							//add_filter('admin_title', '$this->change_title', 10, 2);                               
							                              
                     } 
					 ?>
                    <?php if(get_option('agca_logout')!=""){ ?>						
                                    if(isWPHigherOrEqualThan("3.3")){
                                            jQuery("ul#wp-admin-bar-user-actions li#wp-admin-bar-logout a").text("<?php echo get_option('agca_logout'); ?>");
                                    }else if(isWPHigherOrEqualThan("3.2")){
                                            jQuery("#user_info #user_info_links a:eq(1)").text("<?php echo get_option('agca_logout'); ?>");
                                    }else{
                                            jQuery("#user_info a:eq(1)").text("<?php echo get_option('agca_logout'); ?>");
                                    }

                    <?php } ?>
                    <?php if(get_option('agca_remove_your_profile')==true){ ?>	
                                    if(isWPHigherOrEqualThan("3.3")){
                                            jQuery("ul#wp-admin-bar-user-actions li#wp-admin-bar-edit-profile").css("visibility","hidden");
                                            jQuery("ul#wp-admin-bar-user-actions li#wp-admin-bar-edit-profile").css("height","10px");
                                            jQuery('#wpadminbar #wp-admin-bar-top-secondary > #wp-admin-bar-my-account > a').attr('href','#');
                                            jQuery('#wpadminbar #wp-admin-bar-top-secondary #wp-admin-bar-user-info > a').attr('href','#');
                                            jQuery('#wpadminbar #wp-admin-bar-top-secondary #wp-admin-bar-edit-profile > a').attr('href','#');
                                            
                                    }else if(isWPHigherOrEqualThan("3.2")){
                                            jQuery("#user_info #user_info_links li:eq(0)").remove();
                                    }					
                    <?php } ?>						
                    <?php if(get_option('agca_logout_only')==true){ ?>	
                                    if(isWPHigherOrEqualThan("3.3")){
                                            var logout_content = jQuery("li#wp-admin-bar-logout").html();
                                            jQuery("ul#wp-admin-bar-top-secondary").html('<li id="wp-admin-bar-logout">'+ logout_content +'</li>');
                                    }else if(isWPHigherOrEqualThan("3.2")){
                                            var logoutText = jQuery("#user_info a:nth-child(2)").text();
                                            <?php if(get_option('agca_logout')!=""){ ?>
                                                    logoutText = "<?php echo get_option('agca_logout'); ?>";
                                            <?php } ?>
                                            var logoutLink = jQuery("#user_info a:nth-child(2)").attr("href");						
                                            jQuery("#user_info").html("<a href=\""+logoutLink+"\" title=\"Log Out\">"+logoutText+"</a>");
                                    }else{
                                            var logoutText = jQuery("#user_info a:nth-child(2)").text();
                                            var logoutLink = jQuery("#user_info a:nth-child(2)").attr("href");						
                                            jQuery("#user_info").html("<a href=\""+logoutLink+"\" title=\"Log Out\">"+logoutText+"</a>");
                                    }						
                    <?php } ?>
                
                <?php
                
                
        }
		
	function updateAllColors(){
			
			?> 
			function updateAllColors(){
			<?php
						 foreach($this->colorizer as $k => $v){
							if(($k !="") and ($v !="")){							
								?> updateTargetColor("<?php echo $k;?>","<?php echo $v;?>"); <?php
							}
						}
						?>
						jQuery('.color_picker').each(function(){		
						updateColor(jQuery(this).attr('id'),jQuery(this).val())
					});
					jQuery('label,h1,h2,h3,h4,h5,h6,a,p,.form-table th,.form-wrap label').css('text-shadow','none');
                                        jQuery('#adminmenu li.wp-menu-open').css('border','none');
                                        jQuery('#adminmenu li.wp-menu-open .wp-submenu').css({'border':'none','margin':'0px','border-radius':'0px'}); 
			}<?php
 
	}
	function admin_capabilities(){
		global $wp_roles;
		$capabs = $wp_roles->roles['administrator']['capabilities'];
		$capabilitySelector = "";
		
		$selectedValue = get_option('agca_admin_capability');		
		if($selectedValue == ""){
			$selectedValue = "edit_dashboard";
		}
		
		foreach($capabs as $k=>$v){	
				$selected = "";
				if($selectedValue == $k){
					$selected = " selected=\"selected\" ";
				}
				$capabilitySelector .="<option val=\"$k\" $selected >".$k."</option>\n";
		}
		
		$this->admin_capabilities  = "<select class=\"agca-selectbox\" id=\"agca_admin_capability\"  name=\"agca_admin_capability\" val=\"upload_files\">".$capabilitySelector."</select>";
	}
	
	function admin_capability(){
		$selectedValue = get_option('agca_admin_capability');		
		if($selectedValue == ""){
			$selectedValue = "edit_dashboard";
		}
		return $selectedValue;
	}
	
	function JSPrintAGCATemplateSettingsVar($settings){
		echo "\n<script type=\"text/javascript\">\n";
		echo "var agca_template_settings = ".preg_replace('#<script(.*?)>(.*?)</script>#is', '', $settings).";\n";	//TODO: think about this				
		echo "</script>";	
	}
	
	function appendSettingsToAGCATemplateCustomizations($customizations, $settings){
		$template_settings = json_decode($settings);
	    //print_r($template_settings);
		foreach($template_settings as $sett){
			$key = $sett->code;
							
			//use default value if user's value is not set
			$value="";
			if($sett->value != ""){
				$value = $sett->value;						
			}else{
				$value = $sett->default_value;						
			}
			
			//Prepare settings					
			if($sett->type == 6){
				if($value !== null && (strtolower($value) == "on" || $value == "1")){
					$value = "true";
				}else{
					$value = "false";
				}						
			}								
			$customizations = str_replace("%".$key."%",$value, $customizations);						
		}	
		return $customizations;
	}
	
	function enableSpecificWPVersionCustomizations($customizations){	
		/*enable special CSS for this WP version*/	
		$ver = $this->get_wp_version();		
		$customizations = str_replace("/*".$ver," ", $customizations);
		$customizations = str_replace($ver."*/"," ", $customizations);
		return $customizations;
	}
	
	function removeCSSComments($customizations){				
		$customizations = preg_replace('#/\*.*?\*/#si','',$customizations);
		return $customizations;
	}
	
	function prepareAGCAAdminTemplates(){
		if(get_option( 'agca_templates' ) != ""){
			//print_r(get_option( 'agca_templates' ));
			$templates = get_option( 'agca_templates' );
			foreach($templates as $templname=>$templdata){
				if($templname == get_option('agca_selected_template')){
					
					echo ($templdata['common']);
					echo "<!--AGCAIMAGES: ".$templdata['images']."-->";
				 if(!((get_option('agca_role_allbutadmin')==true) and  (current_user_can($this->admin_capability())))){	
					if($templdata['settings'] == "") $templdata['settings'] = "{}";		
					//print_r($templdata);															
					
					$this->JSPrintAGCATemplateSettingsVar($templdata['settings']);
					
					$admindata = $this->appendSettingsToAGCATemplateCustomizations($templdata['admin'], $templdata['settings']);	
					$admindata = $this->enableSpecificWPVersionCustomizations($admindata);
					$admindata = $this->removeCSSComments($admindata);											
					
					//echo $admindata;
					//REPLACE TAGS WITH CUSTOM TEMPLATE SETTINGS					
					$this->templateCustomizations = $admindata;
				 }				
					break;
				}
			}
		}
	}
	
	function agcaAdminSession(){
		$agcaTemplatesSession = array();
		
		//session_destroy();
		//session_unset();
		
		/*if(!session_id()){
			session_start();		
		}*/
		
		if(!isset($_SESSION["AGCA"])){
			$_SESSION["AGCA"] = array();			
			$_SESSION["AGCA"]["Templates"] = array();				
		}
		//print_r($_SESSION);
		
		if(isset($_SESSION["AGCA"])){
			if(isset($_SESSION["AGCA"]["Templates"])){
				//print_r($_SESSION["AGCA"]["Templates"]);
				$agcaTemplatesSession = json_encode($_SESSION["AGCA"]["Templates"]);				
			}
		}
		
		
		if($agcaTemplatesSession == '""' || $agcaTemplatesSession == '"[]"'){	
			$agcaTemplatesSession = array();
		}
		
		
		return $agcaTemplatesSession;
		
	}
	
	function prepareAGCALoginTemplates(){
		if(get_option( 'agca_templates' ) != ""){
			//print_r(get_option( 'agca_templates' ));
			$templates = get_option( 'agca_templates' );
			foreach($templates as $templname=>$templdata){
				if($templname == get_option('agca_selected_template')){
					echo ($templdata['common']);				
					
					if($templdata['settings'] == "") $templdata['settings'] = "{}";						
					$this->JSPrintAGCATemplateSettingsVar($templdata['settings']);
					
					$logindata = $this->appendSettingsToAGCATemplateCustomizations($templdata['login'], $templdata['settings']);					
					$logindata = $this->enableSpecificWPVersionCustomizations($logindata);
					$logindata = $this->removeCSSComments($logindata);				
					
					echo($logindata);
					break;
				}
			}
		}
	}
        
        function agca_error_check(){
            ?>
                <script type="text/javascript"> 
                 function AGCAErrorPage(msg, url, line){
                     var title = 'AG Custom Admin just caught a JavaScript error on this site:\n\n '+ msg +'\n' + url + '\n' + line+'\n\nThis error prevents AG Custom Admin to work properly. To fix this, please analyse this error message and try to find the source. You can also check browser\'s console to see this error. \n\nIf you need more help, just click on this error message';
                        document.getElementsByTagName('html')[0].style.visibility = "visible";
                        document.body.innerHTML += '<div style="position:absolute;width:auto;height:auto;padding:4px;right:0;top:0;z-index:99999;background:#ff0000;color:#ffffff";border:3px solid #ffffff;><a target="_blank" href="http://agca.argonius.com/ag-custom-admin/ag_custom_admin/error-ocurred-javascript-error-caught" title="'+title+'" style="color:#ffffff;text-decoration:none;font-weight:bold;">Error Ocurred</a></div>';			
		}
                window.onerror = function(msg, url, line) {                   
                    window.onload = function() {
                        AGCAErrorPage(msg, url, line);
                    }                                  
                   return true;
                };
                </script>
            <?php
        }

	function print_admin_css()
	{	
		$agcaTemplateSession = $this->agcaAdminSession();
		$wpversion = $this->get_wp_version();	
		$this->context = "admin";
                $this->agca_error_check();
		?>                 
		<script type="text/javascript">
			var wpversion = "<?php echo $wpversion; ?>";
			var agca_debug = <?php echo ($this->agca_debug)?"true":"false"; ?>;
			var agca_version = "<?php echo $this->agca_version; ?>";
			var agcaTemplatesSession = <?php echo ($agcaTemplateSession==null)?"[]":$agcaTemplateSession; ?>;
			var errors = false;
			var isSettingsImport = false;
			var agca_context = "admin";
			var roundedSidberSize = 0;		
			var agca_installed_templates = <?php echo $this->get_installed_agca_templates(); ?>;
		</script>
		<?php
		$this->prepareAGCAAdminTemplates();
		$this->agca_get_includes();
		$this->admin_capabilities();		
		get_currentuserinfo() ;		
				
	?>	
<?php
	//in case that javaScript is disabled only admin can access admin menu
	if(!current_user_can($this->admin_capability())){
	?>
		<style type="text/css">
			#adminmenu{display:none;}
		</style>
	<?php
	}
?>	
<script type="text/javascript">
document.write('<style type="text/css">html{visibility:hidden;}</style>');
<?php
if(isset($_POST['_agca_import_settings']) && $_POST['_agca_import_settings']=='true'){
    echo 'isSettingsImport = true;';
}
?>    
</script>
<?php if(get_option('agca_admin_menu_arrow') == true){ ?>											
	<style type="text/css">
		.wp-has-current-submenu:after{border:none !important;}
		#adminmenu li.wp-has-submenu.wp-not-current-submenu.opensub:hover:after{border:none !important;}
	</style>										
<?php } ?>
<script type="text/javascript">
  /* <![CDATA[ */
jQuery(document).ready(function() {
try
  {  				
				
				<?php /*CHECK OTHER PLUGNS*/	
					$this->check_active_plugin(); 
					
					if($this->active_plugin["ozh"]){
						?> 
							jQuery('ul#adminmenu').css('display','none'); 
							jQuery('#footer-ozh-oam').css('display','none');	
							jQuery('#ag_main_menu li').each(function(){
								if(jQuery(this).text() == "Admin Menu"){
									jQuery(this).hide();
								}
							});							
						<?php
					}
				?>
		

				//get saved onfigurations	
					<?php	$checkboxes = $this->jsonMenuArray(get_option('ag_edit_adminmenu_json'),'0');	?>

					var checkboxes = <?php echo "[".$checkboxes."]"; ?>;
					
					<?php	$textboxes = $this->jsonMenuArray(get_option('ag_edit_adminmenu_json'),'1');	?>
					var textboxes = <?php echo "[".$textboxes."]"; ?>;
					
					<?php	$buttons = $this->jsonMenuArray(get_option('ag_add_adminmenu_json'),'buttons');	?>
					var buttons = '<?php echo $buttons; ?>';	
					
					<?php	$buttonsJq = $this->jsonMenuArray(get_option('ag_add_adminmenu_json'),'buttonsJq');	?>
					var buttonsJq = '<?php echo $buttonsJq; ?>';				
					
					<?php if($wpversion >=3.5 ){ ?>
						createEditMenuPageV35(checkboxes,textboxes);
					<?php }else if($wpversion >=3.2 ){ ?>
						createEditMenuPageV32(checkboxes,textboxes);
					<?php }else{ ?>
						createEditMenuPage(checkboxes,textboxes);
					<?php } ?>
                                            //console.log(checkboxes);
                                           // console.log(textboxes);
			
		<?php
		//if admin, and option to hide settings for admin is set	
		
		if((get_option('agca_role_allbutadmin')==true) and current_user_can($this->admin_capability())){	
		?>				
		<?php } else{ ?>
                                        <?php if(get_option('agca_admin_menu_brand')!=""){ ?>
                                             additionalStyles = "";
                                             if(isWPHigherOrEqualThan("3.4")){
                                                 additionalStyles = ' style="margin-bottom:-4px" ';
                                             }
                                             jQuery("#adminmenu").before('<div '+additionalStyles+' id="sidebar_adminmenu_logo"><img width="160" src="<?php echo get_option('agca_admin_menu_brand'); ?>" /></div>');
                                             
                                        <?php } ?> 
                                         <?php if(get_option('agca_admin_menu_brand_link')!=""){ ?>					
                                                      
                                                    var href = "<?php echo get_option('agca_admin_menu_brand_link'); ?>";                                                        
                                                    href = href.replace("%BLOG%", "<?php echo get_bloginfo('wpurl'); ?>");

                                                    jQuery("#sidebar_adminmenu_logo").attr('onclick','window.open(\"'+ href+ '\");');                                         
                                                    jQuery("#sidebar_adminmenu_logo").attr('title',href); 
                                               
                                            <?php }else{ ?>
                                                     href = "<?php echo get_bloginfo('wpurl'); ?>";
                                                     jQuery("#sidebar_adminmenu_logo").attr('onclick','window.open(\"'+ href+ '\");');                                        
                                                     jQuery("#sidebar_adminmenu_logo").attr('title',href);
                                        <?php } ?>
                                       
					<?php if(get_option('agca_admin_menu_submenu_round')==true){ ?>
							jQuery("#adminmenu .wp-submenu").css("border-radius","<?php echo get_option('agca_admin_menu_submenu_round_size'); ?>px");
							jQuery("#adminmenu .wp-menu-open .wp-submenu").css('border-radius','');
							<?php $roundedSidebarSize = get_option('agca_admin_menu_submenu_round_size'); ?>
                                 roundedSidberSize = <?php echo ($roundedSidebarSize == "")?"0":$roundedSidebarSize; ?>;
                                                        
                                                        
					<?php } ?>
					<?php if(get_option('agca_admin_menu_autofold')=="force"){ ?>                                                     
                                jQuery("body").addClass("auto-fold");                                               
                    <?php } else if(get_option('agca_admin_menu_autofold')=="disable"){ ?>
								jQuery("body").removeClass("auto-fold");                                               
					<?php } ?>
                                            
                                        <?php $this->print_admin_bar_scripts(); ?>
						
			
					<?php if(get_option('agca_screen_options_menu')==true){ ?>
							jQuery("#screen-options-link-wrap").css("display","none");
					<?php } ?>	
					<?php if(get_option('agca_help_menu')==true){ ?>
							jQuery("#contextual-help-link-wrap").css("display","none");
							jQuery("#contextual-help-link").css("display","none");							
					<?php } ?>	
					<?php if(get_option('agca_options_menu')==true){ ?>
							jQuery("#favorite-actions").css("display","none");
					<?php } ?>	
					<?php if(get_option('agca_privacy_options')==true){ ?>
							jQuery("#privacy-on-link").css("display","none");
					<?php } ?>	
					
					
						
					
					<?php if(get_option('agca_update_bar')==true){ ?>							
                                                        <?php
                                                        if ( ! function_exists( 'c2c_no_update_nag' ) ) :
                                                        function c2c_no_update_nag() {
                                                            remove_action( 'admin_notices', 'update_nag', 3 );
                                                        }
                                                        endif;
                                                        add_action( 'admin_init', 'c2c_no_update_nag' );
                                                        ?>
                                                        jQuery("#update-nag").css("display","none");
							jQuery(".update-nag").css("display","none");
					<?php } ?>
						
					<?php if(get_option('agca_footer')==true){ ?>
							jQuery("#footer,#wpfooter").css("display","none");
					<?php } ?>					
						

					
					<?php if(get_option('agca_footer_left')!=""){ ?>												
								jQuery("#footer-left").html('<?php echo addslashes(get_option('agca_footer_left')); ?>');
					<?php } ?>	
					<?php if(get_option('agca_footer_left_hide')==true){ ?>											
								jQuery("#footer-left").css("display","none");
					<?php } ?>
					<?php if(get_option('agca_footer_right')!=""){ ?>
								jQuery("#footer-upgrade").html('<?php echo addslashes(get_option('agca_footer_right')); ?>');
					<?php } ?>
					<?php if(get_option('agca_footer_right_hide')==true){ ?>											
								jQuery("#footer-upgrade").css("display","none");
					<?php } ?>
					
					<?php if(get_option('agca_language_bar')==true){ ?>
							jQuery("#user_info p").append('<?php include("language_bar/language_bar.php"); ?>');
					<?php } ?>					
					<?php if(get_option('agca_dashboard_icon')==true){ ?>
							var className = jQuery("#icon-index").attr("class");
							if(className=='icon32'){
								jQuery("#icon-index").attr("id","icon-index-removed");
							}
					<?php } ?>
					<?php if(get_option('agca_dashboard_text')!=""){ ?>							
							jQuery("#dashboard-widgets-wrap").parent().find("h2").html("<?php echo addslashes(get_option('agca_dashboard_text')); ?>");
					<?php } ?>
					<?php if(get_option('agca_dashboard_text_paragraph')!=""){ 
                                                        require_once(ABSPATH . 'wp-includes/formatting.php');
                                        ?>	
                                                        jQuery("#wpbody-content #dashboard-widgets-wrap").before('<div id="agca_custom_dashboard_content"></div>');
                                                       
							jQuery("#agca_custom_dashboard_content").html('<br /><?php echo preg_replace('/(\r\n|\r|\n)/', '\n', addslashes(wpautop(get_option('agca_dashboard_text_paragraph')))); ?>');
					<?php } ?>
					
					<?php /*Remove Dashboard widgets*/ ?>
					<?php			

                        if(get_option('agca_dashboard_widget_welcome')==true){
							?>jQuery("#welcome-panel").css("display","none");<?php
						}else{
							?>jQuery("#welcome-panel").css("display","block");<?php
						}						
						if(get_option('agca_dashboard_widget_il')==true){
							$this->remove_dashboard_widget('dashboard_incoming_links','normal');
						}else{
							?>jQuery("#dashboard_incoming_links").css("display","block");<?php
						}
						if(get_option('agca_dashboard_widget_plugins')==true){
							$this->remove_dashboard_widget('dashboard_plugins','normal');
						}else{
							?>jQuery("#dashboard_plugins").css("display","block");<?php
						}
						if(get_option('agca_dashboard_widget_qp')==true){
							$this->remove_dashboard_widget('dashboard_quick_press','side');
						}else{
							?>jQuery("#dashboard_quick_press").css("display","block");<?php
						}
						if(get_option('agca_dashboard_widget_rn')==true){
							$this->remove_dashboard_widget('dashboard_right_now','normal');
						}else{
							?>jQuery("#dashboard_right_now").css("display","block");<?php
						}
						if(get_option('agca_dashboard_widget_rd')==true){
							$this->remove_dashboard_widget('dashboard_recent_drafts','side');
						}else{
							?>jQuery("#dashboard_recent_drafts").css("display","block");<?php
						}
						if(get_option('agca_dashboard_widget_primary')==true){
							$this->remove_dashboard_widget('dashboard_primary','side');
						}else{
							?>jQuery("#dashboard_primary").css("display","block");<?php
						}
						if(get_option('agca_dashboard_widget_secondary')==true){
							$this->remove_dashboard_widget('dashboard_secondary','side');
						}else{
							?>jQuery("#dashboard_secondary").css("display","block");<?php
						}	
						if(get_option('agca_dashboard_widget_activity')==true){
							remove_meta_box( 'dashboard_activity', 'dashboard', 'normal');
						}else{
							?>jQuery("#dashboard_activity").css("display","block");<?php
						}	
						
					?>	
					
			
					
					<?php /*ADMIN MENU*/ ?>							
								
					
							<?php if(get_option('agca_admin_menu_separator_first')==true){ ?>											
								jQuery("li.wp-menu-separator").eq(0).css("display","none");
							<?php } ?>
							<?php if(get_option('agca_admin_menu_separator_second')==true){ ?>											
								jQuery("li.wp-menu-separator").eq(1).css("display","none");
							<?php } ?>	
							<?php if(get_option('agca_admin_menu_icons') == true){ ?>											
										jQuery(".wp-menu-image").each(function(){
											jQuery(this).css("display","none");
										});
										jQuery('#adminmenu div.wp-menu-name').css('padding','8px');
							<?php } ?>
                                                        <?php if(get_option('agca_admin_menu_arrow') == true){ ?>											
								jQuery("#adminmenu .wp-menu-arrow").css("visibility","hidden");							
										
							<?php } ?>
					<?php if(get_option('agca_admin_menu_turnonoff') == 'on'){ ?>
					
					<?php /*If Turned on*/ ?>
					
                                                       
							
							<?php if(get_option('agca_admin_menu_agca_button_only') == true){ ?>											
								jQuery('#adminmenu > li').each(function(){
									if(!jQuery(this).hasClass('agca_button_only')){
										jQuery(this).addClass('noclass');
									}
								});
							 <?php } ?>	
                                                             
                                                        <?php /*Only admin see button*/
							if (current_user_can($this->admin_capability())){ ?>							
								jQuery('#adminmenu').append('<?php echo $this->agca_create_admin_button('AG Custom Admin',array('value'=>'tools.php?page=ag-custom-admin/plugin.php','target'=>'_self')); ?>');
							<?php } ?>
													
							<?php /*EDIT MENU ITEMS*/?>
							<?php if(get_option('ag_edit_adminmenu_json')!=""){ 											
									
									?>			
										var checkboxes_counter = 0;
										var createAGCAbutton = false;
									//console.log(checkboxes);							
									//console.log(textboxes);
									<?php //loop through original menu and hide and change elements according to user setttngs ?>																		

										var topmenuitem;
										jQuery('ul#adminmenu > li').each(function(){											
											
											if(!jQuery(this).hasClass("wp-menu-separator") && !jQuery(this).hasClass("wp-menu-separator-last")){
												//alert(checkboxes[checkboxes_counter]);
												
												topmenuitem = jQuery(this).attr('id');
												//console.log(jQuery(this));										
												
												var matchFound = false;
												var subMenus = "";
												
												for(i=0; i< checkboxes.length ; i++){
												
													if(checkboxes[i][0].indexOf("<-TOP->") >=0){ //if it is top item													
														if(checkboxes[i][0].replace('<-TOP->','') == topmenuitem){//if found match in menu, with top item in array															
															matchFound = true;		
															//console.log(checkboxes[i][0]);
                                                                                                                        //console.log(jQuery(this).find('.wp-menu-name').text());															
															
                                                                                                                        <?php if($wpversion >=3.5 ){ ?>
                                                                                                                            jQuery(this).find('.wp-menu-name').html(textboxes[i][1]);
                                                                                                                        <?php }else{ ?>
                                                                                                                            jQuery(this).find('a').eq(1).html(textboxes[i][1]);
                                                                                                                        <?php } ?>
                                                                                                                        
															if((checkboxes[i][1] == "true") || (checkboxes[i][1] == "checked")){
																jQuery(this).addClass('noclass');
															}
															
															i++;
															var selector = '#' + topmenuitem + ' ul li';
															var hoverPopupSelector = '#' + topmenuitem + ' .wp-submenu.wp-submenu-wrap';
															//console.log(i+" "+checkboxes);	
																var allSubmenuItemsHidden = true;															
																while((i<checkboxes.length) && (checkboxes[i][0].indexOf("<-TOP->") < 0)){															
																	jQuery(selector).each(function(){ //loop through all submenus	
                                                                                                                                            var currentItemText = "";                                                                                                                                           
                                                                                                                                            
                                                                                                                                             <?php if($wpversion >=3.5 ){ ?>
                                                                                                                                                currentItemText = jQuery(this).clone();
                                                                                                                                                jQuery(currentItemText).find("span").remove();
                                                                                                                                                currentItemText = currentItemText.text();
                                                                                                                                            <?php }else{ ?>
                                                                                                                                                currentItemText = jQuery(this).text();
                                                                                                                                            <?php } ?>

                                                                                                                                             //console.log("*"+checkboxes[i][0]+":"+withoutNumber+"*");
																		if(checkboxes[i][0] == currentItemText){
                                                                                                                                                   
																			if((checkboxes[i][1] == "true") || (checkboxes[i][1] == "checked")){
																				jQuery(this).addClass('noclass');
																			}else{
																				allSubmenuItemsHidden = false;
																			}
																			jQuery(this).find('a').text(textboxes[i][1]);																			
																		}
																	});
																	i++;
																}
																if(allSubmenuItemsHidden){
																	jQuery(hoverPopupSelector).hide();
																}																
														};
													}												
												}
												//console.log(subMenus);					
												//checkboxes_counter++;
											}
										});								
									<?php
							 } ?>
							
							
							/*Add user buttons*/					
							jQuery('#adminmenu').append(buttons);						
							
							
					<?php /*END If Turned on*/ ?>
					<?php } else{ ?>
							jQuery("#adminmenu").removeClass("noclass");
					<?php } ?>				
					
					reloadRemoveButtonEvents();

					<?php if(get_option('agca_admin_menu_collapse_button') != true){ ?>
						//add collapse menu button
						jQuery('#adminmenu').append('<li class="hide-if-no-js" onclick="window.setTimeout(function(){updateAllColors();},10);" id="collapse-menu"><div id="collapse-button"><div></div></div><span>Collapse menu</span></li>');					
					<?php } ?>				
					
				
					
					<?php //COLORIZER ?>
					updateAllColors();
					<?php //COLORIZER END ?>				
<?php } //end of apply for any user except admin ?>		
/*Add user buttons*/	
jQuery('#ag_add_adminmenu').append(buttonsJq); 	

                               
 }catch(err){	
	errors = "AGCA - ADMIN ERROR: " + err.name + " / " + err.message;
	console.log(errors);		
 }finally{
	jQuery('html').css('visibility','visible');		
 }  
 <?php
 if($this->saveAfterImport == true){
     ?>savePluginSettings();<?php
 }
 ?>
 
 });
 
 <?php if(get_option('agca_colorizer_turnonoff') == 'on'){
	$this->updateAllColors();
  }else{
	?>function updateAllColors(){}; <?php
	}  ?>

                      
 /* ]]> */   
</script>
		<style type="text/css">
			.underline_text{
				text-decoration:underline;
			}
			.form-table th{
				width:300px;
			}
			
			/*3.3.FIX*/
			#dashboard-widgets div.empty-container{				
				display:none;
			}
		</style>
	<?php 	
	}
	
	function print_login_head(){
		$this->context = "login";	
		$wpversion = $this->get_wp_version();
                $this->agca_error_check();
		?>
		<script type="text/javascript">		
		 document.write('<style type="text/css">html{visibility:hidden;}</style>');		 
		 var agca_version = "<?php echo $this->agca_version; ?>";
		 <?php //var wpversion = "echo $wpversion; ?>
		 var agca_debug = <?php echo ($this->agca_debug)?"true":"false"; ?>;
         var isSettingsImport = false;
         var agca_context = "login";				 
		</script>
		<?php
		$this->prepareAGCALoginTemplates();
		$this->agca_get_includes();		
		
	?>	
	     	
		<script type="text/javascript">
				 
				 
        /* <![CDATA[ */
            jQuery(document).ready(function() {			
				try{ 
                                        <?php if(get_option('agca_login_round_box')==true){ ?>
							jQuery("form#loginform").css("border-radius","<?php echo get_option('agca_login_round_box_size'); ?>px");
                                                        jQuery("#login h1 a").css("border-radius","<?php echo get_option('agca_login_round_box_size'); ?>px");
                                                        jQuery("#login h1 a").css("margin-bottom",'10px');
                                                        jQuery("#login h1 a").css("padding-bottom",'0');
					<?php } ?>
					<?php if(get_option('agca_login_banner')==true){ ?>
							jQuery("#backtoblog").css("display","none");
					<?php } ?>	
					<?php if(get_option('agca_login_banner_text')==true){ ?>
							jQuery("#backtoblog").html('<?php echo addslashes(get_option('agca_login_banner_text')); ?>');
					<?php } ?>
					<?php if(get_option('agca_login_photo_url')==true){ ?>								
							advanced_url = "<?php echo get_option('agca_login_photo_url'); ?>";
							var $url = "url(" + advanced_url + ")";
							jQuery("#login h1 a").css("background",$url+' no-repeat');	
							jQuery("#login h1 a").hide();
							image = jQuery("<img />").attr("src",advanced_url);	
							jQuery(image).load(function() {
								var originalWidth = 326;
								var widthDiff = this.width - originalWidth; 
								jQuery("#login h1 a").height(this.height);
								jQuery("#login h1 a").width(this.width);
								jQuery("#login h1 a").css("background-size",this.width+"px "+this.height+"px");								
								jQuery("#login h1 a").css('margin-left',-(widthDiff/2)+"px");
								jQuery("#login h1 a").show();
							});												
					<?php } ?>
					<?php if(get_option('agca_login_photo_href')==true){ ?>						
							var $href = "<?php echo get_option('agca_login_photo_href'); ?>";                                                        
                                                        $href = $href.replace("%BLOG%", "<?php echo get_bloginfo('wpurl'); ?>");                                                            
                                                        
							jQuery("#login h1 a").attr("href",$href);							
					<?php } ?>
					<?php if(get_option('agca_login_photo_remove')==true){ ?>
							jQuery("#login h1 a").css("display","none");
					<?php } ?>	
									
						jQuery("#login h1 a").attr("title","");	
						
				    <?php if(get_option('agca_login_register_remove')==true){ ?>
							if(jQuery('p#nav').size() > 0){
								jQuery('p#nav').html(jQuery('p#nav').html().replace('|',''));							
							}							
							jQuery('p#nav a').each(function(){
								if(jQuery(this).attr('href').indexOf('register') != -1){
									jQuery(this).remove();
								}
							});							
							
					<?php } ?>						
					<?php if(get_option('agca_login_register_href')!=""){ ?>							
							jQuery('p#nav a').each(function(){
								if(jQuery(this).attr('href').indexOf('register') != -1){
									jQuery(this).attr('href','<?php echo get_option('agca_login_register_href'); ?>');
								}
							});							
							
					<?php } ?>	
					
					<?php if(get_option('agca_login_lostpassword_remove')==true){ ?>
							if(jQuery('p#nav').size() > 0){
								jQuery('p#nav').html(jQuery('p#nav').html().replace('|',''));						
							}							
							jQuery('p#nav a').each(function(){
								if(jQuery(this).attr('href').indexOf('lostpassword') != -1){
									jQuery(this).remove();
								}
							});							
							
					<?php } ?>	

						
					<?php //COLORIZER ?>
					<?php if(get_option('agca_colorizer_turnonoff') == 'on'){ ?>
						jQuery('label,h1,h2,h3,h4,h5,h6,a,p,.form-table th,.form-wrap label').css('text-shadow','none');					
						jQuery("body.login, html").css("background","<?php echo $this->colorizer['login_color_background'];?>");	
						
							
					<?php								
											
														
					 } ?>
					<?php //COLORIZER END ?>			
			 }catch(err){				
				console.log("AGCA - LOGIN ERROR: " + err.name + " / " + err.message);							
			 }finally{		
				jQuery('html').show();
				jQuery('html').css('visibility','visible');							
			 }
            });
        /* ]]> */
		 
        </script>
	<?php 	
	}
	
	function agca_admin_page() {
	
		$wpversion = $this->get_wp_version();
		?>		
		<?php //includes ?>
			<link rel="stylesheet" type="text/css" href="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>style/farbtastic.css?ver=<?php echo $wpversion; ?>" />
			<script type="text/javascript" src="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>script/farbtastic.js?ver=<?php echo $wpversion; ?>"></script>	
			
			<link rel="stylesheet" type="text/css" href="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>style/agca_farbtastic.css?ver=<?php echo $wpversion; ?>" />
			<script type="text/javascript" src="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>script/agca_farbtastic.js?ver=<?php echo $wpversion; ?>"></script>
			<script type="text/javascript" src="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>script/xd.js?ver=<?php echo $wpversion; ?>"></script>			
			<script type="text/javascript">
             var templates_ep = "<?php echo $this->templates_ep; ?>"; 
			 var template_selected = '<?php echo get_option('agca_selected_template'); ?>';			 
            </script>
			<script type="text/javascript" src="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>script/agca_tmpl.js?ver=<?php echo $wpversion; ?>"></script>                  						
		<?php //includes ?>		
		<div class="wrap">
			<h1 style="color:green">AG Custom Admin Settings <span style="font-size:15px;">(v<?php echo $this->agca_version; ?>)</span></h1>						
										<div id="agca_news">&nbsp;</div><br />								
			<form method="post" id="agca_form" action="options.php">
				<?php settings_fields( 'agca-options-group' ); ?>
			<table>
				<tr valign="left" >
								<th scope="row">
                                                                    <label title="If checked, all users will be affected with these changes, except admin. Not checked = apply for all</br></br><strong>Q</strong>: Who is administrator?</br><strong>A</strong>: Go to <i>Advanced</i> tab and change capability option to define admin users." for="agca_role_allbutadmin">Do not apply customizations for Administrator&nbsp;&nbsp;</label>
								</th>
								<td><input class="agca-checkbox" title="If checked, all users will be affected with these changes, except admin. Not checked = apply for all" type="checkbox" name="agca_role_allbutadmin" value="true" <?php if (get_option('agca_role_allbutadmin')==true) echo 'checked="checked" '; echo get_option('agca_role_allbutadmin'); ?> />								
								</td>
				</tr>                                
			</table>                        
                        <div style="float:right;width:152px;margin-left: 100px;margin-top: -25px;"><strong><span style="font-size:12px" >Your feedback:</span></strong> <a class="feedback positive" target="_blank" title="POSITIVE FEEDBACK: I like this plugin!" href="http://agca.argonius.com/ag-custom-admin/feedback/ag-custom-admin-positive-feedback?comments=hidden" style="padding:5px;"><img  style="" width="15" src="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>images/thumbup.png" /></a>  <a class="feedback" target="_blank" title="NEGATIVE FEEDBACK: I don't like this plugin." style="padding:5px;" href="http://agca.argonius.com/ag-custom-admin/feedback/ag-custom-admin-negative-feedback?comments=hidden"><img width="15" src="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>images/thumbdown.png" /></a></div>
                                    
			<br />
			<ul id="ag_main_menu">
				<li class="selected"><a href="#admin-bar-settings" title="Settings for admin bar" >Admin Bar</a></li>
				<li class="normal"><a href="#admin-footer-settings" title="Settings for admin footer" >Admin Footer</a></li>
				<li class="normal"><a href="#dashboad-page-settings" title="Settings for Dashboard page">Dashboard Page</a></li>
				<li class="normal"><a href="#login-page-settings" title="Settings for Login page">Login Page</a></li>
				<li class="normal" ><a href="#admin-menu-settings" title="Settings for main admin menu">Admin Menu</a></li>
				<li class="normal"><a href="#ag-colorizer-setttings" title="AG colorizer settings">Colorizer</a></li>				
                <li class="normal"><a href="#ag-advanced" title="My custom scripts">Advanced</a></li>
				<li class="normal" style=""><a style="color:#DB6014;font-weight:bolder;" href="#ag-templates" title="AG Custom Admin Themes">Admin Themes</a></li>
								
				<li style="background:none;border:none;padding:0;"><a id="agca_donate_button" target="_blank" style="margin-left:8px" title="Like this plugin? You can support its future development by giving a donation by your wish " href="http://agca.argonius.com/ag-custom-admin/support-for-future-development"><img alt="Donate" src="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ?>images/btn_donate_LG.gif" /></a>
				</li>                                
				<li style="background:none;border:none;padding:0;padding-left:10px;margin-top:-7px"></li>		
			</ul>
                        <div id="agca_advertising">
                            <ul>    
                                <li style="min-height:105px;display: block"></li>
                            </ul>
                        </div>
                        <div style="clear:both"></div>
				<div id="section_admin_bar" class="ag_section">
				<h2 class="section_title" tabindex="-1">Admin Bar Settings Page</h2>
				<br />
					<p tabindex="0"><i><strong>Info: </strong>Move your mouse over option labels for more information about an option</i></p>							
				<br />
				<table class="form-table" width="500px">							
							<tr valign="center" class="ag_table_major_options" >
								<td>
									<label tabindex="0" title="Hide admin bar with all elements on the top of the admin page" for="agca_header"><strong>Hide admin bar completely</strong></label>
								</td>
								<td>					
                                                                    <input id="agca_header" class="agca-checkbox" type="checkbox" onchange="if(jQuery('#agca_header').is(':checked')){jQuery('#agca_header_show_logout_content').show('slide');}else{jQuery('#agca_header_show_logout_content').hide('slide');};" title="Hide admin bar with all elements on the top of the admin page" name="agca_header" value="true" <?php if (get_option('agca_header')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>
                                                        
                                                        <?php 
                                                        $agca_header_show_logout_style= "style='display:none'";
                                                        if (get_option('agca_header')==true){
                                                                $agca_header_show_logout_style="";
                                                        }
                                                        ?>
							<tr valign="center" class="ag_table_major_options" id="agca_header_show_logout_content" <?php echo $agca_header_show_logout_style;  ?> >
								<td>
									<label tabindex="0" title='Check this if you want to show Log Out button in top right corner of admin page' for="agca_header_show_logout"><strong>(but show Log Out button)</strong></label>
								</td>
								<td>					
									<input type="checkbox" class="agca-checkbox" title='Check this if you want to show Log Out button in top right corner of admin page' name="agca_header_show_logout" value="true" <?php if ((get_option('agca_header')==true) && (get_option('agca_header_show_logout')==true)) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
                                                        <tr valign="center" >
								<td>
									<label tabindex="0" title="Removes admin bar customizations (AGCA scripts) on front end." for="agca_admin_bar_frontend">Remove admin bar customizations on site pages</label>
								</td>
								<td>					
                                                                    <input style="margin-left:-5px" class="agca-checkbox" id="agca_admin_bar_frontend" type="checkbox" title="Removes admin bar customizations (AGCA scripts) on front end." name="agca_admin_bar_frontend" value="true" <?php if (get_option('agca_admin_bar_frontend')==true) echo 'checked="checked" '; ?> />
																	
								</td>
							</tr>
							<tr valign="center" >
								<td>
									<label tabindex="0" title="Removes admin bar on front end." for="agca_admin_bar_frontend_hide">Remove admin bar on site pages</label>
								</td>
								<td>					
                                                                    <input style="margin-left:-5px" class="agca-checkbox" id="agca_admin_bar_frontend_hide" type="checkbox" title="Removes admin bar on front end." name="agca_admin_bar_frontend_hide" value="true" <?php if (get_option('agca_admin_bar_frontend_hide')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>
							<tr valign="center">								
								<td colspan="2">
									<div class="ag_table_heading"><h3 tabindex="0">On the Left</h3></div>
								</td>
								<td></td>
							</tr>
							<?php if($wpversion<3.3){?>
							<tr valign="center">
								<th >
									<label title="This is link next to heading in admin bar" for="agca_privacy_options">Hide Privacy link</label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" title="This is link next to heading in admin bar" name="agca_privacy_options" value="true" <?php if (get_option('agca_privacy_options')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>
							<?php } ?>							
							<tr valign="center">
								<th >
									<label title="Change default WordPress logo with custom image." for="agca_wp_logo_custom">Change admin bar logo</label>
								</th>
								<td>
									<input id="agca_wp_logo_custom" title="If this field is not empty, image from provided url will be visible in top bar" type="text" size="47" name="agca_wp_logo_custom" value="<?php echo get_option('agca_wp_logo_custom'); ?>" /><input type="button" class="agca_button" onClick="jQuery('#agca_wp_logo_custom').val('');" value="Clear" />
									&nbsp;<p><i>Put here an URL of the new top bar image ( maximum height = 28px)</i>.</p>
								</td>
							</tr> 
                                                        <tr valign="center">
								<th>
                                                                    <label title="Change admin bar logo link.</br></br>Use:</br><strong>%BLOG%</strong> - for blog URL</br><strong>%SWITCH%</strong> - to switch betweent admin and site area" for="agca_wp_logo_custom">Change admin bar logo link</label>
								</th>
								<td>
									<input id="agca_wp_logo_custom_link" type="text" size="47" name="agca_wp_logo_custom_link" value="<?php echo get_option('agca_wp_logo_custom_link'); ?>" /><input type="button" class="agca_button"  onClick="jQuery('#agca_wp_logo_custom_link').val('');" value="Clear" />
									&nbsp;<p><i>Put here a link for admin bar logo </i>.</p>
								</td>
							</tr> 
							<tr valign="center">
								<th >
									<label title="Customize WordPress title using custom title template.</br></br>Examples:</br><strong>%BLOG% -- %PAGE%</strong>  (will be) <i>My Blog -- Add New Post</i></br><strong>%BLOG%</strong> (will be) <i>My Blog</i></br><strong>My Company > %BLOG% > %PAGE%</strong> (will be) <i>My Company > My Blog > Tools</i>" for="agca_custom_title">Custom admin title template</label>
								</th>
								<td>
									<input title="" type="text" size="47" id="agca_custom_title" name="agca_custom_title" value="<?php echo get_option('agca_custom_title'); ?>" /><input type="button" class="agca_button"  onClick="jQuery('#agca_custom_title').val('');" value="Clear" />																
									&nbsp;<p><i>Please use <strong>%BLOG%</strong> and <strong>%PAGE%</strong> in your title template.</i></p>
								</td>
							</tr> 
							<tr valign="center">
								<th >
									<label title="Add custom image on the top of the admin content." for="agca_header_logo_custom">Custom header image</label>
								</th>
								<td>
									<input title="If this field is not empty, image from provided URL will be visible in header" type="text" size="47" id="agca_header_logo_custom" name="agca_header_logo_custom" value="<?php echo get_option('agca_header_logo_custom'); ?>" /><input type="button" class="agca_button"  onClick="jQuery('#agca_header_logo_custom').val('');" value="Clear" />																
									&nbsp;<p><i>Add custom header image</i>.</p>
								</td>
							</tr> 
							<tr valign="center">
								<th >
									<label title="Small Wordpress logo in admin top bar" for="agca_header_logo">Hide WordPress logo</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Small Wordpress logo in admin top bar" type="checkbox" name="agca_header_logo" value="true" <?php if (get_option('agca_header_logo')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<?php if($wpversion>=3.3){?>
							<tr valign="center">
								<th >
									<label title="Hides site name section in admin bar" for="agca_remove_site_link">Hide site name in admin bar</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Hides site name section in admin bar" type="checkbox" name="agca_remove_site_link" value="true" <?php if (get_option('agca_remove_site_link')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 							
							<tr valign="center">
								<th >
									<label title="Hides default WordPress top bar dropdown menus on WordPress logo and Heading" for="agca_remove_top_bar_dropdowns">Hide WordPress top bar dropdown menus</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Hides default WordPress top bar dropdown menus on WordPress logo and Heading" type="checkbox" name="agca_remove_top_bar_dropdowns" value="true" <?php if (get_option('agca_remove_top_bar_dropdowns')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<tr valign="center">
								<th >
									<label title="Removes comments block from admin bar" for="agca_admin_bar_comments">Hide admin bar "Comments"</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Removes comments block from admin bar" type="checkbox" name="agca_admin_bar_comments" value="true" <?php if (get_option('agca_admin_bar_comments')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<tr valign="center" style="margin-top:20px;">
								<th >
									<label title="Removes 'New' block with its contents from admin bar" for="agca_admin_bar_new_content">Hide admin bar "New" content completely</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Removes 'New' block with its contents from admin bar" type="checkbox" name="agca_admin_bar_new_content" value="true" <?php if (get_option('agca_admin_bar_new_content')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 	
							<tr class="new_content_header_submenu" valign="center">
								<th >
									<label title="Removes 'Post' submenu from 'New' option from admin bar" for="agca_admin_bar_new_content_post">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hide "New" content -> Post submenu</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Removes 'Post' submenu from 'New' option from admin bar" type="checkbox" name="agca_admin_bar_new_content_post" value="true" <?php if (get_option('agca_admin_bar_new_content_post')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<tr  class="new_content_header_submenu" valign="center">
								<th >
									<label title="Removes 'Link' submenu from 'New' option from admin bar" for="agca_admin_bar_new_content_link">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hide "New" content -> Link submenu</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Removes 'Link' submenu from 'New' option from admin bar" type="checkbox" name="agca_admin_bar_new_content_link" value="true" <?php if (get_option('agca_admin_bar_new_content_link')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<tr class="new_content_header_submenu" valign="center">
								<th >
									<label title="Removes 'Page' submenu from 'New' option from admin bar" for="agca_admin_bar_new_content_page">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hide "New" content -> Page submenu</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Removes 'Page' submenu from 'New' option from admin bar" type="checkbox" name="agca_admin_bar_new_content_page" value="true" <?php if (get_option('agca_admin_bar_new_content_page')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<tr class="new_content_header_submenu" valign="center">
								<th >
									<label title="Removes 'User' submenu from 'New' option from admin bar" for="agca_admin_bar_new_content_user">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hide "New" content -> User submenu</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Removes 'User' submenu from 'New' option from admin bar" type="checkbox" name="agca_admin_bar_new_content_user" value="true" <?php if (get_option('agca_admin_bar_new_content_user')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<tr class="new_content_header_submenu" valign="center">
								<th >
									<label title="Removes 'Media' submenu from 'New' option from admin bar" for="agca_admin_bar_new_content_media">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Hide "New" content -> Media submenu</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Removes 'Media' submenu from 'New' option from admin bar" type="checkbox" name="agca_admin_bar_new_content_media" value="true" <?php if (get_option('agca_admin_bar_new_content_media')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>
							<tr valign="center">
								<th >
									<label title="Removes update notifications from admin bar" for="agca_admin_bar_update_notifications">Hide admin bar update notifications</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Removes update notifications from admin bar" type="checkbox" name="agca_admin_bar_update_notifications" value="true" <?php if (get_option('agca_admin_bar_update_notifications')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 							
							<?php } ?>
							
							<tr valign="center">
								<th scope="row">
									<label title="Adds custom text in admin top bar. Default Wordpress heading stays intact." for="agca_custom_site_heading">Custom blog heading</label>
								</th>
								<td>
								<textarea title="Adds custom text in admin top bar." rows="5" name="agca_custom_site_heading" cols="40"><?php echo htmlspecialchars(get_option('agca_custom_site_heading')); ?></textarea><p><em><strong>Info: </strong>You can use HTML tags like 'h1' and/or 'a' tag</em></p>
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Hides yellow bar with notifications for new Wordpress release" for="agca_update_bar">Hide WordPress update notification bar</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="Hides yellow bar with notifications for new Wordpress release" type="checkbox" name="agca_update_bar" value="true" <?php if (get_option('agca_update_bar')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							
							<?php if($wpversion<3.3){ ?>
							<tr valign="center">
								<th scope="row">
									<label for="agca_site_heading">Hide default blog heading</label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" name="agca_site_heading" value="true" <?php if (get_option('agca_site_heading')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>
							<?php } ?>
							
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">On the Right</h3></div>
								</td>
								<td>									
								</td>
							</tr>
							<tr valign="center">
								<th scope="row">
									<label for="agca_screen_options_menu-options">Hide Screen Options menu</label>
								</th>
								<td>						
									<input class="agca-checkbox" type="checkbox" name="agca_screen_options_menu" value="true" <?php if (get_option('agca_screen_options_menu')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>
							<tr valign="center">
								<th scope="row">
									<label for="agca_help_menu">Hide Help menu</label>
								</th>
								<td>						
									<input class="agca-checkbox" type="checkbox" name="agca_help_menu" value="true" <?php if (get_option('agca_help_menu')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>
							<tr valign="center">
								<th scope="row">
									<label for="agca_options_menu">Hide Favorite Actions</label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" name="agca_options_menu" value="true" <?php if (get_option('agca_options_menu')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 	
							<tr valign="center">
								<th scope="row">
									<label for="agca_howdy">Change Howdy text</label>
								</th>
								<td><input type="text" size="47" name="agca_howdy" value="<?php echo get_option('agca_howdy'); ?>" /></td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Put 'Exit', for example" for="agca_logout">Change Log out text</label>
								</th>
								<td><input title="Put 'Exit', for example" type="text" size="47" name="agca_logout" value="<?php echo get_option('agca_logout'); ?>" /></td>
							</tr> 	
							<?php if($wpversion >= 3.2){ ?>
								<?php 
									$profile_text = 'Remove "Your profile" option from dropdown menu';
									if($wpversion >= 3.3){
										$profile_text = 'Remove "Edit My Profile" option from dropdown menu';
									}
								?>
							<tr valign="center">
								<th scope="row">
									<label for="agca_remove_your_profile"><?php echo $profile_text; ?></label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" name="agca_remove_your_profile" value="true" <?php if (get_option('agca_remove_your_profile')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<?php } ?>
							<tr valign="center">
								<th scope="row">
									<label title="If selected, hides all elements in top right corner, except Log Out button" for="agca_logout_only">Log out only</label>
								</th>
								<td>
									<input class="agca-checkbox" title="If selected, hides all elements in top right corner, except Log Out button" type="checkbox" name="agca_logout_only" value="true" <?php if (get_option('agca_logout_only')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>							
							</table>
						</div>
						
						<div id="section_admin_footer" style="display:none" class="ag_section">	
							<h2 class="section_title" tabindex="-1">Admin Footer Settings Page</h2>
							<br /><br />						
							<table class="form-table" width="500px">		
							<tr valign="center" class="ag_table_major_options">
								<td>
									<label title="Hides footer with all elements" for="agca_footer"><strong>Hide footer completely</strong></label>
								</td>
								<td>					
									<input class="agca-checkbox" title="Hides footer with all elements" type="checkbox" id="agca_footer" name="agca_footer" value="true" <?php if (get_option('agca_footer')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">Footer Options</h3></div>
								</td>
								<td>									
								</td>
							</tr>
							<tr valign="center">
								<th scope="row">
									<label title="Hides default text in footer" for="agca_footer_left_hide">Hide footer text</label>
								</th>
								<td><input class="agca-checkbox" title="Hides default text in footer" type="checkbox" name="agca_footer_left_hide" value="true" <?php if (get_option('agca_footer_left_hide')==true) echo 'checked="checked" '; ?> />								
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Replaces text 'Thank you for creating with WordPress' with custom text" for="agca_footer_left">Change footer text</label>
								</th>
								<td>
									<textarea title="Replaces text 'Thank you for creating with WordPress' with custom text" rows="5" name="agca_footer_left" cols="40"><?php echo htmlspecialchars(get_option('agca_footer_left')); ?></textarea>
								</td>						
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Hides text 'Get Version ...' on right" for="agca_footer_right_hide">Hide version text</label>
								</th>
								<td><input class="agca-checkbox" title="Hides text 'Get Version ...' on right" type="checkbox" name="agca_footer_right_hide" value="true" <?php if (get_option('agca_footer_right_hide')==true) echo 'checked="checked" '; ?> />								
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Replaces text 'Get Version ...' with custom text" for="agca_footer_right">Change version text</label>
								</th>
								<td>
									<textarea title="Replaces text 'Get Version ...' with custom text" rows="5" name="agca_footer_right" cols="40"><?php echo htmlspecialchars(get_option('agca_footer_right')); ?></textarea>
								</td>
							</tr> 	
							</table>
						</div>
						
						<div id="section_dashboard_page" style="display:none" class="ag_section">	
							<h2 class="section_title"  tabindex="-1">Dashboard Page Settings</h2>
							<table class="form-table" width="500px">	
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">Dashboard Page Options</h3></div>
								</td>
								<td></td>
							</tr>
							<!--<tr valign="center">
								<th scope="row">
									<label title="This is small 'house' icon next to main heading (Dashboard text by default) on Dashboard page" for="agca_dashboard_icon">Hide Dashboard heading icon</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="This is small house icon next to main heading on Dashboard page. Dashboard text is shown by default" type="checkbox" name="agca_dashboard_icon" value="true" <?php if (get_option('agca_dashboard_icon')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>-->
							
							<tr valign="center">
								<th scope="row">
									<label title="Main heading ('Dashboard') on Dashboard page" for="agca_dashboard_text">Change Dashboard heading text</label>
								</th>
								<td><input title="Main heading with text 'Dashboard' on Dashboard page" type="text" size="47" name="agca_dashboard_text" value="<?php echo get_option('agca_dashboard_text'); ?>" /></td>
							</tr>
							<tr valign="center">
								<th scope="row">
									<label title="Adds custom text (or HTML) between heading and widgets area on Dashboard page" for="agca_dashboard_text_paragraph">Add custom Dashboard content<br> <em>(text or HTML content)</em></label>
								</th>
								<td class="agca_editor">								
								<?php $this->getTextEditor('agca_dashboard_text_paragraph'); ?>			
								</td>
							</tr>							
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">Dashboard widgets Options</h3></div>
								</td>
								<td></td>
							</tr>
							<tr><td>
							<p tabindex="0"><i><strong>Info:</strong> These settings override settings in Screen options on Dashboard page.</i></p>							
							</td>
							</tr>
							<tr valign="center">
								<th scope="row">
									<label for="agca_dashboard_widget_welcome">Hide "Welcome" WordPress Message</label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" name="agca_dashboard_widget_welcome" value="true" <?php if (get_option('agca_dashboard_widget_welcome')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>	
                            <tr valign="center">
								<th scope="row">
									<label for="agca_dashboard_widget_activity">Hide "Activity" Dashboard Widget</label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" name="agca_dashboard_widget_activity" value="true" <?php if (get_option('agca_dashboard_widget_activity')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>								
							<!--<tr valign="center">
								<th scope="row">
									<label for="agca_dashboard_widget_il">Hide "Incoming Links"</label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" name="agca_dashboard_widget_il" value="true" <?php if (get_option('agca_dashboard_widget_il')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>-->
								<!--<tr valign="center">
								<th scope="row">
									<label for="agca_dashboard_widget_plugins">Hide "Plugins"</label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" name="agca_dashboard_widget_plugins" value="true" <?php if (get_option('agca_dashboard_widget_plugins')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>	-->										
							<tr valign="center">
								<th scope="row">
									<label for="agca_dashboard_widget_qp">Hide "Quick Draft"</label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" name="agca_dashboard_widget_qp" value="true" <?php if (get_option('agca_dashboard_widget_qp')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>	
							<tr valign="center">
								<th scope="row">
									<label for="agca_dashboard_widget_rn">Hide "At a Glance"</label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" name="agca_dashboard_widget_rn" value="true" <?php if (get_option('agca_dashboard_widget_rn')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>	
							<!--<tr valign="center">
								<th scope="row">
									<label for="agca_dashboard_widget_rd">Hide "Recent Drafts"</label>
								</th>
								<td>					
									<input class="agca-checkbox" type="checkbox" name="agca_dashboard_widget_rd" value="true" <?php if (get_option('agca_dashboard_widget_rd')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>	-->
							<tr valign="center">
								<th scope="row">
									<label title="This is 'WordPress Development Blog' widget by default" for="agca_dashboard_widget_primary">Hide primary widget area</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="This is 'WordPress Development Blog' widget by default" type="checkbox" name="agca_dashboard_widget_primary" value="true" <?php if (get_option('agca_dashboard_widget_primary')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>	
							<tr valign="center">
								<th scope="row">
									<label title="This is 'Other WordPress News' widget by default"  for="agca_dashboard_widget_secondary">Hide secondary widget area</label>
								</th>
								<td>					
									<input class="agca-checkbox" title="This is 'Other WordPress News' widget by default" type="checkbox" name="agca_dashboard_widget_secondary" value="true" <?php if (get_option('agca_dashboard_widget_secondary')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>	
							</table>
						</div>
						<div id="section_login_page" style="display:none" class="ag_section">
						<h2 class="section_title" tabindex="-1">Login Page Settings</h2>												
							<table class="form-table" width="500px">				
													
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">Login Page Options</h3></div>
								</td>								
							</tr>
                                                        <tr valign="center">
									<td>
										<label for="agca_login_banner">Hide back to blog text</label>
									</td>
									<td>					
										<input class="agca-checkbox" type="checkbox" name="agca_login_banner" title="Hide back to blog block" value="true" <?php if (get_option('agca_login_banner')==true) echo 'checked="checked" '; ?> />
									</td>
							</tr>
							<tr valign="center">
								<th scope="row">
									<label title="Changes '<- Back to ...' text in top bar on Login page" for="agca_login_banner_text">Change back to blog text</label>
								</th>
								<td>
									<textarea title="Changes 'Back to ...' text in top bar on Login page" rows="5" name="agca_login_banner_text" cols="40"><?php echo htmlspecialchars(get_option('agca_login_banner_text')); ?></textarea>&nbsp;<p><i>You should surround it with anchor tag &lt;a&gt;&lt;/a&gt;.</i></p>
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="If this field is not empty, image from provided url will be visible on Login page" for="agca_login_photo_url">Change Login header image</label>
								</th>
								<td>
									<input title="If this field is not empty, image from provided url will be visible on Login page" type="text" size="47" id="agca_login_photo_url" name="agca_login_photo_url" value="<?php echo get_option('agca_login_photo_url'); ?>" /><input type="button" class="agca_button"  onClick="jQuery('#agca_login_photo_url').val('');" value="Clear" />																
									&nbsp;<p><i>Put here link of new login image. Image can be of any size and type</i>.</p>
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Put here custom link to a web location, that will be triggered on image click" for="agca_login_photo_href">Change hyperlink on Login image</label>
								</th>
								<td>
									<input title="Put here custom link to a web location, that will be triggered on image click" type="text" size="47" id="agca_login_photo_href"  name="agca_login_photo_href" value="<?php echo get_option('agca_login_photo_href'); ?>" /><input type="button"  class="agca_button"  onClick="jQuery('#agca_login_photo_href').val('');" value="Clear" />
                                                                        &nbsp;<p><i>For blog URL use %BLOG%</i></p>
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Remove login image completely" for="agca_login_photo_remove">Hide Login header image</label>
								</th>
								<td>
									<input class="agca-checkbox" title="Remove login image completely" type="checkbox" name="agca_login_photo_remove" value="true" <?php if (get_option('agca_login_photo_remove')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
                            <tr valign="center">
								<th scope="row">
									<label title="Rounds box on login page" for="agca_login_round_box">Round box corners</label>
								</th>
								<td>
									<input class="agca-checkbox" title="Rounds box on login page" type="checkbox" name="agca_login_round_box" value="true" <?php if (get_option('agca_login_round_box')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
                                                         <?php 
                                                         $roundboxzizestyle = "style='display:none'";
                                                         if (get_option('agca_login_round_box')=='true') $roundboxzizestyle = '';
                                                         ?>
                            <tr valign="center" id="agca_login_round_box_size_block" <?php echo $roundboxzizestyle; ?> >
								<th scope="row">
									<label title="Size of rounded box curve" for="agca_login_round_box_size">Round box corners - size</label>
								</th>
								<td>
									<input class="validateNumber" limit="3" title="Size of rounded box curve" type="text" name="agca_login_round_box_size"  type="text" size="3" value="<?php echo get_option('agca_login_round_box_size'); ?>" />&nbsp;(px)	
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Remove register link on login page" for="agca_login_register_remove">Remove register link</label>
								</th>
								<td>
									<input class="agca-checkbox" title="Remove register link on login page" type="checkbox" name="agca_login_register_remove" value="true" <?php if (get_option('agca_login_register_remove')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>
														<?php 
                                                         $agca_login_register_href_visibility = "style='display:none'";
                                                         if (get_option('agca_login_register_remove')!='true') $agca_login_register_href_visibility = '';
                                                         ?>
							<tr valign="center" id="agca_login_register_href_block" <?php echo $agca_login_register_href_visibility; ?> >
								<th scope="row">
									<label title="Change register link on login page to point to your custom registration page." for="agca_login_register_href">Change register hyperlink</label>
								</th>
								<td>
									<input title="Change register link on login page to point to your custom registration page." type="text" size="47" id="agca_login_register_href"  name="agca_login_register_href" value="<?php echo get_option('agca_login_register_href'); ?>" /><input type="button" class="agca_button"  onClick="jQuery('#agca_login_register_href').val('');" value="Clear" />                                                                        
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Removes lost password link on login page" for="agca_login_lostpassword_remove">Remove lost password link</label>
								</th>
								<td>
									<input class="agca-checkbox" class="agca-checkbox" title="Removes lost password link on login page" type="checkbox" name="agca_login_lostpassword_remove" value="true" <?php if (get_option('agca_login_lostpassword_remove')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr>
						</table>
						</div>
						<?php
							/*ADMIN MENU*/
						?>
						<div id="section_admin_menu" style="display:none" class="ag_section">
						<h2 class="section_title" tabindex="-1">Admin Menu Settings Page</h2>
						<br />
						<p style="font-style:italic" tabindex="0"><strong>Important: </strong>Please Turn off menu configuration before activating or disabling other plugins (or making any other changes to main menu). Use <strong>Reset Settings</strong> button to restore default values if anything goes wrong.</p>					
						<p style="font-style:italic" tabindex="0"><strong></strong>If you found that admin menu items are misaligned or not correct, press <strong>Reset Settings</strong> button. This happens if admin menu is changed by other plugins, or after activating / deactivating other plugings. Avoid such changes after you apply admin menu customizations.</p>
						<br />
							<table class="form-table" width="500px">	
							<tr valign="center" class="ag_table_major_options">
								<td><label for="agca_admin_menu_turnonoff"><strong>Turn on/off admin menu configuration</strong></label></td>
								<td><strong><input class="agca-radio" type="radio" name="agca_admin_menu_turnonoff" title="Turn ON admin menu configuration" value="on" <?php if(get_option('agca_admin_menu_turnonoff') == 'on') echo 'checked="checked" '; ?> /><span class="agca-radio-text" style="color:green">ON</span>&nbsp;&nbsp;&nbsp;&nbsp;<input class="agca-radio" type="radio" name="agca_admin_menu_turnonoff" title="Turn OFF admin menu configuration" value="off" <?php if(get_option('agca_admin_menu_turnonoff') != 'on') echo 'checked="checked"'; ?> /><span class="agca-radio-text" style="color:red">OFF</span></strong></td>
							</tr>
							<tr valign="center" class="ag_table_major_options">
								<td><label for="agca_admin_menu_agca_button_only"><strong>Hide admin menu completly</br>(administrator can see AG custom admin button)</strong></label></td>
								<td><input class="agca-checkbox" type="checkbox" name="agca_admin_menu_agca_button_only" title="Hide admin menu completly (administrator can see 'AG custom admin' button)" value="true" <?php if (get_option('agca_admin_menu_agca_button_only')==true) echo 'checked="checked" '; ?> /></td>
							</tr>
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">Edit / Remove Menu Items</h3></div>
								</td>
								<td>									
								</td>
							</tr>
							<tr>
								<td colspan="2">
								Reset to default values
											<input type="button" class="agca_button"  id="ag_edit_adminmenu_reset_button" title="Reset menu settings to default values" name="ag_edit_adminmenu_reset_button" value="Reset Settings" /><br />
											<p tabindex="0"><em>(click on the top menu items to show / hide their sub-menus)</em></p>
									<table id="ag_edit_adminmenu">									
										<tr style="background-color:#999;">
											<td width="300px"><div style="float:left;color:#fff;"><h3>Item</h3></div><div style="float:right;color:#fff;"><h3>Remove?</h3></div></td><td width="300px" style="color:#fff;" ><h3>Change Text</h3>													
											</td>
										</tr>
									</table>
									<input type="hidden" size="47" id="ag_edit_adminmenu_json" name="ag_edit_adminmenu_json" value="<?php echo htmlspecialchars(get_option('ag_edit_adminmenu_json')); ?>" />
								</td>
								<td></td>
							</tr>
							<tr valign="center">
								<th scope="row">
									<label title="This is blank space between Dashboard and Posts button (by default)" for="agca_admin_menu_separator_first">Remove first menu items separator</label>
								</th>
								<td>
									<input class="agca-checkbox" title="This is blank space separator between Dashboard and Posts button (by default)" type="checkbox" name="agca_admin_menu_separator_first" value="true" <?php if (get_option('agca_admin_menu_separator_first')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="This is blank space  separator between Comments and Appearance button (by default)" for="agca_admin_menu_separator_second">Remove second menu items separator</label>
								</th>
								<td>
									<input class="agca-checkbox" title="This is blank space  separator between Comments and Appearance button (by default)" type="checkbox" name="agca_admin_menu_separator_second" value="true" <?php if (get_option('agca_admin_menu_separator_second')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Removes small icons on admin menu buttons" for="agca_admin_menu_icons">Remove menu icons</label>
								</th>
								<td>
									<input class="agca-checkbox" title="Removes small icons on admin menu buttons" type="checkbox" name="agca_admin_menu_icons" value="true" <?php if (get_option('agca_admin_menu_icons')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
                                                        <tr valign="center">
								<th scope="row">
									<label title="Removes small arrow that appears on button hover" for="agca_admin_menu_arrow">Remove sub-menu arrow</label>
								</th>
								<td>
									<input class="agca-checkbox" title="Removes small arrow that appears on button hover" type="checkbox" name="agca_admin_menu_arrow" value="true" <?php if (get_option('agca_admin_menu_arrow')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Removes collapse button at the end of admin menu" for="agca_admin_menu_collapse_button">Remove "Collapse menu" button</label>
								</th>
								<td>
									<input class="agca-checkbox" title="Removes collapse button at the end of admin menu" type="checkbox" name="agca_admin_menu_collapse_button" value="true" <?php if (get_option('agca_admin_menu_collapse_button')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
                                                        <tr valign="center">
								<th scope="row">
									<label title="Rounds submenu pop-up box" for="agca_admin_menu_submenu_round">Round sub-menu pop-up box</label>
								</th>
								<td>
									<input class="agca-checkbox" title="Rounds submenu pop-up box" type="checkbox" name="agca_admin_menu_submenu_round" value="true" <?php if (get_option('agca_admin_menu_submenu_round')==true) echo 'checked="checked" '; ?> />
								</td>
							</tr> 
                                                         <?php 
                                                         $roundsubmenuzizestyle = "style='display:none'";
                                                         if (get_option('agca_admin_menu_submenu_round')=='true') $roundsubmenuzizestyle = '';
                                                         ?>
                                                        <tr valign="center" id="agca_admin_menu_submenu_round_block" <?php echo $roundsubmenuzizestyle; ?> >
								<th scope="row">
									<label title="Size of rounded box curve" for="agca_admin_menu_submenu_round_size">Round sub-menu pop-up box - size</label>
								</th>
								<td>
									<input class="validateNumber" limit="3" title="Size of rounded box curve" type="text" name="agca_admin_menu_submenu_round_size"  type="text" size="3" value="<?php echo get_option('agca_admin_menu_submenu_round_size'); ?>" />&nbsp;(px)	
								</td>
							</tr> 
                            <tr valign="center">
								<th scope="row">
									<label title="Adds custom logo above the admin menu" for="agca_admin_menu_brand">Add custom branding logo above the admin menu</label>
								</th>
								<td>
									<input id="agca_admin_menu_brand" title="Adds custom logo above the admin menu" type="text" size="47" name="agca_admin_menu_brand" value="<?php echo get_option('agca_admin_menu_brand'); ?>" /><input type="button" class="agca_button" onClick="jQuery('#agca_admin_menu_brand').val('');" value="Clear" />																
									&nbsp;<p><i>Put here your custom image URL. Image can be of any size and type</i>.</p>
								</td>
							</tr> 
                            <tr valign="center">
								<th>
									<label title="Change branding logo link</br></br>Use:</br><strong>%BLOG%</strong> - for blog URL" for="agca_admin_menu_brand_link">Change branding logo link</label>
								</th>
								<td>
									<input id="agca_admin_menu_brand_link" type="text" size="47" name="agca_admin_menu_brand_link" value="<?php echo get_option('agca_admin_menu_brand_link'); ?>" /><input type="button" class="agca_button" onClick="jQuery('#agca_admin_menu_brand_link').val('');" value="Clear" />
									&nbsp;<p><i>Put here a link for branding logo</i>.</p>
								</td>
							</tr> 
							<tr valign="center">
								<th scope="row">
									<label title="Choose how admin menu should behave on mobile devices / small screens" for="agca_admin_menu_autofold">Admin menu auto folding</label>
								</th>
								<td>									
									<select title="Choose how admin menu should behave on mobile devices / small screens" class="agca-selectbox" name="agca_admin_menu_autofold" >
										<option value="" <?php echo (get_option('agca_admin_menu_autofold') == "")?" selected ":""; ?> >Default</option>
										<option value="force" <?php echo (get_option('agca_admin_menu_autofold') == "force")?" selected ":""; ?> >Force admin menu auto-folding</option>
										<option value="disable" <?php echo (get_option('agca_admin_menu_autofold') == "disable")?" selected ":""; ?> >Disable admin menu auto-folding</option>
									</select>
								</td>
							</tr> 
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">Add New Menu Items</h3></div>
								</td>
								<td>									
								</td>
							</tr> 
							<tr>
								<td colspan="2">
									
									<table id="ag_add_adminmenu">									
										<tr>
											<td colspan="2">
												name:<input type="text" size="47" title="New button visible name" id="ag_add_adminmenu_name" name="ag_add_adminmenu_name" />
												url:<input type="text" size="47" title="New button link" id="ag_add_adminmenu_url" name="ag_add_adminmenu_url" />
												<select id="ag_add_adminmenu_target" class="agca-selectbox" style="width:64px">
													<option value="_blank" selected >blank</option>
													<option value="_self">self</option>
													<option value="_parent">parent</option>													
													<option value="_top">top</option>
												</select>
												<input type="button" id="ag_add_adminmenu_button" class="agca_button" title="Add new item button" name="ag_add_adminmenu_button" value="Add new item" />	
											</td><td></td>	
										</tr>
									</table>
								<input type="hidden" size="47" id="ag_add_adminmenu_json" name="ag_add_adminmenu_json" value="<?php echo htmlspecialchars(get_option('ag_add_adminmenu_json')); ?>" />									
								</td>						
								<td>									
								</td>								
							</tr>
							</table>
						</div>
						<div id="section_ag_colorizer_settings" style="display:none" class="ag_section">
						<h2 class="section_title">Colorizer Page</h2>
						<br />						
						<table class="form-table" width="500px">	
							<tr valign="center" class="ag_table_major_options">
								<td><label for="agca_colorizer_turnonoff"><strong>Turn on/off Colorizer configuration</strong></label></td>
								<td><strong><input class="agca-radio" type="radio" name="agca_colorizer_turnonoff" title="Turn ON Colorizer configuration" value="on" <?php if(get_option('agca_colorizer_turnonoff') == 'on') echo 'checked="checked" '; ?> /><span class="agca-radio-text" style="color:green">ON</span>&nbsp;&nbsp;&nbsp;&nbsp;<input class="agca-radio" type="radio" name="agca_colorizer_turnonoff" title="Turn OFF Colorizer configuration" value="off" <?php if(get_option('agca_colorizer_turnonoff') != 'on') echo 'checked="checked"'; ?> /><span class="agca-radio-text" style="color:red">OFF</span></strong></td>
							</tr>	
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">Global Color Options</h3></div>
								</td>
								<td>									
								</td>
							</tr>
							<tr valign="center">
								<th><label title="Change admin page background color" for="color_background">Background color:</label></th>
								<td><input type="text" id="color_background" name="color_background" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_background']); ?>" />
									<input type="button" alt="color_background" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_background" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>	
                                                        <tr valign="center">
								<th><label title="Change login page background color" for="login_color_background">Login page background color:</label></th>
								<td><input type="text" id="login_color_background" name="login_color_background" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['login_color_background']); ?>" />
									<input type="button" alt="login_color_background" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="login_color_background" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
							<tr valign="center">
								<th><label title="Change admin bar (on top) color in admin panel" for="color_header">Admin bar color:</label></th>
								<td><input type="text" id="color_header" name="color_header" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_header']); ?>" />
									<input type="button" alt="color_header" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_header" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">Admin Menu Color Options</h3></div>
								</td>
								<td>									
								</td>
							</tr>
							<tr valign="center">
								<th><label title="Change button background color" for="color_admin_menu_top_button_background">Button background color:</label></th>
								<td><input type="text" id="color_admin_menu_top_button_background" name="color_admin_menu_top_button_background" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_menu_top_button_background']); ?>" />
									<input type="button" alt="color_admin_menu_top_button_background" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_menu_top_button_background" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
                                                         <tr valign="center">
								<th><label title="Change button text color" for="color_admin_menu_font">Button text color:</label></th>
								<td><input type="text" id="color_admin_menu_font" name="color_admin_menu_font" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_menu_font']); ?>" />
									<input type="button" alt="color_admin_menu_font" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_menu_font" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
                                                        <tr valign="center">
								<th><label title="Change button background color for current button" for="color_admin_menu_top_button_current_background">Button current background color:</label></th>
								<td><input type="text" id="color_admin_menu_top_button_current_background" name="color_admin_menu_top_button_current_background" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_menu_top_button_current_background']); ?>" />
									<input type="button" alt="color_admin_menu_top_button_current_background" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_menu_top_button_current_background" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
                                                        <tr valign="center">
								<th><label title="Change button background color on mouseover" for="color_admin_menu_top_button_hover_background">Button hover background color:</label></th>
								<td><input type="text" id="color_admin_menu_top_button_hover_background" name="color_admin_menu_top_button_hover_background" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_menu_top_button_hover_background']); ?>" />
									<input type="button" alt="color_admin_menu_top_button_hover_background" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_menu_top_button_hover_background" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
                                                        <tr valign="center">
								<th><label title="Change button top border color" for="color_admin_menu_submenu_border_top">Button border top color:</label></th>
								<td><input type="text" id="color_admin_menu_submenu_border_top" name="color_admin_menu_submenu_border_top" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_menu_submenu_border_top']); ?>" />
									<input type="button" alt="color_admin_menu_submenu_border_top" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_menu_submenu_border_top" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
							<tr valign="center">
								<th><label title="Change button bottom border color" for="color_admin_menu_submenu_border_bottom">Button border bottom color:</label></th>
								<td><input type="text" id="color_admin_menu_submenu_border_bottom" name="color_admin_menu_submenu_border_bottom" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_menu_submenu_border_bottom']); ?>" />
									<input type="button" alt="color_admin_menu_submenu_border_bottom" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_menu_submenu_border_bottom" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
                                                        </tr>    
							<tr valign="center">
								<th><label title="Change submenu item background color" for="color_admin_menu_submenu_background">Submenu button background color:</label></th>
								<td><input type="text" id="color_admin_menu_submenu_background" name="color_admin_menu_submenu_background" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_menu_submenu_background']); ?>" />
									<input type="button" alt="color_admin_menu_submenu_background" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_menu_submenu_background" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>         
                                                        <tr valign="center">
								<th><label title="Change submenu item background color on mouseover" for="color_admin_menu_submenu_background_hover">Submenu button hover background color:</label></th>
								<td><input type="text" id="color_admin_menu_submenu_background_hover" name="color_admin_menu_submenu_background_hover" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_menu_submenu_background_hover']); ?>" />
									<input type="button" alt="color_admin_menu_submenu_background_hover" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_menu_submenu_background_hover" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>  
                                                         <tr valign="center">
								<th><label title="Change submenu item text color" for="color_admin_submenu_font">Submenu text color:</label></th>
								<td><input type="text" id="color_admin_submenu_font" name="color_admin_submenu_font" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_submenu_font']); ?>" />
									<input type="button" alt="color_admin_submenu_font" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_submenu_font" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>                                                       
							<?php if($wpversion >= 3.2) { ?>
							<tr valign="center">
								<th><label title="Change background color of element behind admin menu" for="color_admin_menu_behind_background">Wrapper background color:</label></th>
								<td><input type="text" id="color_admin_menu_behind_background" name="color_admin_menu_behind_background" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_menu_behind_background']); ?>" />
									<input type="button" alt="color_admin_menu_behind_background" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_menu_behind_background" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
							<tr valign="center">
								<th><label title="Change border color of element behind admin menu" for="color_admin_menu_behind_border">Wrapper border color:</label></th>
								<td><input type="text" id="color_admin_menu_behind_border" name="color_admin_menu_behind_border" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_admin_menu_behind_border']); ?>" />
									<input type="button" alt="color_admin_menu_behind_border" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_admin_menu_behind_border" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
							<?php } ?>
							<!--<tr valign="center">
								<th><label title="Change background submenu color on mouse over in admin menu" for="color_admin_menu_submenu_background_over">Submenu button background (Mouse over):</label></th>
								<td><input type="text" id="color_admin_menu_submenu_background_over" name="color_admin_menu_submenu_background_over" class="color_picker" value="#123456" />
									<input type="button" alt="color_admin_menu_submenu_background_over" class="pick_color_button" value="Pick color" />
								</td>
							</tr>-->
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">Font Color Options</h3></div>
								</td>
								<td>									
								</td>
							</tr>
							<tr valign="center">
								<th><label title="Change color in content text" for="color_font_content">Content text color:</label></th>
								<td><input type="text" id="color_font_content" name="color_font_content" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_font_content']); ?>" />
									<input type="button" alt="color_font_content" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_font_content" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
							<tr valign="center">
								<th><label title="Change color of admin bar text" for="color_font_header">Admin bar text color:</label></th>
								<td><input type="text" id="color_font_header" name="color_font_header" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_font_header']); ?>" />
									<input type="button" alt="color_font_header" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_font_header" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
							<tr valign="center">
								<th><label title="Change color in fotter text" for="color_font_footer">Footer text color:</label></th>
								<td><input type="text" id="color_font_footer" name="color_font_footer" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_font_footer']); ?>" />
									<input type="button" alt="color_font_footer" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_font_footer" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>	
							<tr valign="center">
								<td colspan="2">
										<div class="ag_table_heading"><h3 tabindex="0">Widgets Color Options</h3></div>
								</td>
								<td>									
								</td>
							</tr>
							<tr valign="center">
								<th><label title="Change color in header text" for="color_widget_bar">Title bar background color:</label></th>
								<td><input type="text" id="color_widget_bar" name="color_widget_bar" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_widget_bar']); ?>" />
									<input type="button" alt="color_widget_bar" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_widget_bar" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>
							<tr valign="center">
								<th><label title="Change widget background color" for="color_widget_background">Background color:</label></th>
								<td><input type="text" id="color_widget_background" name="color_widget_background" class="color_picker" value="<?php echo htmlspecialchars($this->colorizer['color_widget_background']); ?>" />
									<input type="button" alt="color_widget_background" class="pick_color_button agca_button" value="Pick color" />
									<input type="button" alt="color_widget_background" class="pick_color_button_clear agca_button" value="Clear" />
								</td>
							</tr>	
							</table>
							<input type="hidden" size="47" id="ag_colorizer_json" name="ag_colorizer_json" value="<?php echo htmlspecialchars(get_option('ag_colorizer_json')); ?>" />	
							 <div id="picker"></div>			
						</div>
						<div id="section_templates" style="display:none" class="ag_section">	
							<h2 class="section_title" tabindex="-1"><span style="float:left">Admin Themes</span><span style="width:100px;color:red;font-size:15px;float:left;margin-top:-8px;margin-left:6px;display:block">(beta)</span></h2>											
							<br /><br />						
							<table class="form-table" width="500px">					
							<tr valign="center">								
								<td>	
									<div id="agca_templates">									
										
									</div>									
								</td>								
							</tr>	
							<tr>							
								<td>
									<div id="advanced_template_options" style="display:none">
										<h4>Advanced Theme Actions</h4>
										<p style="color:red;"><strong>WARNING:</strong> Use these theme actions only if you are experiencing some problems with AGCA themes. With these options you can deactivate or remove all installed themes.</p>
										<p><a href="javascript:agca_activateTemplate('');" title="When used, currently applied AGCA theme will be disabled</br>and WordPress will use default admin UI.</br>Themes will not be removed, and you can use them again.">DEACTIVATE CURRENT THEME</a> - themes will be deactivated, but still installed.</p>
										<p><a href="javascript:agca_removeAllTemplates();" title="All themes will be removed, including all theme settings and customizations.</br>If you're using commercial theme, you can install it again on the same site and activation will not be charged">REMOVE ALL THEMES</a> - installed themes will be removed.</p>										
									</div>
								</td>
							</tr>
							</table>
						</div>
                                                <div id="section_advanced" style="display:none" class="ag_section">
                                                                        <h2 class="section_title" tabindex="-1">Advanced</h2>
                                                                        
                                                                                <br /><br />					
                                                                                <table class="form-table" width="500px">
																				
																					<tr valign="center">
																						<th scope="row">
																							<label title="Choose which capability will be used to distinct admin user from other users.</br>If customizations are not applied for admin users, this setting will be used to define admin users." for="agca_admin_capability">Distinguish admin from other users by capability:</label>
																						</th>
																						<td><?php echo $this->admin_capabilities; ?>&nbsp;&nbsp;<i>(<strong>edit_dashboard</strong> is selected by default)</i>																							
																						</td>
																						<td>
																						</td>
																					</tr> 																					
																					<tr valign="center">
																					<td colspan="2">
																						<br />
																						<p><i><strong>Info: </strong>These options will override existing customizations.</i></p>					
																						<br />
																					</td><td></td>
																					</tr>
                                                                                    <tr valign="center">
                                                                                            <th scope="row">
                                                                                                    <label title="Add custom CSS script to override existing styles" for="agca_script_css">Custom CSS Script</em></label>
                                                                                            </th>
                                                                                            <td>
                                                                                            <textarea style="width:100%;height:200px" title="Add custom CSS script to override existing styles" rows="5" id="agca_custom_css"  name="agca_custom_css" cols="40"><?php echo htmlspecialchars(get_option('agca_custom_css')); ?></textarea>
                                                                                            </td>
                                                                                    </tr>	
                                                                                    <tr valign="center">
                                                                                            <th scope="row">
                                                                                                    <label title="Add additional custom JavaScript" for="agca_custom_js">Custom JavaScript</label>
                                                                                            </th>
                                                                                            <td>
                                                                                            <textarea style="width:100%;height:200px" title="Add additional custom JavaScript" rows="5" name="agca_custom_js"  id="agca_custom_js" cols="40"><?php echo htmlspecialchars(get_option('agca_custom_js')); ?></textarea>
                                                                                            </td>
                                                                                    </tr>
                                                                                     <tr valign="center">
                                                                                            <th scope="row">
                                                                                                    <label title="Export / import settings" for="agca_export_import">Export / import settings</label>
                                                                                            </th>
                                                                                            <td id="import_file_area">
                                                                                                <div id="export_settings_additional"  style="display: none" ><input class="agca-checkbox" type="checkbox" id="export_settings_include_admin_menu" name="export_settings_include_admin_menu" />&nbsp;<label title="Includes 'Admin Menu' configuration in exported settings.</br>Include admin menu settings only if your admin menu looks the same on multiple sites.</br>If configurations are different, imported menu settings could be wrong. In that case, use 'Reset Settings' button from 'Admin Menu' section.</br>(Custom buttons and menu configuration will be included anyway)">Include Admin Menu(?)</label></div> 
                                                                                                <input class="agca_button"  type="button" name="agca_export_settings" value="Export Settings" onclick="exportSettings();"/></br>
                                                                                                <input type="file" id="settings_import_file" name="settings_import_file" style="display: none"/>       
                                                                                                    <input type="hidden" id="_agca_import_settings" name="_agca_import_settings" value="false" /> 
                                                                                                    <input type="hidden" id="_agca_export_settings" name="_agca_export_settings" value="false" /> 
                                                                                               <input class="agca_button" type="button" name="agca_import_settings" value="Import Settings" onclick="importSettings();"/>
                                                                                            </td>                                                                                        
                                                                                         
                                                                                                 
                                                                                            
                                                                                       
                                                                                    </tr>
                                                                                    
                                                                                </table>
                                                </div>
				<br /><br /><br />
				<p class="submit">				
				<input type="button" id="save_plugin_settings" style="padding:0px" title="Save AG Custom Admin configuration" class="button-primary" value="<?php _e('Save Changes') ?>" onClick="savePluginSettings()" />
				</p>        
                                
			</form>
			<form id="agca_templates_form" name="agca_templates_form" action="<?php echo $_SERVER['PHP_SELF'];?>?page=ag-custom-admin/plugin.php" method="post">
					<input type="hidden" name="_agca_save_template" value="true" />
					<input type="hidden" id="templates_data" name="templates_data" value="" />								
					<input type="hidden" id="templates_name" name="templates_name" value="" />			
			</form>		
			</div>
							
										<br />
			<br /><br /><br /><p id="agca_footer_support_info">WordPress 'AG Custom Admin' plugin by Argonius. If you have any questions, ideas for future development or if you found a bug or having any issues regarding this plugin, please visit plugin's <a target="_blank" href="http://agca.argonius.com/ag-custom-admin/">SUPPORT</a> page. <br /><br />You can also support development of this plugin if you <a target="_blank" href="http://agca.argonius.com/ag-custom-admin/support-for-future-development">Make a donation</a>. Thanks!<br /><br />Have a nice blogging!</p><br />
		<?php
	}
}
//<link rel="stylesheet" type="text/css" href="<?php echo trailingslashit(plugins_url(basename(dirname(__FILE__)))); ? >style/agca.css" /> 
//<link rel="stylesheet" type="text/css" href="http://localhost/wp/351/wp-content/plugins/ag-custom-admin/style/agca.css" />
?>