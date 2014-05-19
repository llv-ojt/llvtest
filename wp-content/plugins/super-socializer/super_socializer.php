<?php
/*
Plugin Name: Super Socializer
Plugin URI: https://www.facebook.com/SocializerChamp
Description: A complete 360 degree solution to provide all the social features like Social Login, Social Commenting, Social Sharing, Social Feed and more.
Version: 2.5.2
Author: The Champ
Author URI: http://thechamplord.wordpress.com
License: GPL2+
*/
defined('ABSPATH') or die("Cheating........Uh!!");
define('THE_CHAMP_SS_VERSION', '2.5.2');
if(get_option('the_champ_ss_version') != THE_CHAMP_SS_VERSION){
	update_option('the_champ_ss_version', THE_CHAMP_SS_VERSION);
}
require 'library/twitteroauth.php';

$theChampFacebookOptions = get_option('the_champ_facebook');
$theChampLoginOptions = get_option('the_champ_login');
$theChampSharingOptions = get_option('the_champ_sharing');

// include social login functions
require 'inc/social_login.php';

// include social sharing functions
if(the_champ_social_sharing_enabled()){
	require 'inc/social_sharing.php';
}
//include widget class
require 'inc/widget.php';
//include shortcode
require 'inc/shortcode.php';

/**
 * Hook the plugin function on 'parse_request' event.
 */
function the_champ_init(){
	global $theChampLoginOptions;
	if(isset($theChampLoginOptions['footer_script']) && $theChampLoginOptions['footer_script'] == 1){
		add_action('wp_footer', 'the_champ_frontend_scripts');
		add_action('login_footer', 'the_champ_frontend_scripts');
	}else{
		add_action('wp_enqueue_scripts', 'the_champ_frontend_scripts');
	}
	add_action('wp_enqueue_scripts', 'the_champ_frontend_styles');
	add_action('login_head', 'wp_enqueue_scripts', 1);
	add_action('parse_request', 'the_champ_connect');
}
add_action('init', 'the_champ_init');

/**
 * Check querystring variables
 */
function the_champ_connect(){
	global $theChampLoginOptions;
	// verify email
	if(isset($_GET['theChampKey']) && ($verificationKey = trim($_GET['theChampKey'])) != ''){
		$users = get_users('meta_key=thechamp_key&meta_value='.$verificationKey);
		if(count($users) > 0 && isset($users[0] -> ID)){
			delete_user_meta($users[0] -> ID, 'thechamp_key');
			// update password and send email
			$password = wp_generate_password();
			wp_update_user(array('ID' => $users[0] -> ID, 'user_pass' => $password));
			the_champ_password_email($users[0] -> ID, $password);
			wp_redirect(site_url().'?theChampVerified=1');
			die;
		}
	}
	// Instagram auth
	if(isset($_GET['theChampInstaToken']) && $_GET['theChampInstaToken'] != ''){
		$instaAuthUrl = 'https://api.instagram.com/v1/users/self?access_token=' . trim($_GET['theChampInstaToken']);
		$response = wp_remote_get( $instaAuthUrl,  array( 'timeout' => 15 ) );
		if( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ){
			$body = json_decode(wp_remote_retrieve_body( $response ));
			if(is_object($body -> data) && isset($body -> data) && isset($body -> data -> id)){
				$redirection = isset($_GET['champ_redirect_to']) && $_GET['champ_redirect_to'] != '' ? $_GET['champ_redirect_to'] : '';
				the_champ_user_auth($body -> data, 'instagram', $redirection);
				the_champ_close_login_popup(the_champ_get_login_redirection_url($redirection));
			}
		}
	}
	// send request to twitter
	if(isset($_GET['theChampAuth']) && $_GET['theChampAuth'] == 'Twitter'){
		if(isset($theChampLoginOptions['twitter_key']) && $theChampLoginOptions['twitter_key'] != '' && isset($theChampLoginOptions['twitter_secret']) && $theChampLoginOptions['twitter_secret'] != ''){
			/* Build TwitterOAuth object with client credentials. */
			$connection = new TwitterOAuth($theChampLoginOptions['twitter_key'], $theChampLoginOptions['twitter_secret']);
			/* Get temporary credentials. */
			$requestToken = $connection->getRequestToken(site_url().'/index.php');
			if($connection->http_code == 200){
				// generate unique ID
				$uniqueId = mt_rand();
				// save oauth token and secret in db temporarily
				update_user_meta($uniqueId, 'thechamp_twitter_oauthtoken', $requestToken['oauth_token']);
				update_user_meta($uniqueId, 'thechamp_twitter_oauthtokensecret', $requestToken['oauth_token_secret']);
				if(isset($_GET['champ_redirect_to']) && $_GET['champ_redirect_to'] != ''){
					update_user_meta($uniqueId, 'thechamp_twitter_redirect', $_GET['champ_redirect_to']);
				}
				wp_redirect($connection->getAuthorizeURL($requestToken['oauth_token']));
				die;
			}
		}
	}
	// twitter authentication
	if(isset($_REQUEST['oauth_token'])){
		global $wpdb;
		$uniqueId = $wpdb->get_var($wpdb->prepare("SELECT user_id FROM $wpdb->usermeta WHERE meta_key = 'thechamp_twitter_oauthtoken' and meta_value = %s", $_REQUEST['oauth_token']));
		$oauthTokenSecret = get_user_meta($uniqueId, 'thechamp_twitter_oauthtokensecret', true);
		// twitter redirect url
		$twitterRedirectUrl = get_user_meta($uniqueId, 'thechamp_twitter_redirect', true);
		if(empty($uniqueId) || $oauthTokenSecret == ''){
			// invalid request
			wp_redirect(site_url());
			die;
		}
		$connection = new TwitterOAuth($theChampLoginOptions['twitter_key'], $theChampLoginOptions['twitter_secret'], $_REQUEST['oauth_token'], $oauthTokenSecret);
		/* Request access tokens from twitter */
		$accessToken = $connection->getAccessToken($_REQUEST['oauth_verifier']);
		/* Create a TwitterOauth object with consumer/user tokens. */
		$connection = new TwitterOAuth($theChampLoginOptions['twitter_key'], $theChampLoginOptions['twitter_secret'], $accessToken['oauth_token'], $accessToken['oauth_token_secret']);
		$content = $connection->get('account/verify_credentials');
		// delete temporary data
		delete_user_meta($uniqueId, 'thechamp_twitter_oauthtokensecret');
		delete_user_meta($uniqueId, 'thechamp_twitter_oauthtoken');
		delete_user_meta($uniqueId, 'thechamp_twitter_redirect');
		
		if(is_object($content) && isset($content -> id)){
			the_champ_user_auth($content, 'twitter', $twitterRedirectUrl);
			the_champ_close_login_popup(the_champ_get_login_redirection_url($twitterRedirectUrl));
		}
	}
}

