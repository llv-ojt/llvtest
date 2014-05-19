<?php
defined('ABSPATH') or die("Cheating........Uh!!");
/**
 * File contains the functions necessary for Social Sharing functionality
 */

/**
 * Render sharing interface html.
 */
function the_champ_prepare_sharing_html($postUrl){
	global $theChampSharingOptions, $post;
	if(isset($theChampSharingOptions['horizontal_re_providers'])){
		$html = '<ul class="the_champ_sharing_ul">';
		foreach($theChampSharingOptions['horizontal_re_providers'] as $provider){
			$html .= '<li><span class="the_champ_share_count the_champ_'.$provider.'_count">&nbsp;</span>';
			if($provider == 'print'){
				$html .= '<i alt="Print" Title="Print" class="theChampSharingButton theChampSharing'. ucfirst($provider) .'Button" onclick=\'window.print()\'></i>';
			}elseif($provider == 'email'){
				$html .= '<i alt="Email" Title="Email" class="theChampSharingButton theChampSharing'. ucfirst($provider) .'Button" onclick="window.location.href = \'mailto:?subject=\' + escape(\'Have a look at this website\') + \'&body=\' + escape(\''.$postUrl.'\')"></i>';
			}else{
				if($provider == 'facebook'){
					$sharingUrl = 'https://www.facebook.com/sharer/sharer.php?u=' . $postUrl;
				}elseif($provider == 'twitter'){
					$sharingUrl = 'https://twitter.com/share?url=' . $postUrl;
				}elseif($provider == 'linkedin'){
					$sharingUrl = 'http://www.linkedin.com/shareArticle?mini=true&url=' . $postUrl;
				}elseif($provider == 'google'){
					$sharingUrl = 'https://plus.google.com/share?url=' . $postUrl;
				}elseif($provider == 'yahoo'){
					$sharingUrl = 'http://bookmarks.yahoo.com/toolbar/SaveBM/?u=' . $postUrl . '&t=' . urlencode($post->post_title);
				}elseif($provider == 'reddit'){
					$sharingUrl = 'http://reddit.com/submit?url='.$postUrl.'&title=' . urlencode($post->post_title);
				}elseif($provider == 'digg'){
					$sharingUrl = 'http://digg.com/submit?url='.$postUrl.'&title=' . urlencode($post->post_title);
				}elseif($provider == 'delicious'){
					$sharingUrl = 'http://del.icio.us/post?url='.$postUrl.'&title=' . urlencode($post->post_title);
				}elseif($provider == 'stumbleupon'){
					$sharingUrl = 'http://www.stumbleupon.com/submit?url='.$postUrl.'&title=' . urlencode($post->post_title);
				}elseif($provider == 'float it'){
					$sharingUrl = 'http://www.designfloat.com/submit.php?url='.$postUrl.'&title=' . urlencode($post->post_title);
				}elseif($provider == 'tumblr'){
					$sharingUrl = 'http://www.tumblr.com/share?v=3&u='.urlencode($postUrl).'&t=' . urlencode($post->post_title) . '&s=';
				}elseif($provider == 'vkontakte'){
					$sharingUrl = 'http://vkontakte.ru/share.php?&url='.urlencode($postUrl);
				}elseif($provider == 'pinterest'){
					$sharingUrl = "javascript:void((function(){var e=document.createElement('script');e.setAttribute('type','text/javascript');e.setAttribute('charset','UTF-8');e.setAttribute('src','//assets.pinterest.com/js/pinmarklet.js?r='+Math.random()*99999999);document.body.appendChild(e)})());";
				}
				$html .= '<i alt="'.($provider == 'google' ? 'Google Plus' : ucfirst($provider)).'" Title="'.($provider == 'google' ? 'Google Plus' : ucfirst($provider)).'" class="theChampSharingButton theChampSharing'. ucfirst( str_replace(' ', '', $provider) ) .'Button" ';
				if($provider == 'pinterest'){
					$html .= 'onclick="'.$sharingUrl.'"></i>';
				}else{
					$html .= 'onclick=\' theChampPopup("'.$sharingUrl.'")\'></i>';
				}
			}
			$html .= '</li>';
		}
		$html .= '<li><span class="the_champ_share_count">&nbsp;</span><i title="More" alt="More" class="theChampSharingButton theChampSharingMoreButton" onclick="theChampMoreSharingPopup(this, \''.$postUrl.'\', \''.$post->post_title.'\')" ></i></li></ul><div style="clear:both"></div>';
	}
	return $html;
}

/**
 * Enable sharing interface at selected areas.
 */
