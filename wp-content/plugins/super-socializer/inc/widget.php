<?php 
defined('ABSPATH') or die("Cheating........Uh!!");
/**
 * Widget for Social Login
 */
class TheChampLoginWidget extends WP_Widget { 
	/** constructor */ 
	function TheChampLoginWidget() { 
		parent::WP_Widget( 
			'TheChampLogin', //unique id 
			__('Super Socializer - Login'), //title displayed at admin panel
			array(  
				'description' => __( 'Let your website users login/register using their favorite Social ID Provider, such as Facebook, Twitter, Google+, LinkedIn', 'TheChamp' )) 
			); 
	}
	
	/** This is rendered widget content */ 
	function widget( $args, $instance ) {
		// if social login is disabled, return
		if(!the_champ_social_login_enabled()){
			return;
		}
		extract( $args ); 
		if($instance['hide_for_logged_in']==1 && is_user_logged_in()) return;
		echo $before_widget;
		if( !empty( $instance['title'] ) && !is_user_logged_in() ){ 
			$title = apply_filters( 'widget_title', $instance[ 'title' ] ); 
			echo $before_title . $title . $after_title;
		}
		if( !empty( $instance['before_widget_content'] ) ){ 
			echo '<div>' . $instance['before_widget_content'] . '</div>';
		}
		if(!is_user_logged_in()){
			echo the_champ_login_button(true);
		}else{
			global $theChampLoginOptions, $user_ID;
			$userInfo = get_userdata($user_ID);
			echo "<div style='height:80px;width:180px'><div style='width:63px;float:left;'>";
			if(($userAvatar = get_user_meta($user_ID, 'thechamp_avatar', true)) !== false && strlen(trim($userAvatar)) > 0){
				echo '<img alt="Social Avatar" src="'.$userAvatar.'" height = "60" width = "60" title="'.$userInfo->user_login.'" style="border:2px solid #e7e7e7;"/>';
			}else{
				echo @get_avatar($user_ID, 60, $default, $alt);   
			}
			echo "</div><div style='float:left; margin-left:10px'>";
			echo str_replace('-', ' ', $userInfo -> user_login);
			echo '<br/><a href="' . wp_logout_url(home_url()) . '">' .__('Log Out', 'LoginRadius') . '</a></div></div>';
		}
		echo '<div style="clear:both"></div>';
		if( !empty( $instance['after_widget_content'] ) ){ 
			echo '<div>' . $instance['after_widget_content'] . '</div>';
		}
		echo $after_widget; 
	}  

	/** Everything which should happen when user edit widget at admin panel */ 
	function update( $new_instance, $old_instance ) { 
		$instance = $old_instance; 
		$instance['title'] = strip_tags( $new_instance['title'] ); 
		$instance['before_widget_content'] = $new_instance['before_widget_content']; 
		$instance['after_widget_content'] = $new_instance['after_widget_content']; 
		$instance['hide_for_logged_in'] = $new_instance['hide_for_logged_in'];  

		return $instance; 
	}  

