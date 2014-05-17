<?php

/*
  Plugin Name: Header and Footer
  Plugin URI: http://www.satollo.net/plugins/header-footer
  Description: Header and Footer by Stefano Lissa lets to add html/javascript code to the head and footer of your blog. Some examples are provided on the <a href="http://www.satollo.net/plugins/header-footer">official page</a>.
  Version: 1.5.4
  Author: Stefano Lissa
  Author URI: http://www.satollo.net
  Disclaimer: Use at your own risk. No warranty expressed or implied is provided.
 */

/*
  Copyright 2008-2014 Stefano Lissa (stefano@satollo.net)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation; either version 2 of the License, or
  (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

$hefo_options = get_option('hefo');
$hefo_is_mobile = false;
if (isset($_SERVER['HTTP_USER_AGENT']) && isset($hefo_options['mobile_user_agents_parsed'])) {
    $hefo_is_mobile = preg_match('/' . $hefo_options['mobile_user_agents_parsed'] . '/', strtolower($_SERVER['HTTP_USER_AGENT']));
}

if (is_admin()) {
    include dirname(__FILE__) . '/admin.php';
}

add_action('wp_head', 'hefo_wp_head_pre', 1);

function hefo_wp_head_pre() {
    global $hefo_options, $wp_query;

    if (is_home() && is_paged() && isset($hefo_options['seo_home_paged_noindex'])) {
        echo '<meta name="robots" content="noindex">';
    }

    if (is_home() && !is_paged() && isset($hefo_options['seo_home_canonical'])) {
        echo '<meta name="canonical" content="' . get_option('home') . '">';
    }

    if (is_archive() && is_paged() && isset($hefo_options['seo_archives_paged_noindex'])) {
        echo '<meta name="robots" content="noindex">';
    }

    if (is_search() && isset($hefo_options['seo_search_noindex'])) {
        echo '<meta name="robots" content="noindex">';
    }

    if (isset($hefo_options['og_enabled'])) {
        if (is_home()) {
            if (empty($hefo_options['og_type_home']))
                $hefo_options['og_type_home'] = $hefo_options['og_type'];
            if (!empty($hefo_options['og_type_home']))
                echo '<meta property="og:type" content="' . $hefo_options['og_type_home'] . '" />';
        }
        else {
            if (!empty($hefo_options['og_type']))
                echo '<meta property="og:type" content="' . $hefo_options['og_type'] . '" />';
        }

        if (!empty($hefo_options['fb_app_id'])) {
            echo '<meta property="fb:app_id" content="' . $hefo_options['fb_app_id'] . '" />';
        }

        if (!empty($hefo_options['fb_admins'])) {
            echo '<meta property="fb:admins" content="' . $hefo_options['fb_admins'] . '" />';
        }

        // Add it as higer as possible, Facebook reads only the first part of a page
        if (isset($hefo_options['og_image'])) {
            if (is_single() || is_page()) {
                $xid = $wp_query->get_queried_object_id();
                if (function_exists('bbp_get_topic_forum_id')) {
                    $object = $wp_query->get_queried_object();
                    if ($object != null && $object->post_type == 'topic') {
                        $xid = bbp_get_topic_forum_id($xid);
                    }
                }
                $xtid = function_exists('get_post_thumbnail_id') ? get_post_thumbnail_id($xid) : false;
                if ($xtid) {
                    $ximage = wp_get_attachment_url($xtid);
                    echo '<meta property="og:image" content="' . $ximage . '" />';
                } else {
                    $xattachments = get_children(array('post_parent' => $xid, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order'));
                    if (!empty($xattachments)) {
                        foreach ($xattachments as $id => $attachment) {
                            $ximage = wp_get_attachment_url($id);
                            echo '<meta property="og:image" content="' . $ximage . '" />';
                            break;
                        }
                    } else {
                        if (!empty($hefo_options['og_image_default'])) {
                            echo '<meta property="og:image" content="' . $hefo_options['og_image_default'] . '" />';
                        }
                    }
                }
            } else {
                if (!empty($hefo_options['og_image_default'])) {
                    echo '<meta property="og:image" content="' . $hefo_options['og_image_default'] . '" />';
                }
            }
        }
    }
}

add_action('wp_head', 'hefo_wp_head_post', 11);

function hefo_wp_head_post() {
    global $hefo_options, $wp_query, $wpdb;
    $buffer = '';
    if (is_home())
        $buffer .= hefo_replace($hefo_options['head_home']);

    $buffer .= hefo_replace($hefo_options['head']);

    ob_start();
    eval('?>' . $buffer);
    $buffer = ob_get_contents();
    ob_end_clean();
    echo $buffer;
}

add_action('wp_footer', 'hefo_wp_footer');

function hefo_wp_footer() {
    global $hefo_options;

    $buffer = hefo_replace($hefo_options['footer']);

    ob_start();
    eval('?>' . $buffer);
    $buffer = ob_get_contents();
    ob_end_clean();
    echo $buffer;

    echo '<script>function hefo_popup(url, width, height) {
var left = Math.round(screen.width/2-width/2); var top = 0;
if (screen.height > height) top = Math.round(screen.height/2-height/2);
window.open(url, "share", "scrollbars=yes,resizable=yes,toolbar=no,location=yes,width=" + width + ",height=" + height + ",left=" + left + ",top=" + top);
return false;
}</script>';
}

// BBPRESS
$bbp_reply_count = 0;

add_action('bbp_theme_before_reply_content', 'hefo_bbp_theme_before_reply_content');

function hefo_bbp_theme_before_reply_content() {
    global $hefo_options, $wpdb, $post, $bbp_reply_count;
    $bbp_reply_count++;
    echo hefo_execute(hefo_replace($hefo_options['bbp_theme_before_reply_content']));
}

add_action('bbp_theme_after_reply_content', 'hefo_bbp_theme_after_reply_content');

function hefo_bbp_theme_after_reply_content() {
    global $hefo_options, $wpdb, $post, $bbp_reply_count;

    echo hefo_execute(hefo_replace($hefo_options['bbp_theme_after_reply_content']));
}

add_action('bbp_template_before_single_forum', 'hefo_bbp_template_before_single_forum');

function hefo_bbp_template_before_single_forum() {
    global $hefo_options, $wpdb, $post;

    echo hefo_execute(hefo_replace($hefo_options['bbp_template_before_single_forum']));
}

add_action('bbp_template_before_single_topic', 'hefo_bbp_template_before_single_topic');

function hefo_bbp_template_before_single_topic() {
    global $hefo_options, $wpdb, $post;

    echo hefo_execute(hefo_replace($hefo_options['bbp_template_before_single_topic']));
}

add_action('the_content', 'hefo_the_content');

global $hefo_page_top, $hefo_page_bottom, $hefo_post_top, $hefo_post_bottom;
$hefo_page_top = true;
$hefo_page_bottom = true;
$hefo_post_top = true;
$hefo_post_bottom = true;

function hefo_the_content($content) {
    global $hefo_options, $wpdb, $post, $hefo_page_top, $hefo_page_bottom, $hefo_post_top, $hefo_post_bottom, $hefo_is_mobile;

    //if (is_singular() || ($hefo_options['category'] && (is_category() || is_tag()))) {
    if (is_singular()) {
        if (is_page()) {
            if ($hefo_page_top) {
                $value = get_post_meta($post->ID, 'hefo_before', true);
                if ($value != '1') {
                    if (isset($hefo_options['mobile_page']) && $hefo_is_mobile) {
                        $before = hefo_execute(hefo_replace($hefo_options['mobile_page_before']));
                    } else {
                        $before = hefo_execute(hefo_replace($hefo_options['page_before']));
                    }
                }
            }
            if ($hefo_page_bottom) {
                $value = get_post_meta($post->ID, 'hefo_after', true);
                if ($value != '1') {
                    if (isset($hefo_options['mobile_page']) && $hefo_is_mobile) {
                        $after = hefo_execute(hefo_replace($hefo_options['mobile_page_after']));
                    } else {
                        $after = hefo_execute(hefo_replace($hefo_options['page_after']));
                    }
                }
            }
        } else {
            if ($hefo_post_top) {
                $value = get_post_meta($post->ID, 'hefo_before', true);
                if ($value != '1') {
                    if (isset($hefo_options['mobile_post']) && $hefo_is_mobile) {
                        $before = hefo_execute(hefo_replace($hefo_options['mobile_before']));
                    } else {
                        $before = hefo_execute(hefo_replace($hefo_options['before']));
                    }
                }
            }
            if ($hefo_post_bottom) {
                $value = get_post_meta($post->ID, 'hefo_after', true);
                if ($value != '1') {
                    if (isset($hefo_options['mobile_post']) && $hefo_is_mobile) {
                        $after = hefo_execute(hefo_replace($hefo_options['mobile_after']));
                    } else {
                        $after = hefo_execute(hefo_replace($hefo_options['after']));
                    }
                }
            }
        }
        return $before . $content . $after;
    } else {
        return $content;
    }
}

add_action('the_excerpt', 'hefo_the_excerpt');
global $hefo_count;
$hefo_count = 0;

function hefo_the_excerpt($content) {
    global $hefo_options, $post, $wpdb, $hefo_count;
    $hefo_count++;
    if (is_category() || is_tag()) {
        $before = hefo_execute(hefo_replace($hefo_options['excerpt_before']));
        $after = hefo_execute(hefo_replace($hefo_options['excerpt_after']));

        return $before . $content . $after;
    } else {
        return $content;
    }
}

function hefo_replace($buffer) {
    global $hefo_options, $post;

    if (empty($buffer))
        return '';
    for ($i = 1; $i <= 5; $i++) {
        $buffer = str_replace('[snippet_' . $i . ']', $hefo_options['snippet_' . $i], $buffer);
    }
    $images_url = plugins_url('images', 'header-footer/plugin.php');
    $permalink = urlencode(get_permalink());
    $title = urlencode($post->post_title);
    $buffer = str_replace('[images_url]', $images_url, $buffer);

    $facebook_url = 'https://www.facebook.com/sharer/sharer.php?u=' . $permalink;
    $buffer = str_replace('[facebook_share_url]', $facebook_url, $buffer);

    // Twitter
    $twitter_url = 'http://twitter.com/intent/tweet?text=' . $title;
    $twitter_url .= '&url=' . $permalink;
    $buffer = str_replace('[twitter_share_url]', $twitter_url, $buffer);

    // Google
    $google_url = 'https://plus.google.com/share?url=' . $permalink;
    $buffer = str_replace('[google_share_url]', $google_url, $buffer);

    // Pinterest
    $pinterest_url = 'http://www.pinterest.com/pin/create/button/?url=' . $permalink;
    $pinterest_url .= '&media=' . urlencode(hefo_post_image());
    $pinterest_url .= '&description=' . $title;
    $buffer = str_replace('[pinterest_share_url]', $pinterest_url, $buffer);

    $linkedin_url = 'http://www.linkedin.com/shareArticle?mini=true&url=' . $permalink;
    $linkedin_url .= '&title=' . $title . '&source=' . urlencode(get_option('blogname'));
    $buffer = str_replace('[linkedin_share_url]', $linkedin_url, $buffer);

    return $buffer;
}

function hefo_execute($buffer) {
    global $post;
    if (empty($buffer))
        return '';
    ob_start();
    eval('?>' . $buffer);
    $buffer = ob_get_clean();
    return $buffer;
}

if (isset($hefo_options['sticky_enabled'])) {
    add_action('wp_enqueue_scripts', 'hefo_wp_enqueue_scripts');

    function hefo_wp_enqueue_scripts() {
        wp_enqueue_script('sticky', plugins_url('js/jquery.sticky.js', 'header-footer/plugin.php'), array('jquery'), false, true);
    }

}

function hefo_post_image($size = 'large', $alternative = null) {
    global $post;

    if (empty($post))
        return $alternative;
    $post_id = $post->ID;
    $image_id = function_exists('get_post_thumbnail_id') ? get_post_thumbnail_id($post_id) : false;
    if ($image_id) {
        $image = wp_get_attachment_image_src($image_id, $size);
        return $image[0];
    } else {
        $attachments = get_children(array('post_parent' => $post_id, 'post_status' => 'inherit', 'post_type' => 'attachment', 'post_mime_type' => 'image', 'order' => 'ASC', 'orderby' => 'menu_order ID'));

        if (empty($attachments)) {
            return $alternative;
        }

        foreach ($attachments as $id => &$attachment) {
            $image = wp_get_attachment_image_src($id, $size);
            return $image[0];
        }
    }
}
