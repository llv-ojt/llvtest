<?php
defined('ABSPATH') or die("Cheating........Uh!!");
/** 
 * Shortcode for Social Sharing.
 */ 
function the_champ_sharing_shortcode($params){
	// notify if sharing is disabled
	if(the_champ_social_sharing_enabled()){
		global $post;
		$targetUrl = get_permalink($post -> ID);
		extract(shortcode_atts(array(
			'style' => ''
		), $params));
		$html = '<div class="the_champ_sharing_container" champ-data-href="'.$targetUrl.'" ';
		// style 
		if($style != ""){
			$html .= 'style="'.$style.'"';
		}
		$html .= '>';
		$html .= the_champ_prepare_sharing_html($targetUrl);
		$html .= '</div>';
		return $html;
	}
}
add_shortcode('TheChamp-Sharing', 'the_champ_sharing_shortcode');

/** 
 * Shortcode for Social Login.
 */ 
function the_champ_login_shortcode($params){
	if(the_champ_social_login_enabled()){
		extract(shortcode_atts(array(
			'style' => ''
		), $params));
		$html = '<div ';
		// style 
		if($style != ""){
			if(strpos($style, 'float') === false){
				$style = 'float: left;' . $style;
			}
			$html .= 'style="'.$style.'"';
		}
		$html .= '>';
		$html .= the_champ_login_button(true);
		$html .= '</div><div style="clear:both"></div>';
		return $html;
	}
}
add_shortcode('TheChamp-Login', 'the_champ_login_shortcode');