	/** Widget options in admin panel */ 
	function form( $instance ) { 
		/* Set up default widget settings. */ 
		$defaults = array( 'title' => 'Login with your Social Account', 'before_widget_content' => '', 'after_widget_content' => '' );  

		foreach( $instance as $key => $value )  
			$instance[ $key ] = esc_attr( $value );  

		$instance = wp_parse_args( (array)$instance, $defaults ); 
		?> 
		<p> 
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'TheChamp' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" /> 
			<label for="<?php echo $this->get_field_id( 'before_widget_content' ); ?>"><?php _e( 'Before widget content:', 'TheChamp' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'before_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'before_widget_content' ); ?>" type="text" value="<?php echo $instance['before_widget_content']; ?>" /> 
			<label for="<?php echo $this->get_field_id( 'after_widget_content' ); ?>"><?php _e( 'After widget content:', 'TheChamp' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'after_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'after_widget_content' ); ?>" type="text" value="<?php echo $instance['after_widget_content']; ?>" /> 
			<br /><br />
			<label for="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>"><?php _e( 'Hide for logged in users:', 'TheChamp' ); ?></label> 
			<input type="checkbox" id="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>" name="<?php echo $this->get_field_name( 'hide_for_logged_in' ); ?>" type="text" value="1" <?php if(isset($instance['hide_for_logged_in']) && $instance['hide_for_logged_in']==1) echo 'checked="checked"'; ?> /> 
		</p> 
<?php 
  } 
} 
add_action( 'widgets_init', create_function( '', 'return register_widget( "TheChampLoginWidget" );' )); 

/**
 * Widget for Social Sharing
 */
class TheChampSharingWidget extends WP_Widget { 
	/** constructor */ 
	function TheChampSharingWidget() { 
		parent::WP_Widget( 
			'TheChampHorizontalSharing', //unique id 
			'Super Socializer - Sharing', //title displayed at admin panel 
			//Additional parameters 
			array(
				'description' => __( 'Let your website users share content on popular Social networks like Facebook, Twitter, Tumblr, Google+ and many more', 'TheChamp' )) 
			); 
	}  

	/** This is rendered widget content */ 
	function widget( $args, $instance ) { 
		// return if sharing is disabled
		if(!the_champ_social_sharing_enabled()){
			return;
		}
		extract( $args );
		if($instance['hide_for_logged_in']==1 && is_user_logged_in()) return;
		
		echo "<div class='the_champ_sharing_container' champ-data-href='".site_url()."'>";
		
		echo $before_widget;
		
		if( !empty( $instance['title'] ) ){ 
			$title = apply_filters( 'widget_title', $instance[ 'title' ] ); 
			echo $before_title . $title . $after_title;
		}

		if( !empty( $instance['before_widget_content'] ) ){ 
			echo '<div>' . $instance['before_widget_content'] . '</div>'; 
		}
		echo the_champ_prepare_sharing_html(site_url());

		if( !empty( $instance['after_widget_content'] ) ){ 
			echo '<div>' . $instance['after_widget_content'] . '</div>'; 
		}
		
		echo "</div>";
		echo $after_widget;
	}  

	/** Everything which should happen when user edit widget at admin panel */ 
	function update( $new_instance, $old_instance ) { 
		$instance = $old_instance; 
		$instance['title'] = strip_tags( $new_instance['title'] ); 
		$instance['before_widget_content'] = $new_instance['before_widget_content']; 
		$instance['after_widget_content'] = $new_instance['after_widget_content']; 
		$instance['hide_for_logged_in'] = $new_instance['hide_for_logged_in'];  

		return $instance; 
	}  

	/** Widget edit form at admin panel */ 
	function form( $instance ) { 
		/* Set up default widget settings. */ 
		$defaults = array( 'title' => 'Share the joy', 'before_widget_content' => '', 'after_widget_content' => '' );

		foreach( $instance as $key => $value ){
			$instance[ $key ] = esc_attr( $value );
		}
		
		$instance = wp_parse_args( (array)$instance, $defaults ); 
		?> 
		<p> 
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'TheChamp' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" /> 
			<label for="<?php echo $this->get_field_id( 'before_widget_content' ); ?>"><?php _e( 'Before widget content:', 'TheChamp' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'before_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'before_widget_content' ); ?>" type="text" value="<?php echo $instance['before_widget_content']; ?>" /> 
			<label for="<?php echo $this->get_field_id( 'after_widget_content' ); ?>"><?php _e( 'After widget content:', 'TheChamp' ); ?></label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'after_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'after_widget_content' ); ?>" type="text" value="<?php echo $instance['after_widget_content']; ?>" /> 
			<br /><br /><label for="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>"><?php _e( 'Hide for logged in users:', 'TheChamp' ); ?></label> 
			<input type="checkbox" id="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>" name="<?php echo $this->get_field_name( 'hide_for_logged_in' ); ?>" type="text" value="1" <?php if(isset($instance['hide_for_logged_in'])  && $instance['hide_for_logged_in']==1) echo 'checked="checked"'; ?> /> 
		</p> 
<?php 
  } 
} 
add_action( 'widgets_init', create_function( '', 'return register_widget( "TheChampSharingWidget" );' ));