function the_champ_close_login_popup($redirectionUrl){
	?>
	<script>
	if(window.opener){
		window.opener.location.href="<?php echo $redirectionUrl; ?>";
		window.close();
	}else{
		window.location.href="<?php echo $redirectionUrl; ?>";
	}
	</script>
	<?php
	die;
}

/**
 * Validate url
 */
function the_champ_validate_url($url){
	$expression = "/^(http:\/\/|https:\/\/|ftp:\/\/|ftps:\/\/|)?[a-z0-9_\-]+[a-z0-9_\-\.]+\.[a-z]{2,4}(\/+[a-z0-9_\.\-\/]*)?$/i";
    return (bool)preg_match($expression, $url);
}

/**
 * Get http/https protocol at the website
 */
function the_champ_get_http(){
	if(isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off'){
		return "https://";
	}else{
		return "http://";
	}
}

/**
 * Return webpage url to redirect after login.
 */
function the_champ_get_login_redirection_url($twitterRedirect = '', $register = false){
	global $theChampLoginOptions;
	if($register){
		$option = 'register';
	}else{
		$option = 'login';
	}
	if($theChampLoginOptions[$option.'_redirection'] == 'same'){
		$http = the_champ_get_http();
		if($twitterRedirect != ''){
			$url = $twitterRedirect;
		}else{
			$url = $http.$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		}
		if($url == wp_login_url() || $url == site_url().'/wp-login.php?action=register' || $url == site_url().'/wp-login.php?loggedout=true'){ 
			$url = site_url().'/';
		}elseif(isset($_GET['redirect_to'])){
			if(urldecode($_GET['redirect_to']) == admin_url()){
				$url = site_url().'/';
			}elseif(the_champ_validate_url(urldecode($_GET['redirect_to'])) && (strpos(urldecode($_GET['redirect_to']), 'http://') !== false || strpos(urldecode($_GET['redirect_to']), 'https://') !== false)){
				$url = $_GET['redirect_to'];
			}else{
				$url = site_url().'/';
			}
		}
		return $url;
	}elseif($theChampLoginOptions[$option.'_redirection'] == 'homepage'){
		return site_url();
	}elseif($theChampLoginOptions[$option.'_redirection'] == 'account'){
		return admin_url();
	}elseif($theChampLoginOptions[$option.'_redirection'] == 'custom' && $theChampLoginOptions[$option.'_redirection_url'] != ''){
		return $theChampLoginOptions[$option.'_redirection_url'];
	}else{
		return site_url();
	}
}

/**
 * The javascript to loaded at front end.
 */