function the_champ_render_sharing($content){
	global $post;
	$sharingMeta = get_post_meta($post->ID, '_the_champ_meta', true);
	// if sharing is disabled on this page/post, return content unaltered
	if(isset($sharingMeta['sharing']) && $sharingMeta['sharing'] == 1 && !is_front_page()){
		return $content;
	}
	global $theChampSharingOptions;
	$postUrl = get_permalink($post->ID);
	$sharingDiv = the_champ_prepare_sharing_html($postUrl);
	$horizontalDiv = "<div class='the_champ_sharing_container' champ-data-href='".$postUrl."'><div style='font-weight:bold'>".ucfirst($theChampSharingOptions['title'])."</div>".$sharingDiv."</div>";
	// show horizontal sharing		
	if((isset( $theChampSharingOptions['home']) && is_front_page()) || ( isset( $theChampSharingOptions['post'] ) && is_single() ) || ( isset( $theChampSharingOptions['page'] ) && is_page() ) || ( isset( $theChampSharingOptions['excerpt'] ) && is_front_page() && current_filter() == 'get_the_excerpt' )){	
		if(isset($theChampSharingOptions['top'] ) && isset($theChampSharingOptions['bottom'])){
			$content = $horizontalDiv.'<br/>'.$content.'<br/>'.$horizontalDiv;
		}else{
			if(isset($theChampSharingOptions['top'])){
				$content = $horizontalDiv.$content;
			}
			elseif(isset($theChampSharingOptions['bottom'])){
				$content = $content.$horizontalDiv;
			}
		}
	}
	return $content;
}
add_filter('the_content', 'the_champ_render_sharing');
add_filter('get_the_excerpt', 'the_champ_render_sharing');

/**
 * Get sharing count for providers
 */
function the_champ_sharing_count(){
	if(isset($_GET['urls']) && count($_GET['urls']) > 0){
		$targetUrls = array_unique($_GET['urls']);
	}else{
		the_champ_ajax_response(0, __('Invalid request'));
	}
	global $theChampSharingOptions;
	// no providers selected
	if(!isset($theChampSharingOptions['providers']) || count($theChampSharingOptions['providers']) == 0){
		the_champ_ajax_response(0, __('Providers not selected'));
	}
	$responseData = array();
	foreach($targetUrls as $targetUrl){
		foreach($theChampSharingOptions['providers'] as $provider){
			switch($provider){
				case 'facebook':
					$url = 'http://graph.facebook.com/?id=' . $targetUrl;
					break;
				case 'twitter':
					$url = 'http://urls.api.twitter.com/1/urls/count.json?url=' . $targetUrl;
					break;
				case 'linkedin':
					$url = 'http://www.linkedin.com/countserv/count/share?url='. $targetUrl .'&format=json';
					break;
				case 'reddit':
					$url = 'http://www.reddit.com/api/info.json?url='. $targetUrl;
					break;
				case 'delicious':
					$url = 'http://feeds.delicious.com/v2/json/urlinfo/data?url='. $targetUrl;
					break;
				case 'pinterest':
					$url = 'http://api.pinterest.com/v1/urls/count.json?callback=theChamp&url='. $targetUrl;
					break;
				case 'stumbleupon':
					$url = 'http://www.stumbleupon.com/services/1.01/badge.getinfo?url='. $targetUrl;
					break;
				default:
					$url = '';
			}
			if($url == '') { continue; }
			$response = wp_remote_get( $url,  array( 'timeout' => 15 ) );
			if( ! is_wp_error( $response ) && isset( $response['response']['code'] ) && 200 === $response['response']['code'] ){
				$body = wp_remote_retrieve_body( $response );
				if($provider == 'pinterest'){
					$body = str_replace(array('theChamp(', ')'), '', $body);
				}
				$body = json_decode($body);
				switch($provider){
					case 'facebook':
						if(!empty($body -> shares)){
							$responseData[$targetUrl]['facebook'] = $body -> shares;
						}
						break;
					case 'twitter':
						if(!empty($body -> count)){
							$responseData[$targetUrl]['twitter'] = $body -> count;
						}
						break;
					case 'linkedin':
						if(!empty($body -> count)){
							$responseData[$targetUrl]['linkedin'] = $body -> count;
						}
						break;
					case 'reddit':
						if(!empty($body -> data -> children)){
							$children = $body -> data -> children;
							if(!empty($children[0] -> data -> score)){
								$responseData[$targetUrl]['reddit'] = $children[0] -> data -> score;
							}
						}
						break;
					case 'delicious':
						if(!empty($body[0] -> total_posts)){
							$responseData[$targetUrl]['delicious'] = $body[0] -> total_posts;
						}
						break;
					case 'pinterest':
						if(!empty($body -> count)){
							$responseData[$targetUrl]['pinterest'] = $body -> count;
						}
						break;
					case 'stumbleupon':
						if(!empty($body -> result) && isset( $body -> result -> views )){
							$responseData[$targetUrl]['stumbleupon'] = $body -> result -> views;
						}
						break;
				}
			}
		}
	}
	the_champ_ajax_response(1, $responseData);
}

add_action('wp_ajax_the_champ_sharing_count', 'the_champ_sharing_count');
add_action('wp_ajax_nopriv_the_champ_sharing_count', 'the_champ_sharing_count');