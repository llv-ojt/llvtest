<?php 
    // die if not uninstalling
    if( !defined( 'WP_UNINSTALL_PLUGIN' ) )
        exit ();

        delete_option( 'agca_role_allbutadmin' );
		delete_option( 'agca_screen_options_menu' );
		delete_option(  'agca_help_menu' );
		delete_option(  'agca_logout' );
		delete_option(  'agca_remove_your_profile' );
		delete_option(  'agca_logout_only' );
		delete_option(  'agca_options_menu' );
		delete_option(  'agca_howdy' );
		delete_option(  'agca_header' );
		delete_option(  'agca_header_show_logout' );
		delete_option(  'agca_footer' );
		delete_option(  'agca_privacy_options' );
		delete_option(  'agca_header_logo' );
		delete_option(  'agca_header_logo_custom' );
		delete_option(  'agca_wp_logo_custom' );	
        delete_option(  'agca_wp_logo_custom_link' );
		delete_option(  'agca_site_heading' );
		delete_option(  'agca_custom_site_heading' );
		delete_option(  'agca_update_bar' );
		
		delete_option(  'agca_footer_left' );
		delete_option(  'agca_footer_left_hide' );
		delete_option(  'agca_footer_right' );
		delete_option(  'agca_footer_right_hide' );
		
		delete_option( 'agca_login_banner' );
		delete_option( 'agca_login_banner_text' );
		delete_option( 'agca_login_photo_remove' );
		delete_option( 'agca_login_photo_url' );
		delete_option( 'agca_login_photo_href' );
        delete_option( 'agca_login_round_box' );
		delete_option( 'agca_login_round_box_size' );		
	
		delete_option(  'agca_dashboard_icon' );
		delete_option(  'agca_dashboard_text' );
		delete_option(  'agca_dashboard_text_paragraph' );
        delete_option(  'agca_dashboard_widget_welcome' );			
		delete_option(  'agca_dashboard_widget_il' );	
		delete_option(  'agca_dashboard_widget_plugins' );	
		delete_option(  'agca_dashboard_widget_qp' );	
		delete_option(  'agca_dashboard_widget_rn' );	
		delete_option(  'agca_dashboard_widget_rd' );	
		delete_option(  'agca_dashboard_widget_primary' );	
		delete_option(  'agca_dashboard_widget_secondary' );
		delete_option(  'agca_dashboard_widget_activity' );
		
		//WP3.3
		delete_option( 'agca_admin_bar_comments' );
		delete_option( 'agca_admin_bar_new_content' );
		delete_option( 'agca_admin_bar_new_content_post' );
		delete_option( 'agca_admin_bar_new_content_link' );
		delete_option( 'agca_admin_bar_new_content_page' );
		delete_option( 'agca_admin_bar_new_content_user' );
		delete_option( 'agca_admin_bar_new_content_media' );
		delete_option( 'agca_admin_bar_update_notifications' );
		delete_option( 'agca_remove_top_bar_dropdowns' );

		/*Admin menu*/
		delete_option(  'agca_admin_menu_turnonoff' );
		delete_option(  'agca_admin_menu_agca_button_only' );
		delete_option(  'agca_admin_menu_separator_first' );
		delete_option(  'agca_admin_menu_separator_second' );
		delete_option(  'agca_admin_menu_icons' );
        delete_option(  'agca_admin_menu_arrow' );
        delete_option(  'agca_admin_menu_submenu_round' );
        delete_option(  'agca_admin_menu_submenu_round_size' );
        delete_option(  'agca_admin_menu_brand' );
        delete_option(  'agca_admin_menu_brand_link' );   
		delete_option(  'agca_admin_menu_autofold' );		
		delete_option(  'ag_edit_adminmenu_json' );
		delete_option(  'ag_add_adminmenu_json' );
		delete_option(  'ag_colorizer_json' );	
		delete_option(  'agca_colorizer_turnonoff' );
                
        delete_option( 'agca_custom_js' );
        delete_option( 'agca_custom_css' );
				
		delete_option( 'agca_selected_template' );
        delete_option( 'agca_templates' );
		delete_option( 'agca_disablewarning' );	
				
?>