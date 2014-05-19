<?php defined('ABSPATH') or die("Cheating........Uh!!"); ?>
<script>
var theChampSharingIconPath = '<?php echo plugins_url('../images/sharing', __FILE__); ?>';
</script>

<div id="fb-root"></div>

<div class="metabox-holder columns-2" id="post-body">
		<form action="options.php" method="post">
		<?php settings_fields('the_champ_sharing_options'); ?>
		<div class="menu_div" id="tabs">
			<h2 class="nav-tab-wrapper" style="height:36px">
			<ul>
				<li style="margin-left:9px"><a style="margin:0; height:23px" class="nav-tab" href="#tabs-1"><?php _e('Provider Selection', 'TheChamp') ?></a></li>
				<li style="margin-left:9px"><a style="margin:0; height:23px" class="nav-tab" href="#tabs-2"><?php _e('Social Sharing', 'TheChamp') ?></a></li>
			</ul>
			</h2>
			<div class="menu_containt_div" id="tabs-1">
				<div class="the_champ_left_column">
				<div class="stuffbox" >
					<h3><label><?php _e('Basic Configuration', 'TheChamp');?></label></h3>
					<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
						<tr>
							<th>
							<img id="the_champ_ss_enable_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_sharing_enable"><?php _e("Enable Social Sharing", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_sharing_enable" name="the_champ_sharing[enable]" type="checkbox" <?php echo isset($theChampSharingOptions['enable']) && $theChampSharingOptions['enable'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_ss_enable_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Master control for Social Sharing. It must be checked to enable Social Sharing functionality', 'TheChamp') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_ss_providers_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label><?php _e("Select providers", 'TheChamp'); ?></label>
							</th>
							<td>
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_facebook" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('facebook', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="facebook" />
							<label for="the_champ_sharing_facebook"><?php _e("Facebook", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_twitter" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('twitter', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="twitter" />
							<label for="the_champ_sharing_twitter"><?php _e("Twitter", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_linkedin" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('linkedin', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="linkedin" />
							<label for="the_champ_sharing_linkedin"><?php _e("LinkedIn", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_google" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('google', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="google" />
							<label for="the_champ_sharing_google"><?php _e("Google+", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_print" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('print', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="print" />
							<label for="the_champ_sharing_print"><?php _e("Print", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_email" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('email', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="email" />
							<label for="the_champ_sharing_email"><?php _e("Email", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_yahoo" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('yahoo', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="yahoo" />
							<label for="the_champ_sharing_yahoo"><?php _e("Yahoo", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_reddit" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('reddit', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="reddit" />
							<label for="the_champ_sharing_reddit"><?php _e("Reddit", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_digg" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('digg', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="digg" />
							<label for="the_champ_sharing_digg"><?php _e("Digg", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_delicious" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('delicious', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="delicious" />
							<label for="the_champ_sharing_delicious"><?php _e("Delicious", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_stumble" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('stumbleupon', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="stumbleupon" />
							<label for="the_champ_sharing_stumble"><?php _e("StumbleUpon", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_float" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('float it', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="float it" />
							<label for="the_champ_sharing_float"><?php _e("Float it", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_tumblr" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('tumblr', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="tumblr" />
							<label for="the_champ_sharing_tumblr"><?php _e("Tumblr", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_vk" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('vkontakte', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="vkontakte" />
							<label for="the_champ_sharing_vk"><?php _e("Vkontakte", 'TheChamp'); ?></label>
							</div>
							
							<div class="theChampSharingProviderContainer">
							<input id="the_champ_sharing_pinterest" name="the_champ_sharing[providers][]" type="checkbox" <?php echo isset($theChampSharingOptions['providers']) && in_array('pinterest', $theChampSharingOptions['providers']) ? 'checked = "checked"' : '';?> value="pinterest" />
							<label for="the_champ_sharing_pinterest"><?php _e("Pinterest", 'TheChamp'); ?></label>
							</div>
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_ss_providers_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Select the providers for sharing interface', 'TheChamp') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_ss_rearrange_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label><?php _e("Rearrange icons", 'TheChamp'); ?></label>
							</th>
							<td>
							<ul id="the_champ_ss_rearrange">
								<?php
								if(isset($theChampSharingOptions['horizontal_re_providers'])){
									foreach($theChampSharingOptions['horizontal_re_providers'] as $rearrange){
										?>
										<li title="<?php echo $rearrange ?>" id="the_champ_re_<?php echo str_replace(' ', '_', $rearrange) ?>" >
										<i class="theChampSharingButton theChampSharing<?php echo str_replace(' ', '', $rearrange) ?>Button"></i>
										<input type="hidden" name="the_champ_sharing[horizontal_re_providers][]" value="<?php echo $rearrange ?>">
										</li>
										<?php
									}
								}
								?>
							</ul>
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_ss_rearrange_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Drag the icons to rearrange these in desired order', 'TheChamp') ?>
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
					<h3><label><?php _e('Sharing options', 'TheChamp');?></label></h3>
					<div class="inside">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="form-table editcomment menu_content_table">
						<tr>
							<th>
							<img id="the_champ_ss_title_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label for="the_champ_fblogin_title"><?php _e("Title", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_fblogin_title" name="the_champ_sharing[title]" type="text" value="<?php echo isset($theChampSharingOptions['title']) ? $theChampSharingOptions['title'] : '' ?>" />
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_ss_title_help_cont">
							<td colspan="2">
							<div>
							<?php _e('The text to display above the sharing interface', 'TheChamp') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_ss_position_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label><?php _e("Position with respect to content", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_sharing_top" name="the_champ_sharing[top]" type="checkbox" <?php echo isset($theChampSharingOptions['top']) && $theChampSharingOptions['top'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							<label for="the_champ_sharing_top">Top of the content</label><br/>
							<input id="the_champ_sharing_bottom" name="the_champ_sharing[bottom]" type="checkbox" <?php echo isset($theChampSharingOptions['bottom']) && $theChampSharingOptions['bottom'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							<label for="the_champ_sharing_bottom">Bottom of the content</label>
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_ss_position_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Specify position of the sharing interface with respect to the content', 'TheChamp') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<th>
							<img id="the_champ_ss_location_help" class="the_champ_help_bubble" src="<?php echo plugins_url('../images/info.png', __FILE__) ?>" />
							<label><?php _e("Sharing location", 'TheChamp'); ?></label>
							</th>
							<td>
							<input id="the_champ_sharing_home" name="the_champ_sharing[home]" type="checkbox" <?php echo isset($theChampSharingOptions['home']) && $theChampSharingOptions['home'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							<label for="the_champ_sharing_home">Homepage</label><br/>
							<input id="the_champ_sharing_post" name="the_champ_sharing[post]" type="checkbox" <?php echo isset($theChampSharingOptions['post']) && $theChampSharingOptions['post'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							<label for="the_champ_sharing_post">Posts</label><br/>
							<input id="the_champ_sharing_page" name="the_champ_sharing[page]" type="checkbox" <?php echo isset($theChampSharingOptions['page']) && $theChampSharingOptions['page'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							<label for="the_champ_sharing_page">Pages</label><br/>
							<input id="the_champ_sharing_excerpt" name="the_champ_sharing[excerpt]" type="checkbox" <?php echo isset($theChampSharingOptions['excerpt']) && $theChampSharingOptions['excerpt'] == 1 ? 'checked = "checked"' : '';?> value="1" />
							<label for="the_champ_sharing_excerpt">Excerpts</label>
							</td>
						</tr>
						
						<tr class="the_champ_help_content" id="the_champ_ss_location_help_cont">
							<td colspan="2">
							<div>
							<?php _e('Specify the pages where you want to enable Sharing interface', 'TheChamp') ?>
							</div>
							</td>
						</tr>
						
						<tr>
							<td colspan="2">
							<div>
							<?php _e('<strong>Note:</strong> To disable sharing on particular page/post, edit that page/post and check the <strong>"Disable Social Sharing on this page"</strong> option at the bottom in <strong>"Super Socializer"</strong> section', 'TheChamp') ?>
							</div>
							</td>
						</tr>
					</table>
					</div>
				</div>
				</div>
				<?php include 'help.php'; ?>
			</div>
		<div class="the_champ_clear"></div>
		<p class="submit">
			<input style="margin-left:8px" type="submit" name="save" class="button button-primary" value="<?php _e("Save Changes", 'TheChamp'); ?>" />
		</p>
		
		</div>
	</form>
</div>