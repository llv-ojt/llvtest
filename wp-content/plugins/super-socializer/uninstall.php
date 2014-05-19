<?php
//if uninstall not called from WordPress, exit
if( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
global $wpdb;
$theChampOptions = array(
					'the_champ_login',
					'the_champ_facebook',
					'the_champ_sharing',
					'the_champ_ss_version'
				);
// For Single site
if( !is_multisite() ){
	foreach($theChampOptions as $option){
		delete_option( $option );
	}
	$wpdb->query("delete from $wpdb->usermeta where meta_key like 'thechamp%'");
}else{
	// For Multisite
	$theChampBlogIds = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
	$theChampOriginalBlogId = get_current_blog_id();
	foreach ( $theChampBlogIds as $blogId ){
		switch_to_blog( $blogId );
		foreach($theChampOptions as $option){
			delete_site_option($option);
		}
	}
	switch_to_blog( $theChampOriginalBlogId );
}