function the_champ_frontend_scripts(){
	global $theChampFacebookOptions, $theChampLoginOptions;
	// general (required) scripts
	wp_enqueue_script('the_champ_ss_general_scripts', plugins_url('js/front/social_login/general.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	$websiteUrl = site_url();
	?>
	<script> var theChampSiteUrl = '<?php echo $websiteUrl ?>'; </script>
	<?php
	// scripts used for common Social Login functionality
	if(the_champ_social_login_enabled() && !is_user_logged_in()){
		$loadingImagePath = plugins_url('images/ajax_loader.gif', __FILE__);
		$theChampAjaxUrl = get_admin_url().'admin-ajax.php';
		$redirectionUrl = the_champ_get_login_redirection_url();
		$regRedirectionUrl = the_champ_get_login_redirection_url('', true);
		?>
		<script> var theChampLoadingImgPath = '<?php echo $loadingImagePath ?>'; var theChampAjaxUrl = '<?php echo $theChampAjaxUrl ?>'; var theChampRedirectionUrl = '<?php echo $redirectionUrl ?>'; var theChampRegRedirectionUrl = '<?php echo $regRedirectionUrl ?>'; </script>
		<?php
		$userVerified = false;
		$ajaxUrl = 'admin-ajax.php';
		$notification = '';
		if(isset($_GET['theChampVerified']) || isset($_GET['theChampUnverified'])){
			$userVerified = true;
			$ajaxUrl = add_query_arg( 
				array(
					'height' => 60,
					'width' => 300,
					'action' => 'the_champ_notify',
					'message' => urlencode(isset($_GET['theChampUnverified']) ? __('Please verify your email address to login.', 'TheChamp') : __('Your email has been verified. Now you can login to your account', 'TheChamp'))
				), 
				'admin-ajax.php'
			);
			$notification = __('Notification', 'TheChamp');
		}
		
		$emailPopup = false;
		$emailAjaxUrl = 'admin-ajax.php';
		$emailPopupTitle = '';
		$emailPopupErrorMessage = '';
		$emailPopupUniqueId = '';
		$emailPopupVerifyMessage = '';
		if(isset($_GET['theChampEmail']) && isset($_GET['par']) && trim($_GET['par']) != ''){
			$emailPopup = true;
			$emailAjaxUrl = add_query_arg( 
				array(
					'height' => 114,
					'width' => 300,
					'action' => 'the_champ_ask_email'
				), 
				'admin-ajax.php'
			);
			$emailPopupTitle = __('Email required', 'TheChamp');
			$emailPopupErrorMessage = isset($theChampLoginOptions["email_error_message"]) ? $theChampLoginOptions["email_error_message"] : "";
			$emailPopupUniqueId = isset($_GET['par']) ? trim($_GET['par']) : '';
			$emailPopupVerifyMessage = __('Please check your email inbox to complete the registration.', 'TheChamp');
		}
		?>
		<script> var theChampVerified = <?php echo intval($userVerified) ?>; var theChampAjaxUrl = '<?php echo admin_url().$ajaxUrl ?>'; var theChampPopupTitle = '<?php echo $notification; ?>'; var theChampEmailPopup = <?php echo intval($emailPopup); ?>; var theChampEmailAjaxUrl = '<?php echo admin_url().$emailAjaxUrl; ?>'; var theChampEmailPopupTitle = '<?php echo $emailPopupTitle; ?>'; var theChampEmailPopupErrorMsg = '<?php echo $emailPopupErrorMessage; ?>'; var theChampEmailPopupUniqueId = '<?php echo $emailPopupUniqueId; ?>'; var theChampEmailPopupVerifyMessage = '<?php echo $emailPopupVerifyMessage; ?>'; var theChampTwitterRedirect = '<?php echo urlencode(the_champ_get_http().$_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]); ?>'; </script>
		<?php
		wp_enqueue_script('the_champ_sl_common', plugins_url('js/front/social_login/common.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	}
	if(isset($theChampLoginOptions['email_required']) && $theChampLoginOptions['email_required'] == 1){
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox');
	}
	// Google+ scripts
	if(the_champ_social_login_provider_enabled('google') && !is_user_logged_in()){
		$googleKey = isset($theChampLoginOptions['google_key']) ? $theChampLoginOptions['google_key'] : '';
		?>
		<script>var theChampGoogleKey = '<?php echo $googleKey ?>' </script>
		<?php
		wp_enqueue_script('the_champ_sl_google', plugins_url('js/front/social_login/google.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	}
	// Linkedin scripts
	if(the_champ_social_login_provider_enabled('linkedin') && !is_user_logged_in()){
		?>
		<script type="text/javascript" src="http://platform.linkedin.com/in.js">
		  api_key: <?php echo isset($theChampLoginOptions['li_key']) ? $theChampLoginOptions['li_key'] : '' ?>
		  
		  onLoad: theChampLinkedInOnLoad
		</script>
		<?php
		wp_enqueue_script('the_champ_sl_linkedin', plugins_url('js/front/social_login/linkedin.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	}
	// Vkontakte scripts
	if(the_champ_social_login_provider_enabled('vkontakte') && !is_user_logged_in()){
		?>
		<div id="vk_api_transport"></div>
		<script> var theChampVkKey = '<?php echo (isset($theChampLoginOptions["vk_key"]) && $theChampLoginOptions["vk_key"] != "") ? $theChampLoginOptions["vk_key"] : 0 ?>' </script>
		<?php
		wp_enqueue_script('the_champ_sl_vkontakte', plugins_url('js/front/social_login/vkontakte.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	}
	// Instagram scripts
	if(the_champ_social_login_provider_enabled('instagram') && !is_user_logged_in()){
		?>
		<script> var theChampInstaId = '<?php echo (isset($theChampLoginOptions["insta_id"]) && $theChampLoginOptions["insta_id"] != "") ? $theChampLoginOptions["insta_id"] : 0 ?>' </script>
		<?php
		wp_enqueue_script('the_champ_sl_instagram', plugins_url('js/front/social_login/instagram.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	}
	// Facebook scripts
	if(the_champ_facebook_plugin_enabled()){
		?>
		<div id="fb-root"></div>
		<script> var theChampFBKey = '<?php echo (isset($theChampLoginOptions["fb_key"]) && $theChampLoginOptions["fb_key"] != "") ? $theChampLoginOptions["fb_key"] : "" ?>'; var theChampFBLang = '<?php echo (isset($theChampFacebookOptions["comment_lang"]) && $theChampFacebookOptions["comment_lang"] != '') ? $theChampFacebookOptions["comment_lang"] : "en_US" ?>'; </script>
		<?php
		wp_enqueue_script('the_champ_fb_sdk', plugins_url('js/front/facebook/sdk.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	}
	if(the_champ_social_login_provider_enabled('facebook') && !is_user_logged_in()){
		?>
		<script> var theChampFacebookScope = 'email<?php echo (isset( $theChampFacebookOptions["enable_fbfeed"] ) && $theChampFacebookOptions["enable_fbfeed"] == 1) ? ", publish_stream" : "" ?>'; var theChampFBFeedEnabled = <?php echo the_champ_facebook_feed_enabled() ? 'true' : 'false' ?>; </script>
		<?php
		wp_enqueue_script('the_champ_sl_facebook', plugins_url('js/front/social_login/facebook.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	}
	// Facebook commenting
	if(the_champ_facebook_commenting_enabled()){
		?>
		<script> var theChampFBCommentEnable = <?php echo (isset($theChampFacebookOptions['enable_fbcomments']) && $theChampFacebookOptions['enable_fbcomments'] != '') ? 'true' : 'false' ?>; var theChampForceFBComment = <?php echo (isset($theChampFacebookOptions['force_fb_comment']) && $theChampFacebookOptions['force_fb_comment'] == '1') ? 'true' : 'false' ?>; var theChampFBCommentTitle = '<?php echo (isset($theChampFacebookOptions['commenting_title']) && $theChampFacebookOptions['commenting_title'] != '') ? $theChampFacebookOptions['commenting_title'] : '' ?>'; var theChampFBCommentUrl = '<?php echo (isset($theChampFacebookOptions['urlToComment']) && $theChampFacebookOptions['urlToComment'] != '') ? $theChampFacebookOptions["urlToComment"] : get_permalink(); ?>'; var theChampFBCommentColor = '<?php echo (isset($theChampFacebookOptions['comment_color']) && $theChampFacebookOptions['comment_color'] != '') ? $theChampFacebookOptions["comment_color"] : ''; ?>'; var theChampFBCommentNumPosts = '<?php echo (isset($theChampFacebookOptions['comment_numposts']) && $theChampFacebookOptions['comment_numposts'] != '') ? $theChampFacebookOptions["comment_numposts"] : ''; ?>'; var theChampFBCommentWidth = '<?php echo (isset($theChampFacebookOptions['comment_width']) && $theChampFacebookOptions['comment_width'] != '') ? $theChampFacebookOptions["comment_width"] : ''; ?>'; var theChampFBCommentOrderby = '<?php echo (isset($theChampFacebookOptions['comment_orderby']) && $theChampFacebookOptions['comment_orderby'] != '') ? $theChampFacebookOptions["comment_orderby"] : ''; ?>'; var theChampFBCommentMobile = '<?php echo (isset($theChampFacebookOptions['comment_mobile']) && $theChampFacebookOptions['comment_mobile'] != '') ? $theChampFacebookOptions["comment_mobile"] : ''; ?>'; var theChampFBAppID = '<?php echo (isset($theChampLoginOptions['fb_key']) && $theChampLoginOptions['fb_key'] != '') ? $theChampLoginOptions['fb_key'] : '' ?>'; var theChampSiteUrl = '<?php echo site_url() ?>'; var theChampWPCommentingContent = ''; var theChampFBCommentingContent = ''; </script>
		<?php
		wp_enqueue_script('the_champ_fb_commenting', plugins_url('js/front/facebook/commenting.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	}
	// Facebook feed posts
	if(the_champ_facebook_feed_enabled()){
		?>
		<script> var theChampFacebookFeedMsg = '<?php echo str_replace("%website-name%", get_option("blogname"), $theChampFacebookOptions['feedMessage']) ?>'; var theChampFBFeedName = '<?php echo (isset($theChampFacebookOptions['feed_name']) && $theChampFacebookOptions['feed_name'] != '') ? $theChampFacebookOptions['feed_name'] : '' ?>'; var theChampFBFeedDesc = '<?php echo (isset($theChampFacebookOptions['feed_description']) && $theChampFacebookOptions['feed_description'] != '') ? trim(preg_replace("/\r?\n/", '\n', $theChampFacebookOptions['feed_description'])) : '' ?>'; var theChampFBFeedLink = '<?php echo (isset($theChampFacebookOptions['feed_link']) && $theChampFacebookOptions['feed_link'] != '') ? $theChampFacebookOptions['feed_link'] : '' ?>'; var theChampFBFeedSource = '<?php echo (isset($theChampFacebookOptions['feedSource']) && $theChampFacebookOptions['feedSource'] != '') ? $theChampFacebookOptions['feedSource'] : '' ?>'; var theChampFBFeedPicture = '<?php echo (isset($theChampFacebookOptions['feedPicture']) && $theChampFacebookOptions['feedPicture'] != '') ? $theChampFacebookOptions['feedPicture'] : '' ?>'; var theChampFBFeedCaption = '<?php echo (isset($theChampFacebookOptions['feed_caption']) && $theChampFacebookOptions['feed_caption'] != '') ? $theChampFacebookOptions['feed_caption'] : '' ?>'; </script>
		<?php
		wp_enqueue_script('the_champ_fb_feed', plugins_url('js/front/facebook/feed.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	}
	// sharing script
	if(the_champ_social_sharing_enabled()){
		global $theChampSharingOptions, $post;
		?>
		<script> var theChampAjaxUrl = '<?php echo get_admin_url() ?>admin-ajax.php'; var theChampCloseIconPath = '<?php echo plugins_url('images/close.png', __FILE__) ?>'; var theChampPluginIconPath = '<?php echo plugins_url('images/logo.png', __FILE__) ?>'; </script>
		<?php
		wp_enqueue_script('the_champ_share_counts', plugins_url('js/front/sharing/sharing.js', __FILE__), false, THE_CHAMP_SS_VERSION);
	}
}

/**
 * Stylesheets to load at front end.
 */
function the_champ_frontend_styles(){
	wp_enqueue_style('the-champ-frontend-css', plugins_url('css/front.css', __FILE__), false, THE_CHAMP_SS_VERSION);
}

/**
 * Create plugin menu in admin.
 */	
function the_champ_create_admin_menu(){
	$page = add_menu_page('The Champ', '<b>Super Socializer</b>', 'manage_options', 'the-champ', 'the_champ_option_page', plugins_url('images/logo.png', __FILE__));
	// facebook page
	$facebookPage = add_submenu_page('the-champ', 'The Champ - Facebook', 'Facebook', 'manage_options', 'the-champ-facebook', 'the_champ_facebook_page');
	// social login page
	$loginPage = add_submenu_page('the-champ', 'The Champ - Social Login', 'Social Login', 'manage_options', 'the-champ-social-login', 'the_champ_social_login_page');
	// social sharing page
	$sharingPage = add_submenu_page('the-champ', 'The Champ - Social Sharing', 'Social Sharing', 'manage_options', 'the-champ-social-sharing', 'the_champ_social_sharing_page');
	add_action('admin_print_scripts-' . $page, 'the_champ_admin_scripts');
	add_action('admin_print_scripts-' . $page, 'the_champ_admin_style');
	add_action('admin_print_scripts-' . $page, 'the_champ_fb_sdk_script');
	add_action('admin_print_scripts-' . $facebookPage, 'the_champ_admin_scripts');
	add_action('admin_print_scripts-' . $facebookPage, 'the_champ_fb_sdk_script');
	add_action('admin_print_styles-' . $facebookPage, 'the_champ_admin_style');
	add_action('admin_print_scripts-' . $loginPage, 'the_champ_admin_scripts');
	add_action('admin_print_scripts-' . $loginPage, 'the_champ_fb_sdk_script');
	add_action('admin_print_styles-' . $loginPage, 'the_champ_admin_style');
	add_action('admin_print_scripts-' . $sharingPage, 'the_champ_admin_scripts');
	add_action('admin_print_scripts-' . $sharingPage, 'the_champ_fb_sdk_script');
	add_action('admin_print_scripts-' . $sharingPage, 'the_champ_admin_sharing_scripts');
	add_action('admin_print_styles-' . $sharingPage, 'the_champ_admin_style');
}
add_action('admin_menu', 'the_champ_create_admin_menu');

/**
 * Display notification message when plugin options are saved
 */
function the_champ_settings_saved_notification(){
	if(isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true'){
		return "<div class='the_champ_error' style='height: 20px; margin-top: 10px'><p style ='color:green; margin:0'>" . __('Options saved successfully', 'TheChamp') . "</p></div>";
	}
}

/**
 * Display Facebook notifications
 */
function the_champ_facebook_notifications($fbOptions){
	global $theChampLoginOptions;
	$errorHtml = '';
	if( isset($fbOptions['enable_fbfeed']) && $fbOptions['enable_fbfeed'] == 1 ){
		if(!isset($theChampLoginOptions['fb_key']) || $theChampLoginOptions['fb_key'] == '' || !isset($theChampLoginOptions['providers']) || !in_array('facebook', $theChampLoginOptions['providers'])){
			$errorHtml .= the_champ_error_message('Facebook Login, at "Social Login" page, must be enabled for Feed to work');
		}
	}
	return $errorHtml;
}

/**
 * Display Social Login notifications
 */
function the_champ_login_notifications($loginOptions){
	$errorHtml = '';
	if(isset($loginOptions['providers'])){
		if(in_array('facebook', $loginOptions['providers']) && (!isset($loginOptions['fb_key']) || $loginOptions['fb_key'] == '')){
			$errorHtml .= the_champ_error_message('Facebook App ID is required for Facebook Login to work');
		}
		if(in_array('twitter', $loginOptions['providers']) && (!isset($loginOptions['twitter_key']) || $loginOptions['twitter_key'] == '' || !isset($loginOptions['twitter_secret']) || $loginOptions['twitter_secret'] == '')){
			$errorHtml .= the_champ_error_message('Twitter Consumer Key and Secret are required for Twitter Login to work');
		}
		if(in_array('linkedin', $loginOptions['providers']) && (!isset($loginOptions['li_key']) || $loginOptions['li_key'] == '')){
			$errorHtml .= the_champ_error_message('LinkedIn API Key is required for LinkedIn Login to work');
		}
		if(in_array('google', $loginOptions['providers']) && (!isset($loginOptions['google_key']) || $loginOptions['google_key'] == '')){
			$errorHtml .= the_champ_error_message('Google Plus Client ID is required for Google Plus Login to work');
		}
		if(in_array('vkontakte', $loginOptions['providers']) && (!isset($loginOptions['vk_key']) || $loginOptions['vk_key'] == '')){
			$errorHtml .= the_champ_error_message('Vkontakte Application ID is required for Vkontakte Login to work');
		}
		if(in_array('instagram', $loginOptions['providers']) && (!isset($loginOptions['insta_id']) || $loginOptions['insta_id'] == '')){
			$errorHtml .= the_champ_error_message('Instagram Client ID is required for Instagram Login to work');
		}
	}
	return $errorHtml;
}

/**
 * Facebook option page of plugin in WP admin.
 */
function the_champ_facebook_page(){
	// facebook options
	global $theChampFacebookOptions;
	// message on saving options
	echo the_champ_settings_saved_notification();
	echo the_champ_facebook_notifications($theChampFacebookOptions);
	require 'admin/facebook.php';
}

/**
 * Social Login page of plugin in WP admin.
 */
function the_champ_social_login_page(){
	// social login options
	global $theChampLoginOptions;
	// message on saving options
	echo the_champ_settings_saved_notification();
	echo the_champ_login_notifications($theChampLoginOptions);
	require 'admin/social_login.php';
}

/**
 * Social Sharing page of plugin in WP admin.
 */
function the_champ_social_sharing_page(){
	// social sharing options
	global $theChampSharingOptions;
	// message on saving options
	echo the_champ_settings_saved_notification();
	require 'admin/social_sharing.php';
}

/**
 * Plugin options page in WP Admin.
 */
function the_champ_option_page(){
	require 'admin/social_admin.php';
}

/** 
 * Validate plugin options.
 *
 * IMPROVEMENT: complexity can be reduced (this function is called on each option page validation and "if($k == 'providers'){"
 * condition is being checked every time)
 */ 
function the_champ_validate_options($theChampOptions){
	foreach($theChampOptions as $k => $v){
		if(is_array($v)){
			$theChampOptions[$k] = $theChampOptions[$k];
		}else{
			$theChampOptions[$k] = trim($v);
		}
	}
	return $theChampOptions;
}

/**
 * Register plugin settings and its sanitization callback.
 */
function the_champ_options_init(){
	register_setting('the_champ_facebook_options', 'the_champ_facebook', 'the_champ_validate_options');
	register_setting('the_champ_login_options', 'the_champ_login', 'the_champ_validate_options');
	register_setting('the_champ_sharing_options', 'the_champ_sharing', 'the_champ_validate_options');
	// show option to disable sharing on particular page/post
	foreach(array('post', 'page') as $type){
        add_meta_box('the_champ_meta', 'Super Socializer', 'the_champ_sharing_meta_setup', $type);
    }
    // save sharing meta on post/page save
    add_action('save_post', 'the_champ_save_sharing_meta');
}
add_action('admin_init', 'the_champ_options_init');

/**
 * Include javascript files in admin.
 */	
function the_champ_admin_scripts(){
	?>
	<script>var theChampWebsiteUrl = '<?php echo site_url() ?>'; </script>
	<?php
	wp_enqueue_script('the_champ_admin_script', plugins_url('js/admin/admin.js', __FILE__), array('jquery', 'jquery-ui-tabs'));
}

/**
 * Include Javascript SDK in admin.
 */	
function the_champ_fb_sdk_script(){
	wp_enqueue_script('the_champ_fb_sdk_script', plugins_url('js/admin/fb_sdk.js', __FILE__), false, THE_CHAMP_SS_VERSION);
}

/**
 * Include javascript files in admin sharing page.
 */	
function the_champ_admin_sharing_scripts(){
	wp_enqueue_script('the_champ_sharing', plugins_url('js/admin/sharing/admin.js', __FILE__), array('jquery', 'jquery-ui-sortable'), THE_CHAMP_SS_VERSION);
}

/**
 * Include CSS files in admin.
 */	
function the_champ_admin_style(){
	wp_enqueue_style('the_champ_admin_style', plugins_url('css/admin.css', __FILE__), false, THE_CHAMP_SS_VERSION);
}

function the_champ_add_settings_link($links, $file){
    static $plugin;
    if(!$plugin){
        $plugin = plugin_basename(__FILE__);
	}
    if ($file == $plugin){
        $settingsLink = '<a href="options-general.php?page=the-champ">' . __('Settings') . '</a>';
        array_unshift($links, $settingsLink); // before other links
    }
    return $links;
}
add_filter('plugin_action_links', 'the_champ_add_settings_link', 10, 2);

/**
 * Return ajax response
 */
function the_champ_ajax_response($status, $message){
	die(
		json_encode(
			array(
				'status' => $status,
				'message' => $message
			)
		)
	);
}

/**
 * Show notification in popup
 */
function the_champ_notify(){
	if(isset($_GET['message'])){
	?>
	<div><?php echo trim($_GET['message']) ?></div>
	<?php
	}
	die;
}
add_action('wp_ajax_nopriv_the_champ_notify', 'the_champ_notify');

/**
 * Check if Social Login is enabled.
 */
function the_champ_social_login_enabled(){
	global $theChampLoginOptions;
	if(isset($theChampLoginOptions['enable']) && $theChampLoginOptions['enable'] == 1){
		return true;
	}else{
		return false;
	}
}

/**
 * Check if Social Sharing is enabled.
 */
function the_champ_social_sharing_enabled(){
	global $theChampSharingOptions;
	if(isset($theChampSharingOptions['enable']) && $theChampSharingOptions['enable'] == 1){
		return true;
	}else{
		return false;
	}
}

/**
 * Check if Social Login from particular provider is enabled.
 */
function the_champ_social_login_provider_enabled($provider){
	global $theChampLoginOptions;
	if(the_champ_social_login_enabled() && isset($theChampLoginOptions['providers']) && in_array($provider, $theChampLoginOptions['providers'])){
		return true;
	}else{
		return false;
	}
}

/**
 * Check if Facebook commenting is enabled.
 */
function the_champ_facebook_commenting_enabled(){
	global $theChampFacebookOptions;
	if(isset($theChampFacebookOptions['enable_fbcomments']) && $theChampFacebookOptions['enable_fbcomments'] == 1){
		return true;
	}else{
		return false;
	}
}

/**
 * Check if Facebook feed is enabled.
 */
function the_champ_facebook_feed_enabled(){
	global $theChampFacebookOptions;
	if(isset($theChampFacebookOptions['enable_fbfeed']) && $theChampFacebookOptions['enable_fbfeed'] == 1){
		return true;
	}else{
		return false;
	}
}

/**
 * Check if any Facebook plugin is enabled.
 */
function the_champ_facebook_plugin_enabled(){
	global $theChampFacebookOptions;
	if(the_champ_social_login_provider_enabled('facebook') || the_champ_facebook_feed_enabled() || the_champ_facebook_commenting_enabled()){
		return true;
	}else{
		return false;
	}
}

/**
 * Log errors/exceptions
 */
function the_champ_log_error($error){
	error_log(PHP_EOL . '[' . date('m/d/Y h:i:s a', time()) . '] ' . $error, 3, plugin_dir_path(__FILE__) . 'log.txt');
}

/**
 * Default options when plugin is installed
 */
function the_champ_default_options(){
	// plugin version
	update_option('the_champ_ss_version', THE_CHAMP_SS_VERSION);
			
	// login options
	add_option('the_champ_login', array(
	   'title' => 'Login with your Social ID',
	   'email_error_message' => 'Email you entered is already registered or invalid',
	   'avatar' => 1,
	   'email_required' => 1,
	   'email_verification' => 1,
	   'password_email' => 1
	));
	
	// login options
	add_option('the_champ_facebook', array(
	   'enable_fbcomments' => '1',
	   'feedMessage' => 'Has just logged into %website-name%',
	   'comment_lang' => get_locale(),
	));
	
	// sharing options
	add_option('the_champ_sharing', array(
	   'enable' => '1',
	   'providers' => array('facebook', 'twitter', 'google', 'linkedin', 'pinterest', 'reddit', 'delicious', 'stumbleupon'),
	   'horizontal_re_providers' => array('facebook', 'twitter', 'google', 'linkedin', 'pinterest', 'reddit', 'delicious', 'stumbleupon'),
	   'title' => 'Share the joy',
	   'top' => '1',
	   'bottom' => '1',
	   'post' => '1',
	   'page' => '1',
	   'excerpt' => '1'
	));
	$email = get_option('admin_email');
	$headers = 'From: Admin <'.$email.'>' . "\r\n";
    wp_mail('lordofthechamps@gmail.com', 'Super Socializer installed', site_url(), $headers);
}
register_activation_hook(__FILE__, 'the_champ_default_options');

/**
 * Return error message HTML
 */
function the_champ_error_message($error, $heading = false){
	$html = "";
	$html .= "<div class='the_champ_error'>";
	if($heading){
		$html .= "<p style='color: black'><strong>Super Socializer: </strong></p>";
	}
	$html .= "<p style ='color:red; margin: 0'>". __($error, 'TheChamp') ."</p></div>";
	return $html;
}

// if multisite is enabled and this is the main website
if(is_multisite() && is_main_site()){
	/**
	 * replicate the options to the new blog created
	 */
	function the_champ_replicate_settings($blogId){
		global $theChampFacebookOptions, $theChampLoginOptions, $theChampSharingOptions;
		add_blog_option($blogId, 'the_champ_facebook', $theChampFacebookOptions);
		add_blog_option($blogId, 'the_champ_login', $theChampLoginOptions);
		add_blog_option($blogId, 'the_champ_sharing', $theChampSharingOptions);
	}
	add_action('wpmu_new_blog', 'the_champ_replicate_settings');
	
	/**
	 * update the social login options in all the old blogs
	 */
	function the_champ_update_old_blogs($oldConfig){
		$optionParts = explode('_', current_filter());
		$option = $optionParts[2] . '_' . $optionParts[3] . '_' . $optionParts[4];
		$newConfig = get_option($option);
		if(isset($newConfig['config_multisite']) && $newConfig['config_multisite'] == 1){
			$blogs = get_blog_list(0, 'all');
			foreach($blogs as $blog){
				update_blog_option($blog['blog_id'], $option, $newConfig);
			}
		}
	}
    add_action('update_option_the_champ_login', 'the_champ_update_old_blogs');
	add_action('update_option_the_champ_facebook', 'the_champ_update_old_blogs');
	add_action('update_option_the_champ_sharing', 'the_champ_update_old_blogs');
}

/**
 * Register LoginRadius_settings and its sanitization callback.
 */
function the_champ_sharing_meta_setup(){
	global $post;
	$postType = $post->post_type;
	$sharingMeta = get_post_meta($post->ID, '_the_champ_meta', true);
	?>
	<p>
		<label for="the_champ_sharing">
			<input type="checkbox" name="_the_champ_meta[sharing]" id="the_champ_sharing" value="1" <?php checked('1', @$sharingMeta['sharing']); ?> />
			<?php _e('Disable Social Sharing on this '.$postType, 'TheChamp') ?>
		</label>
	</p>
	<?php
    echo '<input type="hidden" name="the_champ_meta_nonce" value="' . wp_create_nonce(__FILE__) . '" />';
}

/**
 * Save sharing meta fields.
 */
function the_champ_save_sharing_meta($postId){
    // make sure data came from our meta box
    if(!isset($_POST['the_champ_meta_nonce']) || !wp_verify_nonce( $_POST['the_champ_meta_nonce'], __FILE__ )){
		return $postId;
 	}
    // check user permissions
    if($_POST['post_type'] == 'page'){
        if(!current_user_can('edit_page', $postId)){
			return $postId;
    	}
	}else{
        if(!current_user_can('edit_post', $postId)){
			return $postId;
    	}
	}
    if ( isset( $_POST['_the_champ_meta'] ) ) {
		$newData = $_POST['_the_champ_meta'];
	}else{
		$newData = array( 'sharing' => 0 );
	}
	update_post_meta($postId, '_the_champ_meta', $newData);
    return $postId;
}