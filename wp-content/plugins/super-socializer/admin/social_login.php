<?php defined('ABSPATH') or die("Cheating........Uh!!"); ?>
<div id="fb-root"></div>

<form action="options.php" method="post">
<?php settings_fields('the_champ_login_options'); ?>
	<div class="metabox-holder">
		<div class="menu_div" id="tabs">
			<h2 class="nav-tab-wrapper" style="height:37px">
			<ul>
				<li style="margin-left:9px"><a style="margin:0; line-height:auto !important; height:23px" class="nav-tab" href="#tabs-1"><?php _e('Basic Configuration', 'TheChamp') ?></a></li>
				<li style="margin-left:9px"><a style="margin:0; line-height:auto !important; height:23px" class="nav-tab" href="#tabs-2"><?php _e('Social Login', 'TheChamp') ?></a></li>
			</ul>
			</h2>
			<div class="menu_containt_div" id="tabs-1">
				<div class="the_champ_left_column">
				<div class="stuffbox">
					<h3 class="hndle"><label><?php _e('Basic Configuration', 'TheChamp');?></label></h3>
					<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
						<tr>
							<th>
							<img id="the_champ_sl_enable_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_login_enable"><?php _e("Enable Social Login", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_login_enable" name="the_champ_login[enable]" type="checkbox" <?php echo isset($theChampLoginOptions['enable']) && $theChampLoginOptions['enable'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_enable_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Master control for Social Login. It must be checked to enable Social Login functionality', 'TheChamp') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_providers_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label><?php _e("Select providers", 'TheChamp'); ?></label>
							</th>
							<td>
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_login_facebook" name="the_champ_login[providers][]" type="checkbox" <?php echo isset($theChampLoginOptions['providers']) && in_array('facebook', $theChampLoginOptions['providers']) ? 'checked = "checked"' : '';?> value="facebook" />
							<label for="the_champ_login_facebook"><?php _e("Facebook", 'TheChamp'); ?></label>
							</div>
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_login_twitter" name="the_champ_login[providers][]" type="checkbox" <?php echo isset($theChampLoginOptions['providers']) && in_array('twitter', $theChampLoginOptions['providers']) ? 'checked = "checked"' : '';?> value="twitter" />
							<label for="the_champ_login_twitter"><?php _e("Twitter", 'TheChamp'); ?></label>
							</div>
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_login_linkedin" name="the_champ_login[providers][]" type="checkbox" <?php echo isset($theChampLoginOptions['providers']) && in_array('linkedin', $theChampLoginOptions['providers']) ? 'checked = "checked"' : '';?> value="linkedin" />
							<label for="the_champ_login_linkedin"><?php _e("LinkedIn", 'TheChamp'); ?></label>
							</div>
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_login_google" name="the_champ_login[providers][]" type="checkbox" <?php echo isset($theChampLoginOptions['providers']) && in_array('google', $theChampLoginOptions['providers']) ? 'checked = "checked"' : '';?> value="google" />
							<label for="the_champ_login_google"><?php _e("Google+", 'TheChamp'); ?></label>
							</div>
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_login_vkontakte" name="the_champ_login[providers][]" type="checkbox" <?php echo isset($theChampLoginOptions['providers']) && in_array('vkontakte', $theChampLoginOptions['providers']) ? 'checked = "checked"' : '';?> value="vkontakte" />
							<label for="the_champ_login_vkontakte"><?php _e("Vkontakte", 'TheChamp'); ?></label>
							</div>
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_login_instagram" name="the_champ_login[providers][]" type="checkbox" <?php echo isset($theChampLoginOptions['providers']) && in_array('instagram', $theChampLoginOptions['providers']) ? 'checked = "checked"' : '';?> value="instagram" />
							<label for="the_champ_login_instagram"><?php _e("Instagram", 'TheChamp'); ?></label>
							</div>
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_providers_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Select Social ID provider to enable in Social Login', 'TheChamp') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_slfb_key_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_fblogin_key"><?php _e("Facebook App ID", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_fblogin_key" name="the_champ_login[fb_key]" type="text" value="<?php echo isset($theChampLoginOptions['fb_key']) ? $theChampLoginOptions['fb_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_slfb_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf(__('Required for Facebook Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Facebook App ID', 'TheChamp'), 'http://thechamplord.wordpress.com/2014/01/16/getting-the-facebook-app-id/') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sltw_key_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_twlogin_key"><?php _e("Twitter Consumer Key", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_twlogin_key" name="the_champ_login[twitter_key]" type="text" value="<?php echo isset($theChampLoginOptions['twitter_key']) ? $theChampLoginOptions['twitter_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sltw_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf(__('Required for Twitter Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Twitter Consumer Key', 'TheChamp'), 'http://thechamplord.wordpress.com/2014/01/28/getting-twitter-consumer-key-and-secret/') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sltw_secret_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_twlogin_secret"><?php _e("Twitter Consumer Secret", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_twlogin_secret" name="the_champ_login[twitter_secret]" type="text" value="<?php echo isset($theChampLoginOptions['twitter_secret']) ? $theChampLoginOptions['twitter_secret'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sltw_secret_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf(__('Required for Twitter Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Twitter Consumer Secret', 'TheChamp'), 'http://thechamplord.wordpress.com/2014/01/28/getting-twitter-consumer-key-and-secret/') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_slli_key_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_lilogin_key"><?php _e("LinkedIn API Key", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_lilogin_key" name="the_champ_login[li_key]" type="text" value="<?php echo isset($theChampLoginOptions['li_key']) ? $theChampLoginOptions['li_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_slli_key_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf(__('Required for LinkedIn Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get LinkedIn API Key', 'TheChamp'), 'http://thechamplord.wordpress.com/2014/01/26/getting-linkedin-api-key/') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_slgp_id_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_gplogin_key"><?php _e("Google+ Client ID", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_gplogin_key" name="the_champ_login[google_key]" type="text" value="<?php echo isset($theChampLoginOptions['google_key']) ? $theChampLoginOptions['google_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_slgp_id_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf(__('Required for GooglePlus Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get GooglePlus Client ID', 'TheChamp'), 'http://thechamplord.wordpress.com/2013/12/30/getting-google-plus-client-id/') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_slvk_id_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_vklogin_key"><?php _e("Vkontakte Application ID", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_vklogin_key" name="the_champ_login[vk_key]" type="text" value="<?php echo isset($theChampLoginOptions['vk_key']) ? $theChampLoginOptions['vk_key'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_slvk_id_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf(__('Required for Vkontakte Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Vkontakte Application ID', 'TheChamp'), 'http://thechamplord.wordpress.com/2014/03/07/how-to-configure-vkontakte-application-and-get-application-id/') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_slinsta_id_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_insta_key"><?php _e("Instagram Client ID", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_insta_key" name="the_champ_login[insta_id]" type="text" value="<?php echo isset($theChampLoginOptions['insta_id']) ? $theChampLoginOptions['insta_id'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_slinsta_id_help_cont">
							<td colspan="2">
							<div>
							<?php echo sprintf(__('Required for Instagram Social Login to work. Please follow the documentation at <a href="%s" target="_blank">this link</a> to get Instagram Client ID', 'TheChamp'), 'http://thechamplord.wordpress.com/2014/04/14/how-to-configure-instagram-application-and-get-client-id/') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_footer_script_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_login_footer_script"><?php _e("Include Javascript in website footer", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_login_footer_script" name="the_champ_login[footer_script]" type="checkbox" <?php echo isset($theChampLoginOptions['footer_script']) && $theChampLoginOptions['footer_script'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_footer_script_help_cont">
							<td colspan="2">
							<div>
							<?php _e('If enabled (recommended), all the Javascript code will be included in the footer of your website.<br/><strong>"wp_footer" and "login_footer" hooks should be there in your Wordpress theme for this to work, if you are not sure about this, keep this option unchecked.</strong>', 'TheChamp') ?>
							</div>
							</td>
						</tr>
					</table>
					</div>
				</div>
				</div>
				<?php include 'help.php'; ?>
			</div>
			
			<div class="menu_containt_div" id="tabs-2">
				<div class="the_champ_left_column">
				<div class="stuffbox">
					<h3 class="hndle"><label><?php _e('Login options', 'TheChamp');?></label></h3>
					<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
						<tr>
							<th>
							<img id="the_champ_sl_title_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_fblogin_title"><?php _e("Title", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_fblogin_title" name="the_champ_login[title]" type="text" value="<?php echo isset($theChampLoginOptions['title']) ? $theChampLoginOptions['title'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_title_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Text to display above the Social Login interface', 'TheChamp') ?>
							</div>
							<img src="<?php echo plugins_url('../images/snaps/title.png', __FILE__); ?>" />
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_loginpage_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_fblogin_enableAtLogin"><?php _e("Enable at login page", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_fblogin_enableAtLogin" name="the_champ_login[enableAtLogin]" type="checkbox" <?php echo isset($theChampLoginOptions['enableAtLogin']) && $theChampLoginOptions['enableAtLogin'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_loginpage_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Social Login interface will get enabled at your Wordpress login page', 'TheChamp') ?>
							</div>
							<img src="<?php echo plugins_url('../images/snaps/sl_wplogin.png', __FILE__); ?>" />
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_regpage_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_fblogin_enableAtRegister"><?php _e("Enable at register page", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_fblogin_enableAtRegister" name="the_champ_login[enableAtRegister]" type="checkbox" <?php echo isset($theChampLoginOptions['enableAtRegister']) && $theChampLoginOptions['enableAtRegister'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_regpage_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Social Login interface will get enabled at your Wordpress registration page', 'TheChamp') ?>
							</div>
							<img src="<?php echo plugins_url('../images/snaps/sl_wpreg.png', __FILE__); ?>" />
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_cmntform_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_fblogin_enableAtComment"><?php _e("Enable at Comment form", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_fblogin_enableAtComment" name="the_champ_login[enableAtComment]" type="checkbox" <?php echo isset($theChampLoginOptions['enableAtComment']) && $theChampLoginOptions['enableAtComment'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_cmntform_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Social Login interface will get enabled at your Wordpress Comment form<br/><strong>Note: Social Login at comment form of your website will not get enabled if Facebook commenting is enabled.</strong>', 'TheChamp') ?>
							</div>
							<img src="<?php echo plugins_url('../images/snaps/sl_wpcomment.png', __FILE__); ?>" />
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_avatar_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_login_avatar"><?php _e("Enable Social avatar", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_login_avatar" name="the_champ_login[avatar]" type="checkbox" <?php echo isset($theChampLoginOptions['avatar']) && $theChampLoginOptions['avatar'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_avatar_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Social profile pictures of the logged in user will be displayed as profile avatar', 'TheChamp') ?>
							</div>
							<img src="<?php echo plugins_url('../images/snaps/sl_wpavatar.png', __FILE__); ?>" />
							<img src="<?php echo plugins_url('../images/snaps/sl_wpavatar2.png', __FILE__); ?>" />
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_emailreq_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_login_email_required"><?php _e("Email required", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_login_email_required" name="the_champ_login[email_required]" type="checkbox" <?php echo isset($theChampLoginOptions['email_required']) && $theChampLoginOptions['email_required'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_emailreq_help_cont">
							<td colspan="2">
							<div>
							<?php _e('If enabled and Social ID provider does not provide user\'s email address on login, user will be asked to provide his/her email address. Otherwise, a dummy email will be generated', 'TheChamp') ?>
							</div>
							<img src="<?php echo plugins_url('../images/snaps/sl_email_required.png', __FILE__); ?>" />
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_emailreq_error_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_login_email_required_error"><?php _e("Error message for 'Email required' popup", 'TheChamp'); ?></label>
							</th>
							<td>
							<textarea rows="4" cols="40" id="the_champ_login_email_required_error" name="the_champ_login[email_error_message]"><?php echo isset($theChampLoginOptions['email_error_message']) ? $theChampLoginOptions['email_error_message'] : '' ?></textarea>
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_emailreq_error_help_cont">
							<td colspan="2">
							<div>
							<?php _e('This message will be displayed to user if it provides invalid or already registered email', 'TheChamp') ?>
							</div>
							<img src="<?php echo plugins_url('../images/snaps/sl_emailreq_message.png', __FILE__); ?>" />
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_emailver_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_password_email_verification"><?php _e("Enable email verification", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_password_email_verification" name="the_champ_login[email_verification]" type="checkbox" <?php echo isset($theChampLoginOptions['email_verification']) && $theChampLoginOptions['email_verification'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_emailver_help_cont">
							<td colspan="2">
							<div>
							<?php _e('If enabled, email provided by the user will be verified by sending a confirmation link to that email. User would not be able to login without verifying his/her email', 'TheChamp') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_postreg_email_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_password_email"><?php _e("Send username-password after user registration", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_password_email" name="the_champ_login[password_email]" type="checkbox" <?php echo isset($theChampLoginOptions['password_email']) && $theChampLoginOptions['password_email'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_postreg_email_help_cont">
							<td colspan="2">
							<div>
							<?php _e('If enabled, an email will be sent to user after registration through Social Login, regarding his/her login credentials (username-password to be able to login via traditional login form)', 'TheChamp') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_loginredirect_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label><?php _e("Login redirection", 'TheChamp'); ?></label>
							</th>
							<td id="the_champ_login_redirection_column">
							<input id="the_champ_login_redirection_same" name="the_champ_login[login_redirection]" type="radio" <?php echo !isset($theChampLoginOptions['login_redirection']) || $theChampLoginOptions['login_redirection'] == 'same' ? 'checked = "checked"' : '';?> value="same" />
							<label for="the_champ_login_redirection_same">Same page where user logged in</label><br/>
							<input id="the_champ_login_redirection_home" name="the_champ_login[login_redirection]" type="radio" <?php echo isset($theChampLoginOptions['login_redirection']) && $theChampLoginOptions['login_redirection'] == 'homepage' ? 'checked = "checked"' : '';?> value="homepage" />
							<label for="the_champ_login_redirection_home">Homepage</label><br/>
							<input id="the_champ_login_redirection_account" name="the_champ_login[login_redirection]" type="radio" <?php echo isset($theChampLoginOptions['login_redirection']) && $theChampLoginOptions['login_redirection'] == 'account' ? 'checked = "checked"' : '';?> value="account" />
							<label for="the_champ_login_redirection_account">Account dashboard</label><br/>
							<input id="the_champ_login_redirection_custom" name="the_champ_login[login_redirection]" type="radio" <?php echo isset($theChampLoginOptions['login_redirection']) && $theChampLoginOptions['login_redirection'] == 'custom' ? 'checked = "checked"' : '';?> value="custom" />
							<label for="the_champ_login_redirection_custom">Custom Url</label><br/>
							<input id="the_champ_login_redirection_url" name="the_champ_login[login_redirection_url]" type="text" value="<?php echo isset($theChampLoginOptions['login_redirection_url']) ? $theChampLoginOptions['login_redirection_url'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_loginredirect_help_cont">
							<td colspan="2">
							<div>
							<?php _e('User will be redirected to the selected page after Social Login', 'TheChamp') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_sl_register_redirect_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label><?php _e("Registration redirection", 'TheChamp'); ?></label>
							</th>
							<td id="the_champ_register_redirection_column">
							<input id="the_champ_register_redirection_same" name="the_champ_login[register_redirection]" type="radio" <?php echo !isset($theChampLoginOptions['register_redirection']) || $theChampLoginOptions['register_redirection'] == 'same' ? 'checked = "checked"' : '';?> value="same" />
							<label for="the_champ_register_redirection_same">Same page where user logged in</label><br/>
							<input id="the_champ_register_redirection_home" name="the_champ_login[register_redirection]" type="radio" <?php echo isset($theChampLoginOptions['register_redirection']) && $theChampLoginOptions['register_redirection'] == 'homepage' ? 'checked = "checked"' : '';?> value="homepage" />
							<label for="the_champ_register_redirection_home">Homepage</label><br/>
							<input id="the_champ_register_redirection_account" name="the_champ_login[register_redirection]" type="radio" <?php echo isset($theChampLoginOptions['register_redirection']) && $theChampLoginOptions['register_redirection'] == 'account' ? 'checked = "checked"' : '';?> value="account" />
							<label for="the_champ_register_redirection_account">Account dashboard</label><br/>
							<input id="the_champ_register_redirection_custom" name="the_champ_login[register_redirection]" type="radio" <?php echo isset($theChampLoginOptions['register_redirection']) && $theChampLoginOptions['register_redirection'] == 'custom' ? 'checked = "checked"' : '';?> value="custom" />
							<label for="the_champ_register_redirection_custom">Custom Url</label><br/>
							<input id="the_champ_register_redirection_url" name="the_champ_login[register_redirection_url]" type="text" value="<?php echo isset($theChampLoginOptions['register_redirection_url']) ? $theChampLoginOptions['register_redirection_url'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_sl_register_redirect_help_cont">
							<td colspan="2">
							<div>
							<?php _e('User will be redirected to the selected page after registration (first Social Login) through Social Login', 'TheChamp') ?>
							</div>
							</td>
						</tr>
					</table>
					</div>
				</div>
				</div>
				
				<?php include 'help.php'; ?>
			</div>
		</div>
		<div class="the_champ_clear"></div>
		<p class="submit">
			<input style="margin-left:8px" type="submit" name="save" class="button button-primary" value="<?php _e("Save Changes", 'TheChamp'); ?>" />
		</p>
	</div>
</form>